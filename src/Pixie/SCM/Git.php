<?php

namespace Pixie\SCM;

class Git extends Base
{

	protected $_gitPath = '/usr/bin/git';

	public function checkout()
	{
		return $this->_git('clone -b ' . $this->app()->branch . ' ' . $this->app()->scm_url . ' ' . $this->app()->getDeploymentPath());
	}

	public function update()
	{
		if (false == chdir($this->app()->getDeploymentPath()))
		{
			return false;
		}
        $extra = 'origin ' . $this->app()->branch;
		return $this->_git('pull ' . $extra);
	}

	protected function _git($command)
	{
		$command = $this->_gitPath . ' ' . $command . ' 2>&1';

		$return = 0;
		exec($command, $output, $return);
        Pixie_Cli::Debug("Output : " . implode("\n", $output));
        Pixie_Cli::Debug("Exit Code : " . $return);
		if (0 < $return)
		{
			return false;
		}
		return true;
	}

}
