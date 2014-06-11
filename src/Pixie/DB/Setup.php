<?php

namespace Pixie\DB;

class Setup extends DB
{

	public function check()
	{
        $this->_connect();

        $databasePath = $this->_getPath();

        if (!is_writable($databasePath))
        {
            if (false == chmod($databasePath, Pixie_Setup::DIR_MODE))
            {
                throw new Pixie_Exception("Unable to chmod database path " . $databasePath);
            }
        }

		foreach (Pixie::ItemList() as $itemClass)
		{
			$itemClass::CheckTable();
		}
	}

}
