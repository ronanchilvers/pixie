<?php

class Pixie_SCM_Svn extends Pixie_SCM
{

	protected $_svnPath = '/usr/bin/svn';

	public function checkout()
	{
        // svn checkout url path
		return $this->_svn('checkout ' . $this->_repositoryPath() . ' ' . $this->app()->getDeploymentPath());
	}	

	public function update()
	{
		if (false == chdir($this->app()->getDeploymentPath()))
		{
			return false;
		}
		return $this->_svn('update');
	}

    protected function _repositoryPath()
    {
        return Pixie_HTTP::Join($this->app()->scm_url, $this->app()->branch);
    }

	protected function _svn($command)
	{
        $config = Pixie::Config();
        $username = $config['svn']['username'];
        $password = $config['svn']['password'];
		$command = $this->_svnPath . ' --username ' . $username . ' --password ' . $password . ' ' . $command . ' 2>&1';		
        Pixie_Cli::Debug('SVN Command : ' . $command);
	
		$return = 0;
		exec($command, $output, $return);
		if (0 < $return)
		{
			return false;
		}
		return true;
	}

}