<?php

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
        $this->encryptorInterface = $encryptorInterface;
        $this->filters = $filters;
        $this->nonce = $nonce;
        $this->scopeConfig = $scopeConfig;
    }

    public function openOrderBook()
    {
        $this->socketSequence = 0;
        $loop = \React\EventLoop\Factory::create();
        $connector = new \Ratchet\Client\Connector($loop);

        $connector($this->getWebSocketUrl(), [], $this->getHeaders())->then(
            function (\Ratchet\Client\WebSocket $conn) {
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
                    $msg = json_decode($msg);
                    // Just trying to get first message
                    \Zend_Debug::dump($msg);
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

    protected function getPayload()
    {
        return base64_encode(json_encode([
            'request' => '/v1/order/events',
            'nonce' => $this->nonce->getNonce()
        ]));
    }

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
