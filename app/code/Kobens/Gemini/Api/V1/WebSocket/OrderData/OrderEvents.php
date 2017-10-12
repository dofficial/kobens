<?php

// TODO: Abstract the private order book aspect of this into Kobens_Core.

namespace Kobens\Gemini\Api\V1\WebSocket\OrderData;

use Symfony\Component\Console\Output\OutputInterface;

class OrderEvents
{
    // TODO: Stuff about this: https://docs.gemini.com/websocket-api/?shell#symbols-and-minimums

    const XML_PATH_TRADE_PUBLIC = 'gemini/api_keys/trade_public';
    const XML_PATH_TRADE_SECRET = 'gemini/api_keys/trade_secret';

    const WEBSOCKET_URL = 'wss://api.gemini.com/v1/order/events';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptorInterface;

    /**
     * @var OutputInterface
     */
    protected $outputInterface;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var \Kobens\Gemini\Helper\Nonce
     */
    protected $nonce;

    /**
     * @var string
     */
    protected $secretApiKey;

    /**
     * @var int
     */
    protected $socketSequence;

    /**
     * @var string
     */
    protected $publicApiKey;

    /**
     * @var string
     */
    protected $websocketUrl;

    /**
     * @var \Kobens\Gemini\Model\Cache
     */
    protected $cache;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param array $filters
     */
    public function __construct(
        \Kobens\Gemini\Model\Cache $cache,
        \Kobens\Gemini\Helper\Nonce $nonce,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        $filters = []
    ) {
        $this->cache = $cache;
        $this->encryptorInterface = $encryptorInterface;
        $this->filters = $filters;
        $this->nonce = $nonce;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Stores an order book in cache.
     */
    public function openOrderBook()
    {
        $loop = \React\EventLoop\Factory::create();
        $connector = new \Ratchet\Client\Connector($loop);
        $connector($this->getWebSocketUrl(), [], $this->getHeaders())->then(
            function (\Ratchet\Client\WebSocket $conn) {
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
                    $msg = json_decode($msg);
                    if ($msg instanceOf \stdClass) {
                        $this->processMessage($msg);
                    } elseif (is_array($msg)) {
                        $this->populateBook($msg);
                    } else {
                        throw new \Kobens\Core\Exception\Exception('Unknown Websocket Message');
                    }
                });

                $conn->on('close', function($code = null, $reason = null) {
                    $this->outputMsg("Connection Closed.\n");
                });
            },
            function (\Exception $e) use ($loop) {
                $loop->stop();
                $this->outputMsg("\nException Thrown: {$e->getMessage()}\n");
                \Zend_Debug::dump($e->getTraceAsString());
            }
        );
        $loop->run();
    }

    /**
     * @param array $orders
     */
    protected function populateBook(array $orders)
    {
        if ($orders) foreach ($orders as $order) {
            if ($order->type !== 'initial') {
                throw new \Kobens\Core\Exception\Exception('Order book can only be populated with "initial" message types.');
            }
            \Zend_Debug::dump($order);
            exit;
        }
    }

    /**
     * Process a message from the websocket stream.
     *
     * @param \stdClass $msg
     */
    protected function processMessage(\stdClass $msg)
    {
        if ($msg->type == 'subscription_ack') {
            $this->resetSocketSequence();
            // TODO: do we want to do anything else here?
            return;
        }
        $this->setSocketSequence($msg->socket_sequence);

    }

    protected function setSocketSequence($socketSequence)
    {
        switch (true) {
            case $socketSequence == 0:
                if ($this->socketSequence !== null) {
                    throw new \Kobens\Gemini\Exception\SocketSequenceException();
                }
                $this->socketSequence = 0;
                break;
            default:
                if ($this->socketSequence != $socketSequence - 1) {
                    throw new \Kobens\Gemini\Exception\SocketSequenceException();
                }
                $this->socketSequence = $socketSequence;
                break;
        }
    }

    /**
     * Resets the socket sequence to null
     */
    protected function resetSocketSequence()
    {
        $this->socketSequence = null;
    }

    /**
     * @return string
     */
    protected function getWebSocketUrl()
    {
        if (!$this->websocketUrl) {
            $url = self::WEBSOCKET_URL;
            // TODO: apply filters
            $this->websocketUrl = $url;
        }
        return $this->websocketUrl;
    }

    /**
     * @return string[]
     */
    protected function getHeaders()
    {
        $payload = $this->getPayload();
        $headers = [
            'X-GEMINI-APIKEY' => $this->getPublicApiKey(),
            'X-GEMINI-PAYLOAD' => $payload,
            'X-GEMINI-SIGNATURE' => $this->getSignature($payload)
        ];
        return $headers;
    }

    /**
     * @return string
     */
    protected function getPayload()
    {
        return base64_encode(json_encode([
            'request' => '/v1/order/events',
            'nonce' => $this->nonce->getNonce()
        ]));
    }

    /**
     * @param string $payload       base64 encoded array
     * @return string
     */
    protected function getSignature($payload)
    {
        return hash_hmac('sha384', $payload, $this->getSecretApiKey());
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->outputInterface = $output;
        return $this;
    }

    /**
     * @param string $message
     */
    public function outputMsg($message)
    {
        if ($this->outputInterface) {
            $this->outputInterface->write($message);
        }
    }

    /**
     * @throws \Kobens\Core\Exception\NoApiKeyException
     * @return string
     */
    protected function getPublicApiKey()
    {
        if (!$this->publicApiKey) {
            $key = $this->scopeConfig->getValue(self::XML_PATH_TRADE_PUBLIC);
            if (!$key) {
                throw new \Kobens\Core\Exception\NoApiKeyException('Gemini Trader Public API Key not set.');
            }
            $key = $this->encryptorInterface->decrypt($key);
            $this->publicApiKey = $key;
        }
        return $this->publicApiKey;
    }

    /**
     * @throws \Kobens\Core\Exception\NoApiKeyException
     * @return string
     */
    protected function getSecretApiKey()
    {
        if (!$this->secretApiKey) {
            $key = $this->scopeConfig->getValue(self::XML_PATH_TRADE_SECRET);
            if (!$key) {
                throw new \Kobens\Core\Exception\NoApiKeyException('Gemini Trader Secret API Key not set.');
            }
            $this->secretApiKey = $this->encryptorInterface->decrypt($key);
        }
        return $this->secretApiKey;
    }

}
