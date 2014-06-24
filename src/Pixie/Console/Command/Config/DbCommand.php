<?php

namespace Pixie\Console\Command\Config;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Pixie\DB\Setup;

class DbCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('config:db')
            ->setDescription('Check that the db file exists and model tables are up to date')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeLn('Checking database');
        $db = new Setup();
        $db->check($output);
    }
}
