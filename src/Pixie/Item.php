<?php

namespace Pixie;

use Pixie\Environment;
use Pixie\Item\TableExistsException;
use Pixie\Item\UnableToCreateTableException;

abstract class Item
{
    const INT  	   = 'int';
    const TEXT     = 'text';
    const SELECT   = 'select';
    const DATETIME = 'datetime';

    protected static $itemName  = false;
    protected static $tableName = false;
    protected static $fields 	 = array();
    protected static $orderBy 	 = false;

    public static function ItemName()
    {
        return static::$itemName;
    }

    public static function TableName()
    {
        return static::$tableName;
    }

    public static function IdColName()
    {
        return static::FullFieldname('id');
    }

    public static function OrderBy()
    {
        return static::$orderBy;
    }

    public static function FullFieldname($name)
    {
        $itemName = static::ItemName();
        if (0 === strpos($name, $itemName)) {
            return $name;
        }

        return $itemName . '_' . $name;
    }

    public static function CheckTable()
    {
        if (false == static::db()->select("SELECT * FROM " . static::TableName())) {
            return static::CreateTable();
        }

        return static::UpdateTable();
    }

    public static function CreateTable()
    {
        $fieldDefinitions = array();
        $fieldDefinitions[] = static::db()->quote(static::IdColName()) . ' INTEGER PRIMARY KEY AUTOINCREMENT';
        foreach (static::$fields as $name => $field) {
            $fieldName = static::FullFieldname($name);
            $definition = static::db()->quote($fieldName) . ' ';

            switch ($field['type']) {

                case static::DATETIME:
                    $definition .= 'DATETIME';
                    break;

                case static::INT:
                    $definition .= 'INTEGER';
                    break;

                default:
                    $length = (isset($field['length'])) ? $field['length'] : 255;
                    $definition .= 'VARCHAR(' . $length . ')';
                    break;
            }

            $fieldDefinitions[] = $definition;
        }

        $sql = 'CREATE TABLE ' . static::db()->quote(static::TableName()) . " (\n\t";
        $sql .= implode(",\n\t", $fieldDefinitions);
        $sql .= "\n);";

        if (false == static::db()->execute($sql)) {
            throw new UnableToCreateTableException(static::db()->lastErrorMsg(), static::db()->lastErrorCode());
        }

        return true;
    }

    public static function UpdateTable()
    {
        if (false == ($info = static::db()->schema(static::TableName()))) {
            return false;
        }

        $fieldDefinitions = array();
        foreach (static::$fields as $name => $field) {
            $fieldName = static::FullFieldname($name);
            if (array_key_exists($fieldName, $info)) {
                continue;
            }

            $definition = "ADD COLUMN " . static::db()->quote($fieldName) . ' ';

            switch ($field['type']) {

                case static::DATETIME:
                    $definition .= 'DATETIME';
                    break;

                case static::INT:
                    $definition .= 'INTEGER';
                    break;

                default:
                    $length = (isset($field['length'])) ? $field['length'] : 255;
                    $definition .= 'VARCHAR(' . $length . ')';
                    break;
            }

            $fieldDefinitions[] = $definition;
        }

        if (empty($fieldDefinitions)) {
            return true;
        }

        $sql = 'ALTER TABLE ' . static::db()->quote(static::TableName()) . " \n\t";
        $sql .= implode(",\n\t", $fieldDefinitions);
        $sql .= "\n;";

        if (false == static::db()->execute($sql)) {
            throw new UnableToAlterTableException(static::db()->lastErrorMsg(), static::db()->lastErrorCode());
        }

        return true;
    }

    public static function FindByKey($key)
    {
        $key = strrev($key);
        list($id, $class) = explode('_', $key, 2);
        $id 	= (int) strrev($id);
        $class  = strrev($class);
        if (0 < (int) $id && class_exists($class)) {
            return $class::Find((int) $id);
        }

        return false;
    }

    public static function FindFirst($idOrWhere = 'all', $params = array())
    {
        return static::Find($idOrWhere, $params, 1);
    }

    public static function Find($idOrWhere = 'all', $params = array(), $limit = false)
    {
        $sql = "SELECT * FROM " . static::db()->quote(static::TableName());
        if (is_integer($idOrWhere)) {
            $sql .= " WHERE " . static::IdColName() . " = :itemId";
            $params = array('itemId' => $idOrWhere);

            return static::FindFirstBySql($sql, $params);
        } elseif ('all' != $idOrWhere) {
            $sql .= " WHERE {$idOrWhere}";
        }

        if ($orderBy = static::OrderBy()) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if (1 == $limit) {
            return static::FindFirstBySql($sql, $params);
        } elseif (false !== $limit) {
            $sql .= " LIMIT {$limit}";
        }

        return static::FindBySql($sql, $params);
    }

    public static function FindFirstBySql($sql, $params = array())
    {
        $sql .= ' LIMIT 1';
        if (false == ($result = static::FindBySql($sql, $params))) {
            return false;
        }

        if (0 == count($result)) {
            return false;
        }

        return array_shift($result);
    }

