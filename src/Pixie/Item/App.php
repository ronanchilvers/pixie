<?php

namespace Pixie\Item;

use Pixie\File;
use Pixie\Item As BaseItem;
use Pixie\SCM\Base As SCM;
use Pixie\String;

class App extends BaseItem
{
    const STATUS_NEW        = 'new';
    const STATUS_UPDATING   = 'updating';
    const STATUS_OK         = 'ok';
    const STATUS_FAILED     = 'failed';

    const ENV_DEV           = 'development';
    const ENV_STAGING       = 'staging';
    const ENV_PRODUCTION    = 'production';

	static protected $itemName  = 'app';
	static protected $tableName = 'apps';
	static protected $fields    = array(
			'name'        => array(
				'type' 		=> BaseItem::TEXT,
				'required'  => true,
                'listing'   => true,
				),
			'root'        => array(
				'type' 		=> BaseItem::TEXT,
				'required'  => true,
                'listing'   => true
				),
			'config_base' 	  => array(
				'type' 		=> BaseItem::TEXT,
				'default' 	=> 'config/virtualhost.conf',
				'required'  => true,
				),
			'environment' => array(
				'type'    	=> BaseItem::SELECT,
				'default' 	=> 'development',
				'values' 	=> array('development', 'staging', 'production'),
				'required' 	=> true,
                'listing'   => true,
				),
			'scm' 		  => array(
				'type'    	=> BaseItem::SELECT,
				'values'	=> array(SCM::GIT, SCM::SVN),
				'default'	=> SCM::GIT,
				'required'  => true,
                'listing'   => true,
				),
			'scm_url'	  => array(
				'type'    	=> BaseItem::TEXT,
				'required'  => true,
                'listing'   => true,
				),
			'branch' 	  => array(
				'type'    	=> BaseItem::TEXT,
				'default' 	=> 'master',
                'listing'   => true,
				),
			'status'	  => array(
				'type'   	=> BaseItem::TEXT,
				'default'   => 'new',
                'listing'   => true,
				),
			'created' 	  => array(
				'type'   	=> BaseItem::DATETIME,
				'default'   => 'now',
				),
			'deployed' 	  => array(
				'type'   	=> BaseItem::DATETIME
				),
			'last_updated'=> array(
				'type'   	=> BaseItem::DATETIME,
                'default'   => 'now'
				)
		);

	public function beforeCreate()
	{
		parent::beforeCreate();
		if ("" == $this->root)
		{
            $this->root = $this->name;
		}
        $this->root = String::Urlize($this->root);

        if ("master" == $this->branch && "svn" == $this->scm)
        {
            $this->branch = 'trunk';
        }
	}

	public function queue()
	{
		static::db()->begin();

		$this->status = static::STATUS_UPDATING;
		if (false == $this->save())
		{
			static::db()->rollback();
			return false;
		}

		if (false == Pixie_Queue::Factory()->shove($this, 'deploy'))
		{
			static::db()->rollback();
			return false;
		}

		static::db()->commit();
		return true;
	}

	public function deploy()
	{
		try {
			$deployment = Pixie_App_Deployment::Factory($this);
			$deployment->execute();
			$deployment->configure();
            $deployment->runHooks();

			if (get_class($deployment))
			{
				$this->deployed = date('Y-m-d H:i:s');
			}
			$this->last_updated = date('Y-m-d H:i:s');
			$this->status       = static::STATUS_OK;
			return $this->save();
		}
		catch (Pixie_Deployment_Exception $ex) {
            Pixie_Cli::Err($ex->getMessage());
			$this->status = static::STATUS_FAILED;
			$this->save();
		}
	}

	public function getDeploymentPath()
	{
		$config = Pixie::Config();
		return File::Join($config['app']['base'], $this->root, $this->environment);
	}

	public function getConfigurationTemplatePath()
	{
		$config = Pixie::Config();
		return File::Join($this->getDeploymentPath(), $this->config_base . '.' . $this->environment);
	}

	public function getConfigurationPath()
	{
		$config = Pixie::Config();
		return File::Join($config['app']['conf'], $this->root . '.' . $this->environment . '.conf');
	}

	public function isNew()
	{
		return (static::STATUS_NEW == $this->status);
	}

	public function isUpdating()
	{
		return (static::STATUS_UPDATING == $this->status);
	}
}
