<?php

namespace Pixie\Console\Command\Deployment;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Pixie\Item\App;

class Listing extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('deployment:list')
            ->setDescription('List all deployments')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apps  = App::find();

        if (0 == count($apps)) {
            $output->writeLn('No deployments found');
            return;
        }

        $app        = new App();
        $fields     = $app->getListingFields();
        $table      = new Table($output);
        $table->setHeaders(array_merge(array('id'),$fields));
        foreach ($apps as $app) {
            $table->addRow(array_merge(array($app->id), $app->getListingData()));
        }
        $table->render();
    }
}
