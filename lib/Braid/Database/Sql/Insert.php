<?php

namespace Riimu\Braid\Database\Sql;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Insert extends Query
{
    private $table;
    private $fields;
    private $values;

    public function __construct($table, array $values)
    {
        $this->table = $table;
        $this->fields = array_keys($values);
        $this->values = array_values($values);
    }

    public function buildSql(& $params)
    {
        $params = [];

        return sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $this->table,
            implode('`, `', $this->fields),
            $this->buildInsertValues($this->values, $params)
        );
    }

    private function buildInsertValues($values, & $params)
    {
        $fields = [];

        foreach ($values as $value) {
            $fields[] = $this->buildQueryValue($value, $params);
        }

        return implode(', ', $fields);
    }
}
