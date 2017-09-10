<?php

namespace Kobens\Gemini\Model\BTC\USD\Book;

use Magento\Framework\App\ResourceConnection;

class Keeper
{
    protected $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $event
     */
    public static function updateBook(\stdClass $event, $time)
    {
        $side = $event->side;
        $price = $event->price;
        $remaining = $event->remaining;

        if (isset(self::$book[$side][$price])) {
            $lastTime = self::$book[$side][$price]['time'];
            if ($lastTime < $time) {
                if ($remaining == 0) {
                    unset(self::$book[$side][$price]);
                } else {
                    self::_updateBook($side, $price, $remaining, $time);
                }
            }
        } else {
            self::_updateBook($side, $price, $remaining, $time);
        }
    }

    protected static function _updateBook($side, $price, $remaining, $time)
    {
        self::$book[$side][$price] = [
            'remaining' => $remaining,
            'time' => $time,
        ];
    }

    public static function getAskPrice()
    {
        ksort(self::$book['ask']);
        return floatval(array_keys(self::$book['ask'])[0]);
    }

    public static function getBidPrice()
    {
        ksort(self::$book['bid']);
        $vals = array_keys(self::$book['bid']);
        return floatval(end($vals));
    }

    public static function getLastTradePrice()
    {
        return self::$lastTrade['price'];
    }

    public static function setLastTradePrice($price, $time)
    {
        if (is_null(self::$lastTrade['time']) || self::$lastTrade['time'] < $time) {
            self::$lastTrade['price'] = $price;
            self::$lastTrade['time'] = $time;
        }
    }

    public function connect(OutputInterface $output)
    {
        $loop = \React\EventLoop\Factory::create();
        $connector = new \Ratchet\Client\Connector($loop);
        $connector(self::WEBSOCKET_URL)
            ->then(function(\Ratchet\Client\WebSocket $conn) use ($output) {
            $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn, $output) {
                $origMsg = $msg;
                $msg = json_decode($msg);
                switch ($msg->type) {
                    case 'heartbeat':
                        $output->writeln('Heartbeat Detected');
                        break;

                    case 'update':
                        $report = false;
                        foreach ($msg->events as $event) {
                            switch ($event->type) {
                                case 'change':
                                    $time = $event->reason == 'initial' ? time() : $msg->timestampms;
                                    Book::updateBook($event, $time);
                                    break;
                                case 'trade':
                                    Book::setLastTradePrice($event->price, $msg->timestampms);
                                    $output->writeln("Last Trade Price: {$event->price}");
                                    $report = true;
                                    break;
                                default:
                                    \Zend_Debug::dump($msg);
                                    exit;
                            }
                        }
                        if (Ticker::getAskPrice() != ($askPrice = Book::getAskPrice())) {
                            Ticker::setAskPrice($askPrice);
                            $report = true;
                        }
                        if (Ticker::getBidPrice() != ($bidPrice = Book::getBidPrice())) {
                            Ticker::setBidPrice($bidPrice);
                            $report = true;
                        }
                        if ($report) {
                            exec('tput reset');
                            $spread = floatval(number_format($askPrice - $bidPrice, 2));
                            if ($spread < 0) {
                                $conn->close();
                                \Zend_Debug::dump([
                                    Ticker::$secondToLastMsg,
                                    Ticker::$lastMsg,
                                    $msg
                                ]);
                                exit;
                            }
                            $output->write("Ask: $askPrice\tBid: $bidPrice\tSpread:$spread\r");
                        }
                        Ticker::$secondToLastMsg = Ticker::$lastMsg;
                        Ticker::$lastMsg = $msg;
                        break;

                    default:
                        throw new \Exception ('Unhandled Message Type: '.$msg->type."\n".$origMsg);
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
}
