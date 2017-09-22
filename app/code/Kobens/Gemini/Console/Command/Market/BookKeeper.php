<?php

namespace Kobens\Gemini\Console\Command\Market;

use Kobens\Core\Model\Exchange\Pair\BTC\USD as BTCUSD;
use Kobens\Core\Model\Exchange\Pair\ETH\USD as ETHUSD;
use Kobens\Core\Model\Exchange\Pair\ETH\BTC as ETHBTC;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookKeeper extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Kobens\Gemini\Api\V1\WebSocket\MarketData\BTC\USD
     */
    protected $btcUsd;

    /**
     * @var \Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH\BTC
     */
    protected $ethBtc;

    /**
     * @var \Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH\USD
     */
    protected $ethUsd;

    /**
     * @param \Kobens\Gemini\Api\V1\WebSocket\MarketData\BTC\USD\Proxy $btcUsd
     * @param \Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH\USD\Proxy $ethUsd
     * @param \Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH\BTC\Proxy $ethBtc
     * @param string $name
     */
    public function __construct(
        \Kobens\Gemini\Api\V1\WebSocket\MarketData\BTC\USD\Proxy $btcUsd,
        \Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH\USD\Proxy $ethUsd,
        \Kobens\Gemini\Api\V1\WebSocket\MarketData\ETH\BTC\Proxy $ethBtc,
        $name = 'kobens:gemini:market:book-keeper'
    ) {
        parent::__construct($name);
        $this->btcUsd = $btcUsd;
        $this->ethBtc = $ethBtc;
        $this->ethUsd = $ethUsd;
    }

    protected function configure()
    {
        parent::configure();
        $this->setDescription('Opens a Gemini Market Order Book');

        $this->addArgument(
            'pair',
            \Symfony\Component\Console\Input\InputArgument::OPTIONAL,
            'What currency pair\'s book to open.',
            BTCUSD::PAIR
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        switch ($input->getArgument('pair')) {
            case BTCUSD::PAIR:
                $this->btcUsd->openBook();
                break;

            case ETHUSD::PAIR:
                $this->ethUsd->openBook();
                break;

            case ETHBTC::PAIR:
                $this->ethBtc->openBook();
                break;

            default:
                throw new \Kobens\Core\Exception\UnknownPairException(__(
                    'Unkown pair on the Gemini exchange: %1',
                    $input->getArgument('pair')
                ));
                break;
        }
    }

}
