<?php

namespace Riimu\Braid\Database;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Record implements \ArrayAccess
{
    protected static $table = '';
    protected static $fields = [];
    protected static $primaryKeys = [];

    private $new;
    private $values;

    public function __construct()
    {
        $this->new = true;
        $this->values = array_fill_keys(static::getFields(), null);
    }

    public function setNew($state)
    {
        $this->new = (bool) $state;
    }

    public function isNew()
    {
        return $this->new;
    }

    public function setValue($field, $value)
    {
        if (!array_key_exists($field, $this->values)) {
            throw new \InvalidArgumentException("Invalid record field '$field'");
        }

        $this->values[$field] = $value;
    }

    public function setValues(array $values)
    {
        array_map([$this, 'setValue'], array_keys($values), array_values($values));
    }

    public function getValue($field)
    {
        if (!array_key_exists($field, $this->values)) {
            throw new \InvalidArgumentException("Invalid record field '$field'");
        }

        return $this->values[$field];
    }

    public function setDatabaseValues(array $values)
    {
        if (array_diff_key($values, $this->values) !== []) {
            throw new \InvalidArgumentException("Database values contain invalid fields");
        }

        foreach ($values as $key => $value) {
            $this->values[$key] = $value;
        }
    }

    public function getDatabaseValues()
    {
        return $this->values;
    }

    public function setTimestampField($field, \DateTime $date = null)
    {
        $this->setValue($field, $date === null ? null : date('Y-m-d H:i:s', $date->getTimestamp()));
    }

    public function getTimestampField($field)
    {
        return ($timestamp = $this->getValue($field)) === null ? null : new \DateTime($timestamp);
    }

    public static function getTable()
    {
        return static::$table;
    }

    public static function getFields()
    {
        return static::$fields;
    }

    public static function getPrimaryKeys()
    {
        return static::$primaryKeys;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->values);
    }

    public function offsetGet($offset)
    {
        return $this->getValue($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->setValue($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->setValue($offset, null);
    }
}