    public static function FindBySql($sql, $params = array())
    {
        $rows = static::db()->select($sql, $params);
        if (empty($rows)) {
            return array();
        }

        $items = array();
        foreach ($rows as $row) {
            $item = new static();
            $item->setFromArray($row);

            $items[] = $item;
        }

        return $items;
    }

    public static function getListingFields()
    {
        $fields = array();
        foreach (static::$fields as $field => $config) {
            if (!isset($config['listing']) || !$config['listing']) {
                continue;
            }
            $fields[] = $field;
        }
        return $fields;
    }

    /**
     * Get the current environment singleton
     *
     * @return Pixie\Environment
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function environment()
    {
        return Environment::instance();
    }

    /**
     * Get a DB instance
     *
     * @return Pixie\DB\Connection
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected static function db()
    {
        return static::environment()->getDb();
    }

    protected $_id     = false;
    protected $_data   = array();
    protected $_errors = array();

    public function __construct()
    {
        foreach (static::$fields as $name => $field) {
            $this->_data[static::FullFieldname($name)] = null;
        }
    }

    public function __get($name)
    {
        if ('id' == $name) {
            return $this->_id;
        }
        $fieldName = static::FullFieldname($name);
        if (empty($this->_data[$fieldName]) && isset(static::$fields[$name]['default'])) {
            return static::$fields[$name]['default'];
        }

        return $this->_data[$fieldName];
    }

    public function __set($name, $value)
    {
        if ('id' == $name) {
            return false;
        }
        $fieldName = static::FullFieldname($name);
        $this->_data[$fieldName] = $value;
    }

    public function getKey()
    {
        if (!$this->isLoaded()) {
            return false;
        }

        return get_called_class() . '_' . $this->_id;
    }

    public function setFromArray($array)
    {
        if (!is_array($array)) {
            return false;
        }
        if (array_key_exists(static::IdColName(), $array)) {
            $this->_id = $array[static::IdColName()];
            unset($array[static::IdColName()]);
        }

        foreach ($array as $name => $value) {
            $fieldName = static::FullFieldname($name);
            $this->_data[$fieldName] = $value;
        }
    }

    public function getDataArray()
    {
        return $this->_data;
    }

    public function getListingData()
    {
        $listingFields = static::getListingFields();
        $data = array();
        foreach ($listingFields as $field) {
            $data[$field] = $this->_data[static::FullFieldname($field)];
        }
        return $data;
    }

    public function isLoaded()
    {
        return (false !== $this->_id);
    }

    public function addError($shortFieldname, $error)
    {
        $this->_errors[$shortFieldname] = $error;
    }

    public function hasErrors()
    {
        return (!empty($this->_errors));
    }

    /**
     * Get the current error array
     *
     * @return array
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    public function getFieldClasses($name)
    {
        $css = array();
        if (isset($this->_errors[$name])) {
            $css[] = 'error';
        }

        return implode(' ', $css);
    }

    public function prepareSave()
    {
        foreach (static::$fields as $name => $field) {
            $fieldName = static::FullFieldname($name);
            if (empty($this->_data[$fieldName]) && isset($field['default'])) {
                switch ($field['type']) {

                    case static::DATETIME:
                        $this->_data[$fieldName] = date('Y-m-d H:i:s', strtotime($field['default']));
                        break;

                    default:
                        $this->_data[$fieldName] = $field['default'];
                        break;

                }
            }
        }
    }

    public function beforeCreate()
    {}

    public function beforeSave()
    {
        foreach (static::$fields as $name => $field) {
            $fieldName = static::FullFieldname($name);

            if ((!isset($this->_data[$fieldName]) || empty($this->_data[$fieldName])) && isset($field['required']) && true === $field['required']) {
                $this->addError($name, $name . ' is required');
            }

            switch ($field['type']) {

                case static::SELECT:
                    if (isset($field['values']) && !in_array($this->_data[$fieldName], $field['values'])) {
                        $this->addError($name, 'Invalid value for ' . $name);
                        continue;
                    }
                    break;

                default:
                    break;

            }

        }

    }

    public function beforeDestroy()
    {}

    public function afterCreate()
    {}

    public function afterSave()
    {}

    public function afterDestroy()
    {}

    public function save($validate = true)
    {
        $this->prepareSave();

        if (!$this->isLoaded()) {
            $this->beforeCreate();
        }

        $this->beforeSave();
        if ($validate && $this->hasErrors()) {
            return false;
        }

        if (false == $this->isLoaded()) {
            if (false == static::db()->insert(static::TableName(), $this->_data)) {
                return false;
            }

            $this->_id = static::db()->insertId();

            $this->afterCreate();
        } else {
            $where  = static::IdColName() . ' = :itemId';
            $params = array('itemId' => $this->_id);

            if (false == static::db()->update(static::TableName(), $this->_data, $where, $params)) {
                return false;
            }
        }

        $this->afterSave();

        return true;
    }

    public function destroy()
    {
        if (!$this->isLoaded()) {
            return false;
        }

        $this->beforeDestroy();

        $where = static::IdColName() . ' = :itemId';
        $params = array('itemId' => $this->_id);

        if (false == static::db()->delete(static::TableName(), $where, $params)) {
            Pixie_Cli::Debug("Unable to delete item " . $this->getKey());

            return false;
        }

        $this->afterDestroy();

        return true;
    }
}
