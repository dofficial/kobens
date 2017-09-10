<?php

namespace Kobens\Gemini\Console\Command\BTC\USD;

use Kobens\Core\Helper\Console\Command\StdOut\Format;
use Kobens\Gemini\Model\BTC\USD\Book;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Ticker extends \Symfony\Component\Console\Command\Command
{
    const WEBSOCKET_URL = 'wss://api.gemini.com/v1/marketdata/btcusd';

    protected static $askPrice;

    protected static $bidPrice;

    protected static $columns = [
        'Lowest Ask',
        'Highest Bid',
        'Spread',
        'BTC / USD',
        "          BTC",
        "     USD",
        "    Time",
        "Heartbeat"
    ];

    protected static $lastMsg;
    protected static $secondToLastMsg;

    protected function configure()
    {
        parent::configure();
        $this->setDescription('BTC/USD Pair Live Pricing Ticker');
    }

    public static function getAskPrice()
    {
        return self::$askPrice;
    }

    public static function setAskPrice($price)
    {
        self::$askPrice = $price;
    }

    public static function getBidPrice()
    {
        return self::$bidPrice;
    }

    public static function setBidPrice($price)
    {
        self::$bidPrice = $price;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->connect($output);

    }

    public function connect(OutputInterface $output)
    {
        $loop = \React\EventLoop\Factory::create();
        $connector = new \Ratchet\Client\Connector($loop);
        $connector(self::WEBSOCKET_URL)
            ->then(function(\Ratchet\Client\WebSocket $conn) use ($output) {
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn, $output) {
                    $msg = json_decode($msg);
                    switch ($msg->type) {
                        case 'heartbeat':
                            break;

                        case 'update':
                            self::processUpdate($msg, $output, $conn);
                            break;

                        default:
                            throw new \Exception ('Unhandled Message Type: '.$msg->type."\n");
                            break;
                    }
                });

                $conn->on('close', function($code = null, $reason = null) {
                    echo "Connection Closed ($code - $reason)\n";
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
            $output->writeln(self::getHeaders());
        }
        $askPrice = Book::getAskPrice();
        $bidPrice = Book::getBidPrice();
        $spread = floatval(number_format($askPrice - $bidPrice, 2));

        // Something fucked up... need to debug it
        if ($spread < 0) {
            $conn->close();
            // trim the book to a readable size
            $book = Book::getBook();
            $i = 0;
            foreach (array_keys($book['ask']) as $key) {
                if ($i > 10) {
                    unset($book['ask'][$key]);
                }
                $i++;
            }
            $count = count($book['bid']);
            $i = 0;
            foreach (array_keys($book['bid']) as $key) {
                unset($book['bid'][$key]);
                $i++;
                if ($count - $i = 10) {
                    break;
                }
            }
            \Zend_Debug::dump([
                'book' => $book,
                'second to last message' => Ticker::$secondToLastMsg,
                'last message' => Ticker::$lastMsg,
                'current message' => $msg
            ]);
            exit;
        }
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
        for ($i = 0, $j = count(Ticker::$columns); $i < $j; $i++) {
            while (strlen($columns[$i]) < strlen(Ticker::$columns[$i])) {
                $columns[$i] = ' '.$columns[$i];
            }
        }
        $columns[0] = Format::red($columns[0]);
        $columns[1] = Format::green($columns[1]);
        $columns[3] = $lastTrade['maker'] == 'bid' ? Format::red($columns[3]) : Format::green($columns[3]);

        echo "\t",implode("\t", $columns),"\r";

        Ticker::$secondToLastMsg = Ticker::$lastMsg;
        Ticker::$lastMsg = $msg;
    }

    protected function getHeaders()
    {
        $headers = "\t";
        for ($i = 0, $j = count(Ticker::$columns); $i < $j; $i++) {
            if ($i <> 0) {
                $headers .= "\t";
            }
            $headers .= Format::underline(Ticker::$columns[$i]);
        }
        return $headers;
    }
}
