<?php

namespace Pixie\Console\Command\Deployment;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Pixie\Item\App;

class Add extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('deployment:add')
            ->setDescription('Add a deployment')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The name for the deployment')
            ->addOption('url', 'u', InputOption::VALUE_REQUIRED, 'The URL to checkout the deployment from')
            ->addOption('environment', 'e', InputOption::VALUE_OPTIONAL, 'The environment for the deployment - development, staging or production', App::ENV_DEV)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app                = new App();
        $app->name          = $input->getOption('name');
        $app->environment   = $input->getOption('environment');
        $app->scm_url       = $input->getOption('url');

        if (false == $app->save()) {
            $output->writeLn('Failed to save deployment');
            return false;
        }

        $output->writeLn('Deployment added');
    }
}
