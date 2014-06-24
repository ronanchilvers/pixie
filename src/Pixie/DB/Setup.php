<?php

namespace Pixie\DB;

use Pixie\Exception as BaseException;
use Pixie\Item\Exception as ItemException;
use Symfony\Component\Console\Output\OutputInterface;

class Setup extends Connection
{
    protected static $itemList = array(
            'Pixie\Item\App'
        );

    public function check(OutputInterface $output)
    {
        $output->writeLn('Connecting');
        $this->connection();
        $databasePath = $this->getPath();

        $output->writeLn('Checking database path ' . $databasePath);
        if (!is_writable($databasePath)) {
            $output->writeLn('Database path ' . $databasePath . ' is not writeable');
            if (false == chmod($databasePath, 0755)) {
                throw new Exception("Unable to chmod database path " . $databasePath);
            }
        }

        foreach (static::$itemList as $itemClass) {
            try {
                $output->writeLn('Checking model ' . $itemClass);
                $itemClass::CheckTable();
            }
            // catch (ItemException $ex) {
            //     $output->writeLn($ex->getMessage());
            // }
            catch (BaseException $ex) {
                $output->writeLn($ex->getMessage());
            }
        }
    }
}
