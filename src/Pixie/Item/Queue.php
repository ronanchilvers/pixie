<?php

namespace Pixie\Item;

use Pixie\Item As BaseItem;

class Queue extends BaseItem
{
	const PENDING  = 'pending';
	const STARTED  = 'started';
	const COMPLETE = 'complete';

	static $_ItemName = 'task';
	static $_TableName = 'queue';
	static $_Fields = array(
			'key' => array(
				'type' 		=> BaseItem::TEXT,
				'required' 	=> true
				),
			'method' => array(
				'type' 		=> BaseItem::TEXT,
				'required' 	=> true
				),
			'args' => array(
				'type' 		=> BaseItem::TEXT,
				'required'  => true,
				'length'	=> 1024
				),
			'status' => array(
				'type' 		=> BaseItem::SELECT,
				'values' 	=> array('pending', 'started', 'complete'),
				'default' 	=> 'pending'
				),
			'created' => array(
				'type' 		=> BaseItem::DATETIME,
				'default' 	=> 'now',
				)
		);
	static $_OrderBy = 'task_created ASC';

	static public function Factory()
	{
		return new static();
	}

    static public function PopAll()
    {
        while (static::Pop())
        {}
        return true;
    }

	static public function Pop()
	{
		if (false == $task = static::FindFirst("task_status = :status", array('status' => static::PENDING)))
		{
			return false;
		}
        Pixie_Cli::Msg('App : ' . $task->key . ', method : ' . $task->method);
		if ($task->execute())
		{
			$task->destroy();
		}
		return true;
	}

    public function isStarted()
    {
        return ('started' == $this->status);
    }

    public function requeue()
    {
        $this->status = 'pending';
        return $this->save();
    }

	public function shove($item, $method)
	{
		$args = func_get_args();
		$item = array_shift($args);
		$method = array_shift($args);

		$this->key    = $item->getKey();
		$this->method = $method;
		$this->args   = serialize($args);

		return $this->save();
	}

	public function execute()
	{
		$this->status = static::STARTED;
		if (false == $this->save())
		{
			return false;
		}

		if (false == ($item = BaseItem::FindByKey($this->key)))
		{
			return false;
		}

		if (is_callable(array($item, $this->method)))
		{
			call_user_func_array(array($item, $this->method), unserialize($this->args));
		}

		$this->status = static::COMPLETE;
		if (false == $this->save())
		{
			return false;
		}

		return true;
	}

}
