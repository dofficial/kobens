<?php

namespace Kobens\Bittrex\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for unlocking an account.
 */
class Ticker extends Command
{
    protected function configure()
    {
        $this->addArgument(
            'market',
            InputArgument::OPTIONAL,
            'Start a ticker for the currency',
            ''
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }

}