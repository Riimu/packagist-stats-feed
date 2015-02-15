<?php

namespace Riimu\Braid\Database;

/**
 * Persistent storage handler for records.
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Repository
{
    /** @var string Prefix for the record database table names */
    private $tablePrefix = '';

    /** @var \PDO PDO instance for accessing the database */
    protected $pdo;

    /**
     * Creates a new instance of Repository.
     * @param \PDO $pdo The PDO instance used to access the database
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->tablePrefix = '';
    }

    /**
     * Sets the prefix used by the table names.
     * @param string $prefix The prefix used by the table names
     */
    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
    }

    /**
     * Inserts or updates the record, depending on whether it is a new record or not.
     *
     * If the record is inserted into the database and it has a single primary
     * key and it has not been set, the primary key will be set to the value
     * of the last insert id.
     *
     * @param Record $record The record to insert or update
     */
    protected function saveRecord(Record $record)
    {
        if ($record->isNew()) {
            $this->insertRecord($record);
            $record->setNew(false);

            $keys = $record->getPrimaryKeys();
            if (count($keys) === 1 && $record[$keys[0]] === null) {
                $record[$keys[0]] = $this->pdo->lastInsertId();
            }
        } else {
            $this->updateRecord($record);
        }
    }

    /**
     * Selects records from the database
     * @param string $record Name of the record class to retrieve
     * @param array $where The where definition
     * @param array|null $order The order of the results or null for no ordering
     * @param integer|null $limit Maximum number of results or null for no limit
     * @param integer|null $offset Offset for the records or null for no offset
     * @param array $with Definition for the additional records to return in the set
     * @return array Containing the returned records or arrays or record sets
     */
    protected function selectRecord($record, array $where, $order = null, $limit = null, $offset = null, array $with = [])
    {
        $query = new Sql\Select(
            $this->getRecordTable($record),
            $this->getRecordFields($record),
            new Sql\Where($where)
        );

        if ($order !== null) {
            $query->orderBy($order);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
        if ($offset !== null) {
            $query->offset($offset);
        }

        foreach ($with as $definition) {
            $join = $definition['record'];
            $query->join(
                $this->getRecordTable($join),
                $this->getRecordFields($join),
                $definition['on'],
                $definition['alias']
            );
        }

        $stmt = $query->execute($this->pdo);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = [];

        foreach ($stmt as $row) {
            $entry = new $record;
            $entry->setDatabaseValues($this->getPrefixedFields($row, 'this'));
            $entry->setNew(false);
            $set = [$entry];

            foreach ($with as $definition) {
                $join = new $definition['record']();
                $join->setDatabaseValues($this->getPrefixedFields($row, $definition['alias']));
                $join->setNew(false);
                $set[] = $join;
            }

            $result[] = count($set) === 1 ? $set[0] : $set;
        }

        return $result;
    }

    /**
     * Returns the fields from the database result row prefixed by a string.
     * @param array $row The result row
     * @param string $prefix The field prefix
     * @return array Array containing the unprefixed fields with their values
     */
    private function getPrefixedFields($row, $prefix)
    {
        $result = [];
        $prefix = $prefix . '_';
        $length = strlen($prefix);

        foreach ($row as $field => $value) {
            if (strncmp($prefix, $field, $length) === 0) {
                $result[substr($field, $length)] = $value;
            }
        }

        return $result;
    }

    /**
     * Inserts the record to the database.
     * @param Record $record Record to insert
     */
    protected function insertRecord(Record $record)
    {
        $query = new Sql\Insert($this->getRecordTable($record), $this->getRecordValues($record));
        $query->execute($this->pdo);
    }

    /**
     * Updates the record in the database.
     * @param Record $record Record to update
     */
    protected function updateRecord(Record $record)
    {
        list($values, $where) = $this->getRecordEntry($record);
        $query = new Sql\Update($this->getRecordTable($record), $values, new Sql\Where($where));
        $query->execute($this->pdo);
    }

    /**
     * Deletes the record from the database.
     * @param Record $record Record to delete
     */
    protected function deleteRecord(Record $record)
    {
        list(, $where) = $this->getRecordEntry($record);
        $query = new Sql\Delete($this->getRecordTable($record), Sql\Where($where));
        $query->execute($this->pdo);
    }

    /**
     * Returns the record database values and the primary key based where definition.
     * @param Record $record The record to use
     * @return array Array containing the record values and the where definition
     * @throws \UnexpectedValueException If the primary key fields are unset
     */
    protected function getRecordEntry(Record $record)
    {
        $values = $this->getRecordValues($record);
        $where = [];

        foreach ($this->getRecordKeys($record) as $key) {
            if (!isset($values[$key])) {
                throw new \UnexpectedValueException(
                    sprintf('Primary key values are unset for the record \'%s\'', get_class($record))
                );
            }

            $where[$key] = ['=', $values[$key]];
            unset($values[$key]);
        }

        return [$values, $where];
    }

    /**
     * Returns the prefixed table name for the record.
     * @param Record|string $record A record instance or name of the record class
     * @return string Prefix table name for the record
     * @throws \UnexpectedValueException If the record does not define the table name
     */
    protected function getRecordTable($record)
    {
        return $this->tablePrefix .
            $this->getFromRecord($record, 'getTable', 'The record \'%s\' does not define the database table', false);
    }

    /**
     * Returns the primary key fields for the record.
     * @param Record|string $record A record instance or name of the record class
     * @return string[] Primary key fields for the record
     * @throws \UnexpectedValueException If the record does not define any primary keys
     */
    protected function getRecordKeys($record)
    {
        return $this->getFromRecord($record, 'getPrimaryKeys', 'The record \'%s\' does not define any primary keys');
    }

    /**
     * Returns the field names for the record.
     * @param Record|string $record A record instance or name of the record class
     * @return string[] Fields names for the record
     * @throws \UnexpectedValueException If the record does not define any field names
     */
    protected function getRecordFields($record)
    {
        return $this->getFromRecord($record, 'getFields', 'The record \'%s\' does not define any database fields');
    }

    /**
     * Returns the fields values from the record.
     * @param Record $record The record to use
     * @return array The field values from the record
     * @throws \UnexpectedValueException if the record does not return any record values
     */
    protected function getRecordValues(Record $record)
    {
        return $this->getFromRecord($record, 'getDatabaseValues', 'The record \'%s\' does not return any database values');
    }

    /**
     * Validates and returns the requested information from the record.
     * @param Record|string $record A record instance or name of the record class
     * @param string $method Name of the method to use
     * @param string $error Error message to produce if the returned value is empty
     * @param boolean $isArray True to expect an array, false to expect a string
     * @return array|string The returned value from the method
     * @throws \UnexpectedValueException If the returned information is empty or incorrect type
     */
    private function getFromRecord($record, $method, $error, $isArray = true)
    {
        $result = $record instanceof Record ? $record->$method() : $record::$method();

        if (empty($result)) {
            throw new \UnexpectedValueException(sprintf($error, get_class($record)));
        } elseif ($isArray ? !is_array($result) : !is_string($result)) {
            throw new \UnexpectedValueException(
                sprintf('Unexpected returned value type from %s::%s()', get_class($record), $method)
            );
        }

        return $result;
    }
}
