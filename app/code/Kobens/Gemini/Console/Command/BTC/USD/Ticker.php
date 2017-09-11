<?php

namespace Kobens\Gemini\Console\Command\BTC\USD;

use Kobens\Core\Helper\Console\Command\StdOut\Format;
use Kobens\Gemini\Model\BTC\USD\Book;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Ticker extends \Symfony\Component\Console\Command\Command
{
    const WEBSOCKET_URL = 'wss://api.gemini.com/v1/marketdata/btcusd';

    const STDOUT_PREFIX = " "; // " " | "\t"
    const STDOUT_COLUMN_SEPARATOR = "  "; // " " | "\t"

    protected $columns = [
        'Lowest Ask',
        'Highest Bid',
        'Spread',
        'Last Trade',
        "          BTC",
        "     USD",
        "Time    ",
        "Heartbeat"
    ];

    /**
     * @var int
     */
    protected $socketSequence = 0;

    protected function configure()
    {
        parent::configure();
        $this->setDescription('BTC/USD Pair Live Pricing Ticker');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $this->connect($output);
        }
    }

    public function connect(OutputInterface $output)
    {
        $this->socketSequence = 0;
        $loop = \React\EventLoop\Factory::create();
        $connector = new \Ratchet\Client\Connector($loop);
        $connector(self::WEBSOCKET_URL)
            ->then(function(\Ratchet\Client\WebSocket $conn) use ($output) {
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn, $output) {
                    $msg = json_decode($msg);
                    if ($msg->socket_sequence != 0) {
                        if ($this->socketSequence <> $msg->socket_sequence-1) {
                            $conn->close(1000, 'Non-sequential socket squence detected, reconnecting...');
                            return;
                        }
                        $this->socketSequence = $msg->socket_sequence;
                    }
                    switch ($msg->type) {
                        case 'heartbeat':
                            break;

                        case 'update':
                            $this->processUpdate($msg, $output, $conn);
                            break;

                        default:
                            throw new \Exception ('Unhandled Message Type: '.$msg->type."\n");
                            break;
                    }
                });

                $conn->on('close', function($code = null, $reason = null) {
                    echo "\nConnection Closed ($code - $reason)\r\n\r\n\r\n\r\n";
                });
            },
            function(\Exception $e) use ($loop) {
                echo "Could not connect: {$e->getMessage()}\n";
                $loop->stop();
            }
        );
        $loop->run();
    }

    /**
     * @param unknown $msg
     * @param unknown $output
     */
    public function processUpdate($msg, $output, $conn)
    {
        foreach ($msg->events as $event) {
            switch ($event->type) {
                case 'change':
                    $time = $event->reason == 'initial' ? time() : $msg->timestampms;
                    Book::updateBook($event, $time);
                    break;
                case 'trade':
                    if (in_array($event->makerSide, ['bid','ask'])) {
                        Book::setLastTrade(
                            $event->price,
                            $event->makerSide,
                            $event->amount,
                            $msg->timestampms
                        );
                    }
                    break;
                default:
                    \Zend_Debug::dump($msg);
                    exit;
            }
        }
        if ($msg->events[0]->type == 'change' && $msg->events[0]->reason == 'initial') {
            $output->writeln($this->getHeaders());
        }

        echo self::STDOUT_PREFIX, implode(self::STDOUT_COLUMN_SEPARATOR, $this->getCurrentState()), "\r";
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
        $askPrice = Book::getAskPrice();
        $bidPrice = Book::getBidPrice();
        $spread = floatval(number_format($askPrice - $bidPrice, 2));
        $lastTrade = Book::getLastTrade();
        $columns = [
            Book::getAskPrice(),
            Book::getBidPrice(),
            $spread,
            $lastTrade['price'],
            $lastTrade['amount'],
            round($lastTrade['amount'] * $lastTrade['price'], 2, PHP_ROUND_HALF_UP),
            $lastTrade['time'] ? date('H:i:s', $lastTrade['time']) : '',
            microtime(true)
        ];
        for ($i = 0, $j = count($this->columns); $i < $j; $i++) {
            while (strlen($columns[$i]) < strlen($this->columns[$i])) {
                $columns[$i] = ' '.$columns[$i];
            }
        }
        $columns[0] = Format::red($columns[0]);
        $columns[1] = Format::green($columns[1]);
        $columns[3] = $lastTrade['maker'] == 'bid' ? Format::red($columns[3]) : Format::green($columns[3]);

        return $columns;
    }
}
