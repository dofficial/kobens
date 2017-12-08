<?php

namespace Kobens\Gemini\Api\V1\WebSocket\MarketData;

/**
 * @category    \Kobens
 * @package     \Kobens\Gemini
 */
abstract class AbstractBookKeeper extends \Kobens\Core\Model\Exchange\Book\Keeper\AbstractKeeper
{
    const WEBSOCKET_URL = 'wss://api.gemini.com/v1/marketdata/:pair';

    /**
     * @var string
     */
    protected $websocketUrl;

    /**
     * @var int
     */
    protected $socketSequence;

    /**
     * Return the url for the market's websocket API corresponding to
     * the book's currency pair.
     *
     * @return string
     */
    protected function getWebSocketUrl()
    {
        if (!$this->websocketUrl) {
            $pair =
                $this->getBaseCurrency()->getPairIdentity() .
                $this->getQuoteCurrency()->getPairIdentity()
            ;
            $this->websocketUrl = str_replace(':pair', $pair, self::WEBSOCKET_URL);
        }
        return $this->websocketUrl;
    }

    /**
     * Open the market's order book and maintain an up to date copy of it by
     * sending event data off to the book keeper.
     *
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\Keeper\AbstractKeeper::openBook()
     */
    public function openBook()
    {
        $this->socketSequence = 0;
        $loop = \React\EventLoop\Factory::create();
        $connector = new \Ratchet\Client\Connector($loop);
        $connector($this->getWebSocketUrl())->then(
            function(\Ratchet\Client\WebSocket $conn) {
                $conn->on('message', function(\Ratchet\RFC6455\Messaging\MessageInterface $msg) use ($conn) {
                    $msg = json_decode($msg);
                    if ($msg->socket_sequence == 0) {
                        $this->populateBook($msg->events);
                    } else {
                        if ($this->socketSequence <> $msg->socket_sequence-1) {
                            $conn->close(1000, 'Non-sequential socket squence detected, reconnecting...');
                            return;
                        }
                        $this->socketSequence = $msg->socket_sequence;

                        switch ($msg->type) {
                            case 'heartbeat':
                                // TODO: Gemini recommends logging and retaining all heartbeat messages. If your WebSocket connection is unreliable, please contact Gemini support with this log.
                                // FIXME: If you miss one or more heartbeats, disconnect and reconnect.
                                $book = $this->cache->load($this->getBookCacheKey());
                                $this->cache->save($book, $this->getBookCacheKey());
                                break;

                            case 'update':
                                try {
                                    $this->processEvents($msg->events, $msg->timestampms);
                                } catch (\Kobens\Core\Exception\ClosedBookException $e) {
                                    $conn->close(10000, 'Closed book detected. reconnecting...');
                                }
                                break;

                            default:
                                throw new \Exception ('Unhandled Message Type: '.$msg->type."\n");
                                break;
                        }

                    }
                });
                $conn->on('close', function($code = null, $reason = null) {
                    if ($reason) {
                        // TODO: Do anything with this?
                    }
                    $this->openBook();
                });
            },
            function(\Exception $e) use ($loop) {
                $loop->stop();
                die($e->getMessage());
            }
        );
        $loop->run();
    }

    /**
     * Populate the market's order book
     *
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\Book\Keeper\AbstractKeeper::populateBook()
     */
    protected function populateBook(array $events)
    {
        if ($events[0]->reason !== 'initial') {
            throw new \Exception('Book can only be populated with initial event set');
        }
        $book = [
            'bid' => [],
            'ask' => []
        ];
        foreach ($events as $event) {
            $book[$event->side][(string) $event->price] = floatval($event->remaining);
        }
        parent::populateBook($book);
    }

    /**
     * Process a set of events for the market's order book.
     *
     * @param array $events
     * @param decimal $timestampms
     */
    protected function processEvents(array $events, $timestampms)
    {
        foreach ($events as $event) {
            switch ($event->type) {
                case 'change':
                    $this->updateBook($event->side, $event->price, floatval($event->remaining));
                    break;
                case 'trade':
                    if (in_array($event->makerSide, ['bid','ask'])) {
                        $this->setLastTrade(new \Kobens\Core\Model\Exchange\Book\Trade\Trade(
                            $event->makerSide,
                            $event->amount,
                            $event->price,
                            $timestampms
                        ));
                    }
                    break;

                case 'auction_indicative':
                default:
                    break;
            }
        }
    }

}
