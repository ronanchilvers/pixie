<?php

namespace Pixie\Console\Command\Deployment;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Pixie\Item\App;

class Delete extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('deployment:delete')
            ->setDescription('Delete a deployment permanently')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'The id of the deployment')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption('id');
        $app = App::findFirst($id);
        if (!$app instanceof App) {
            $output->writeLn('Unable to locate deployment id ' . $id);
            return false;
        }
        if (false == $app->destroy()) {
            $output->writeLn('Failed to delete deployment');
            return false;
        }

        $output->writeLn('Deployment deleted');
    }
}
