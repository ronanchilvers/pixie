<?php

namespace Pixie\SCM;

abstract class Base
{

    const GIT = 'git';
    const SVN = 'svn';

	static public function Factory(Pixie_App $app)
	{
        switch ($app->scm)
        {
            case static::SVN:
                return new Pixie_SCM_Svn($app);
                break;

            case static::GIT:
            default:
                return new Pixie_SCM_Git($app);
                break;
        }
        throw new Pixie_Exception('Unknown SCM ' . $app->scm);
	}

	abstract public function checkout();
	abstract public function update();

	protected $_app;

	public function __construct(Pixie_App $app = null)
	{
		$this->_app = $app;
	}

	public function app()
	{
		return $this->_app;
	}
}
