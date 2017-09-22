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
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var OutputInterface
     */
    protected $outputInterface;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $secretApiKey;

    /**
     * @var string
     */
    protected $publicApiKey;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param array $filters
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        $filters = []
    ) {
        $this->filters = $filters;
        $this->jsonDecoder = $jsonDecoder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->outputInterface = $output;
    }

    protected function outputMsg($message)
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
            // TODO: Throw invalid API key exception if decode fails
            $key = $this->jsonDecoder->decode($key);
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
            // TODO: Throw invalid API key exception if decode fails
            $key = $this->jsonDecoder->decode($key);
            $this->secretApiKey = $key;
        }
        return $this->secretApiKey;
    }

}
