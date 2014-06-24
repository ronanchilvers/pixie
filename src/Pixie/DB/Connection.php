<?php

namespace Pixie\DB;

use Pixie\File;
use Pixie\Environment;
use SQLite3;

class Connection
{
    const DEFAULT_FILENAME = 'pixie.db';

    protected $resource;
    protected $lastInsertId;
    protected $lastErrorCode;
    protected $lastErrorMsg;

    public function __construct()
    {}

    public function __destruct()
    {
        if ($this->resource instanceof SQLite3) {
            $this->resource->close();
        }
    }

    public function execute($sql)
    {
        return $this->runQuery($sql);
    }

    public function select($sql, $params = array())
    {
        if (!empty($params)) {
            $sql = $this->escapeInto($sql, $params);
        }
        if (false == ($result = $this->runQuery($sql))) {
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
            $values[]  = "'" . $this->escape($value) . "'";
        }
        $columns    = implode(',', $columns);
        $values     = implode(',', $values);
        $sql        = 'INSERT INTO ' . $this->quote($table) . " ({$columns}) VALUES ({$values})";
        if (false == $this->runQuery($sql)) {
            return false;
        }
        $this->lastInsertId = $this->connection()->lastInsertRowId();

        return true;
    }

    public function update($table, $record, $where, $params = array())
    {
        $columns = array();
        foreach ($record as $column => $value) {
            $columns[] = $this->quote($column) . ' = \'' . $this->escape($value) . '\'';
        }
        $columns    = implode(', ', $columns);
        $where      = $this->escapeInto($where, $params);
        $sql        = "UPDATE " . $this->quote($table) . " SET {$columns} WHERE {$where}";

        return $this->runQuery($sql);
    }

    public function delete($table, $where, $params = array())
    {
        $where  = $this->escapeInto($where, $params);
        $sql    = "DELETE FROM " . $this->quote($table) . " WHERE {$where}";

        return $this->runQuery($sql);
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
        return $this->runQuery('BEGIN');
    }

    public function rollback()
    {
        return $this->runQuery('ROLLBACK');
    }

    public function commit()
    {
        return $this->runQuery('COMMIT');
    }

    public function insertId()
    {
        return $this->lastInsertId;
    }

    public function lastErrorCode()
    {
        return $this->lastErrorCode;
        // return $this->connection()->lastErrorCode();
    }

    public function lastErrorMsg()
    {
        return $this->lastErrorMsg;
        // return $this->connection()->lastErrorMsg();
    }

    public function quote($string)
    {
        return "`{$string}`";
    }

    protected function escape($value)
    {
        return $this->connection()->escapeString($value);
    }

    protected function escapeInto($string, $params)
    {
        $find   = array();
        $rep    = array();

        foreach ($params as $param => $value) {
            $find[] = ":{$param}";
            $rep[]  = $this->prepareValue($value);
        }

        $clause     = str_replace($find, $rep, $string);

        return $clause;
    }

    protected function prepareValue($value)
    {
        $output     = '';
        if (is_int($value) || is_float($value)) {
            $output = $this->escape($value);
        } elseif (is_string($value) || empty($value)) {
            $output = "'" . $this->escape($value) . "'";
        }

        return $output;
    }

    protected function runQuery($sql)
    {
        $this->lastErrorCode    = false;
        $this->lastErrorMsg     = false;

        $connection = $this->connection();
        if ('SELECT' == substr($sql, 0, 6) || 'PRAGMA' == substr($sql, 0, 6)) {
            if (false == ($result = @$connection->query($sql))) {
                $this->lastErrorCode = $connection->lastErrorCode();
                $this->lastErrorMsg = $connection->lastErrorMsg();
                return false;
            }

            return $result;
        }
        if (false == ($result = @$connection->exec($sql))) {
            $this->lastErrorCode = $connection->lastErrorCode();
            $this->lastErrorMsg = $connection->lastErrorMsg();
            return false;
        }

        return true;
    }

    protected function connection()
    {
        if (false == is_resource($this->resource)) {
            try {
                $databasePath       = $this->getPath();
                $this->resource     = new SQLite3($databasePath);
            } catch (\Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }

        return $this->resource;
    }

    protected function getPath()
    {
        $env        = Environment::instance();
        $config     = $env->config();
        if (isset($config['path.database'])) {
            return $config['path.database'];
        }

        return File::join($env->getConfigDir(), static::DEFAULT_FILENAME);
    }

}
