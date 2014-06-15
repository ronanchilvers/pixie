<?php

namespace Pixie\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('A test command')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'A name for the test command to echo'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Name is ' . $name;
        } else {
            $text = 'No name given';
        }
        $output->writeln($text);
    }
}
