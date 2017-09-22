<?php

namespace Kobens\Gemini\Console\Command\Market;

use Kobens\Core\Model\Exchange\Pair\BTC\USD as BTCUSD;
use Kobens\Core\Model\Exchange\Pair\ETH\USD as ETHUSD;
use Kobens\Core\Model\Exchange\Pair\ETH\BTC as ETHBTC;
use Kobens\Core\Helper\Console\Command\StdOut\Format;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @category    \Kobens
 * @package     \Kobens\Gemini
 */
class Ticker extends \Symfony\Component\Console\Command\Command
{
    const STDOUT_PREFIX = " "; // " " | "\t"
    const STDOUT_COLUMN_SEPARATOR = "  "; // " " | "\t"

    const ARGUMENT_REFRESH = 'refresh_rate';
    const ARGUMENT_PAIR = 'pair';

    protected $columns;

    /**
     * Refresh rate (in microseconds)
     *
     * @var integer
     */
    protected $refreshRate = 500000;

    /**
     * @var int
     */
    protected $socketSequence = 0;

    /**
     * @var \Kobens\Gemini\Model\Exchange\Book\BTC\USD
     */
    protected $btcUsd;

    /**
     * @var \Kobens\Gemini\Model\Exchange\Book\ETH\USD
     */
    protected $ethUsd;

    /**
     * @var \Kobens\Gemini\Model\Exchange\Book\ETH\BTC
     */
    protected $ethBtc;

    /**
     * The book that ultimately gets used based off input parameters.
     *
     * @var \Kobens\Core\Model\Exchange\Book\BookInterface
     */
    protected $book;

    /**
     * State of the book last time it was output to screen
     *
     * @var array
     */
    protected $lastState = [];

    public function __construct(
        \Kobens\Gemini\Model\Exchange\Book\BTC\USD\Proxy $btcUsd,
        \Kobens\Gemini\Model\Exchange\Book\ETH\USD\Proxy $ethUsd,
        \Kobens\Gemini\Model\Exchange\Book\ETH\BTC\Proxy $ethBtc,
        $name = 'kobens:gemini:market:ticker'
    ) {
        $this->btcUsd = $btcUsd;
        $this->ethBtc = $ethBtc;
        $this->ethUsd = $ethUsd;
        parent::__construct($name);
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Gemini Order Book Ticker')
            ->addArgument(
                self::ARGUMENT_PAIR,
                InputArgument::OPTIONAL,
                'What currency pair to watch.',
                BTCUSD::PAIR
            )
            ->addArgument(
                self::ARGUMENT_REFRESH,
                InputArgument::OPTIONAL,
                'Refresh rate (in milleseconds) to ping the cache for updates.',
                ($this->refreshRate / 1000)
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Initialize what book to watch
        try {
            switch ($input->getArgument(self::ARGUMENT_PAIR)) {
                case BTCUSD::PAIR:
                    $this->book = $this->btcUsd;
                    break;

                case ETHUSD::PAIR:
                    $this->book = $this->ethUsd;
                    break;

                case ETHBTC::PAIR:
                    $this->book = $this->ethBtc;
                    break;

                default:
                    throw new \Kobens\Core\Exception\UnknownPairException();
                    break;
            }
        } catch (\Kobens\Core\Exception\UnknownPairException $e) {
            $output->writeln(sprintf(
                'Currency pair "%s" not found on this exchange.',
                $input->getArgument(self::ARGUMENT_PAIR)
            ));
            return;
        }

        // Setup ticker's refresh rate
        $refreshRate = intval($input->getArgument(self::ARGUMENT_REFRESH));
        if ($refreshRate < 100) {
            $output->writeln(__(
                'Refresh rates shorter than 100 milliseconds are not recommended. Reverting to default refresh rate'
            ));
        } else {
            $this->refreshRate = $refreshRate * 1000;
        }

        // Setup header columns based off currency pairs
        $base = $this->book->getBaseCurrency();
        $quote = $this->book->getQuoteCurrency();

        $columns = [
            'Ask ('.$quote->getMainUnitAbbreviation().')',
            'Bid ('.$quote->getMainUnitAbbreviation().')',
            'Spread ('.$quote->getMainUnitAbbreviation().')',
            'Last Trade ('.$quote->getMainUnitAbbreviation().')',
            'Amount ('.$base->getMainUnitAbbreviation().')',
            'Value ('.$quote->getMainUnitAbbreviation().')'
        ];
        $colLength = $quote->getSubunitDenomination() + 6; // (decimal + up to 5 figures)
        foreach ([0,2,1,3,5] as $i) {
            $colVal = $columns[$i];
            while (strlen($colVal) < $colLength) {
                $colVal = ' '.$colVal;
            }
            $columns[$i] = $colVal;
        }

        $colLength = $base->getSubunitDenomination() + 6; // (decimal + up to 5 figures)
        $colVal = $columns[4];
        while (strlen($colVal) < $colLength) {
            $colVal = ' '.$colVal;
        }
        $columns[4] = $colVal;

        $this->columns = $columns;

        // Start the ticker!
        $this->startTicker($output);
    }

    protected function startTicker(OutputInterface $output)
    {
        try {
            $this->book->getBook();

            $output->writeln($this->getHeaders());
            while (true) {
                $currentState = $this->getCurrentState();
                $report = false;
                if ($currentState !== $this->lastState) {
                    $this->lastState = $currentState;
                    $report = true;
                } else {
                    for ($i=0, $j = count($currentState); $i < $j; $i++) {
                        if ($currentState[$i] != $this->lastState[$i]) {
                            $report = true;
                            $this->lastState = $currentState;
                            break;
                        }
                    }
                }
                if ($report) {
                    echo
                        self::STDOUT_PREFIX,
                        implode(
                            self::STDOUT_COLUMN_SEPARATOR,
                            $currentState
                        ),
                        "\r"
                    ;
                }

                usleep($this->refreshRate);
            }
        } catch (\Kobens\Core\Exception\ClosedBookException $e) {
            $this->lastState = [];
            $bookIsClosed = true;
            $output->writeln('Book is closed, waiting for book to be opened...');
            while ($bookIsClosed) {
                try {
                    $this->getCurrentState();
                    $bookIsClosed = false;
                    $output->writeln("Open Book detected...");
                } catch (\Kobens\Core\Exception\ClosedBookException $e) {
                    usleep($this->refreshRate);
                }
            }
            $this->startTicker($output);
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
        $askPrice = $this->book->getAskPrice();
        $bidPrice = $this->book->getBidPrice();
        $spread = $this->book->getSpread();
        $lastTrade = $this->book->getLastTrade();

        if ($lastTrade) {
            $price = $lastTrade->getPrice();
            $qty = $lastTrade->getQuantity();
            $rate = $this->book->getPair()->getQuoteQty($qty, $price);
        } else {
            $price = '';
            $qty = '';
            $rate = '';
        }

        $columns = [
            $askPrice,
            $bidPrice,
            $spread,
            $price,
            $qty,
            $rate
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
