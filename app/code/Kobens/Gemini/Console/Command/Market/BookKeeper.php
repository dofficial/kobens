<?php

namespace Kobens\Gemini\Console\Command\Market;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookKeeper extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Kobens\Gemini\Api\V1\WebSocket\MarketData\BTC\USD
     */
    protected $btcUsd;

    public function __construct(
        \Kobens\Gemini\Api\V1\WebSocket\MarketData\BTC\USD\Proxy $btcUsd,
        $name = 'kobens:gemini:market:book-keeper'
    ) {
        parent::__construct($name);
        $this->btcUsd = $btcUsd;
    }

    protected function configure()
    {
        parent::configure();
        $this->setDescription('Opens a Gemini Market Order Book');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->btcUsd->openBook();
    }

}
