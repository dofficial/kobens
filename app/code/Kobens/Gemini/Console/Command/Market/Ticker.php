<?php

namespace Kobens\Gemini\Console\Command\Market;

use Kobens\Core\Helper\Console\Command\StdOut\Format;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Ticker extends \Symfony\Component\Console\Command\Command
{
    const STDOUT_PREFIX = " "; // " " | "\t"
    const STDOUT_COLUMN_SEPARATOR = "  "; // " " | "\t"

    protected $columns = [
        'Lowest Ask',
        'Highest Bid',
        'Spread',
        'Last Trade',
        "          BTC",
//         "     USD",
//         "Time    ",
//         "Heartbeat"
    ];

    /**
     * @var int
     */
    protected $socketSequence = 0;

    /**
     * @var \Kobens\Gemini\Model\Exchange\Book\BTC\USD
     */
    protected $btcUsd;

    public function __construct(
        \Kobens\Gemini\Model\Exchange\Book\BTC\USD\Proxy $btcUsd,
        $name = 'kobens:gemini:market:ticker'
    ) {
        parent::__construct($name);
        $this->btcUsd = $btcUsd;
    }

    protected function configure()
    {
        parent::configure();
        $this->setDescription('Gemini Order Book Ticker');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startTicker($output);
    }

    protected function startTicker(OutputInterface $output)
    {
        while (true) {
            try {
                $output->writeln($this->getHeaders());
                while (true) {
                    echo
                        self::STDOUT_PREFIX,
                        implode(
                            self::STDOUT_COLUMN_SEPARATOR,
                            $this->getCurrentState()
                        ),
                        "\r"
                    ;
                    usleep(500000); // 500 milliseconds
                }
            } catch (\Kobens\Core\Exception\ClosedBookException $e) {
                $bookIsClosed = true;
                $output->writeln('Book is closed, waiting for book to be opened...');
                while ($bookIsClosed) {
                    try {
                        $this->getCurrentState();
                        $bookIsClosed = false;
                    } catch (\Kobens\Core\Exception\ClosedBookException $e) {
                        $output->writeln('Book is closed... waiting 5 secods before re-attempting.');
                        sleep(1);
                    }
                }
            }
        }

    }


    protected function getHeaders()
    {
        $headers = self::STDOUT_PREFIX;
        for ($i = 0, $j = count($this->columns); $i < $j; $i++) {
            if ($i <> 0) {
                $headers .= self::STDOUT_COLUMN_SEPARATOR;
            }
            $headers .= Format::underline($this->columns[$i]);
        }
        return $headers;
    }

    protected function getCurrentState()
    {
        $askPrice = $this->btcUsd->getAskPrice();
        $bidPrice = $this->btcUsd->getBidPrice();
        $spread = $this->btcUsd->getSpread();
        $lastTrade = $this->btcUsd->getLastTrade();

        $columns = [
            $askPrice,
            $bidPrice,
            $spread,
            !$lastTrade ? '' : $lastTrade->getPrice(),
            !$lastTrade ? '' : $lastTrade->getQuantity(),
//             round($lastTrade['amount'] * $lastTrade['price'], 2, PHP_ROUND_HALF_UP),
//             $lastTrade['time'] ? date('H:i:s', $lastTrade['time']) : '',
//             microtime(true)
        ];
        for ($i = 0, $j = count($this->columns); $i < $j; $i++) {
            while (strlen($columns[$i]) < strlen($this->columns[$i])) {
                $columns[$i] = ' '.$columns[$i];
            }
        }
        $columns[0] = Format::red($columns[0]);
        $columns[1] = Format::green($columns[1]);
        if ($lastTrade) {
            $columns[3] = $lastTrade->getMakerSide() == 'bid'
                ? Format::red($columns[3])
                : Format::green($columns[3]);
        }

        return $columns;
    }
}
