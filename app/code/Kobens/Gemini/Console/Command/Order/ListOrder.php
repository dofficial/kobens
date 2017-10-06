<?php

namespace Kobens\Gemini\Console\Command\Order;

// use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class ListOrder extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Kobens\Gemini\Api\V1\WebSocket\OrderData\OrderEvents
     */
    protected $orderEvents;

    public function __construct(
        \Kobens\Gemini\Api\V1\WebSocket\OrderData\OrderEvents\Proxy $orderEvents,
        $name = 'kobens:gemini:order:book-keeper'
    ) {
        $this->orderEvents = $orderEvents;
        parent::__construct($name);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->orderEvents
            ->setOutput($output)
            ->openOrderBook()
        ;
    }
}