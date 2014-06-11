<?php

namespace Pixie\DB;

class Connection
{

    protected $_connection;
    protected $_lastInsertId;

    public function __construct()
    {}

    public function __destruct()
    {
        if ($this->_connection instanceof SQLite3) {
            $this->_connection->close();
        }
    }

    public function execute($sql)
    {
        return $this->_runQuery($sql);
    }

    public function select($sql, $params = array())
    {
        if (!empty($params)) {
            $sql = $this->_escapeInto($sql, $params);
        }
        if (false == ($result = $this->_runQuery($sql))) {
            return false;
        }
        $output = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $output[] = $row;
        }

        return $output;
    }

    public function insert($table, $record)
    {
        $columns = $values = array();
        foreach ($record as $column => $value) {
            $columns[] = $this->quote($column);
            $values[]  = "'" . $this->_escape($value) . "'";
        }
        $columns = implode(',', $columns);
        $values  = implode(',', $values);

        $sql = 'INSERT INTO ' . $this->quote($table) . " ({$columns}) VALUES ({$values})";
        if (false == $this->_runQuery($sql)) {
            return false;
        }

        $this->_lastInsertId = $this->_connection->lastInsertRowId();

        return true;
    }

    public function update($table, $record, $where, $params = array())
    {
        $columns = array();
        foreach ($record as $column => $value) {
            $columns[] = $this->quote($column) . ' = \'' . $this->_escape($value) . '\'';
        }
        $columns = implode(', ', $columns);

        $where = $this->_escapeInto($where, $params);

        $sql = "UPDATE " . $this->quote($table) . " SET {$columns} WHERE {$where}";

        return $this->_runQuery($sql);
    }

    public function delete($table, $where, $params = array())
    {
        $where = $this->_escapeInto($where, $params);

        $sql = "DELETE FROM " . $this->quote($table) . " WHERE {$where}";

        return $this->_runQuery($sql);
    }

    public function schema($table)
    {
        $sql = "PRAGMA table_info({$table});";
        if (false == ($result = $this->select($sql))) {
            return false;
        }

        $info = array();
        foreach ($result as $row) {
            $info[$row['name']] = array(
                    'type'    => $row['type'],
                    'notnull' => (bool) $row['notnull'],
                    'pk'      => (bool) $row['pk']
                );
        }

        return $info;
    }

    public function begin()
    {
        return $this->_runQuery('BEGIN');
    }

    public function rollback()
    {
        return $this->_runQuery('ROLLBACK');
    }

    public function commit()
    {
        return $this->_runQuery('COMMIT');
    }

    public function insertId()
    {
        return $this->_lastInsertId;
    }

    public function lastErrorCode()
    {
        $this->_connect();

        return $this->_connection->lastErrorCode();
    }

    public function quote($string)
    {
        return "`{$string}`";
    }

    protected function _escape($value)
    {
        $this->_connect();

        return $this->_connection->escapeString($value);
    }

    protected function _escapeInto($string, $params)
    {
        $find   = array();
        $rep    = array();

        foreach ($params as $param => $value) {
            $find[] = ":{$param}";
            $rep[]  = $this->_prepareValue($value);
        }

        $clause     = str_replace($find, $rep, $string);

        return $clause;
    }

    protected function _prepareValue($value)
    {
        $output     = '';
        if (is_int($value) || is_float($value)) {
            $output = $this->_escape($value);
        } elseif (is_string($value) || empty($value)) {
            $output = "'" . $this->_escape($value) . "'";
        }

        return $output;
    }

    protected function _runQuery($sql)
    {
    	$this->_connect();
    	if ('SELECT' == substr($sql, 0, 6) || 'PRAGMA' == substr($sql, 0, 6))
    	{
	    	if (false == ($result = @$this->_connection->query($sql)))
	    	{
	    		return false;
	    	}
	    	return $result;
    	}
    	if (false == ($result = $this->_connection->exec($sql)))
    	{
    		return false;
    	}
    	return true;
    }

    protected function _connect()
    {
    	if (false == is_resource($this->_connection))
    	{
	    	try {
		    	$databasePath = $this->_getPath();
                $this->_connection = new SQLite3($databasePath);
	    	}
	    	catch (Exception $ex) {
	    		throw new Pixie_Db_Exception($ex->getMessage());
	    	}
    	}
    }

    protected function _getPath()
    {
        $config = Pixie::Config();
        return Pixie_File::Join(CONFIG, $config['database']['filename']);
    }

}
