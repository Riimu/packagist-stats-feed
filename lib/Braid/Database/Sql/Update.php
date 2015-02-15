<?php

namespace Riimu\Braid\Database\Sql;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Update extends Query
{
    private $table;
    private $values;
    private $where;

    public function __construct($table, array $values, Where $where)
    {
        $this->table = $table;
        $this->values = $values;
        $this->where = $where;
    }

    public function buildSql(& $params)
    {
        return sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $this->table,
            $this->buildSetValues($this->values, $params),
            $this->where->buildConditions($params)
        );
    }

    private function buildSetValues(array $values, & $params)
    {
        $fields = [];

        foreach ($values as $name => $value) {
            $fields[] = sprintf('`%s` = %s', $name, $this->buildQueryValue($value, $params));
        }

        return implode(', ', $fields);
    }
}
