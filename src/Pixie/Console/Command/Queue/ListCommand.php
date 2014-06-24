<?php

namespace Pixie\Console\Command\Queue;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('queue:list')
            ->setDescription('List all current queue jobs')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = 'Task list';
        $output->writeln($text);
    }
}
