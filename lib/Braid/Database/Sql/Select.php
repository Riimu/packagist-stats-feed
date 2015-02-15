<?php

namespace Riimu\Braid\Database\Sql;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Select extends Query
{
    private $table;
    private $fields;
    private $where;
    private $order;
    private $limit;
    private $offset;
    private $joins;

    public function __construct($table, array $fields, Where $where)
    {
        $this->table = $table;
        $this->fields = $fields;
        $this->where = $where;
        $this->joins = [];
    }

    public function orderBy(array $order)
    {
        $this->order = $order;
    }

    public function limit($limit)
    {
        $this->limit = (int) $limit;
    }

    public function offset($offset)
    {
        $this->offset = (int) $offset;
    }

    public function join($table, array $fields, $on, $alias)
    {
        $this->joins[] = [
            'table' => $table,
            'fields' => $fields,
            'on' => $on,
            'alias' => $alias
        ];
    }

    public function buildSql(& $params)
    {
        $fields = $this->buildFields($this->fields, 'this');
        $from = sprintf('`%s` AS `this`', $this->table);

        foreach ($this->joins as $join) {
            $fields = array_merge($fields, $this->buildFields($join['fields'], $join['alias']));
            $from .= sprintf(' INNER JOIN `%s` AS `%s` ON %s', $join['table'], $join['alias'], $join['on']);
        }

        return sprintf(
            'SELECT %s FROM %s WHERE %s%s%s',
            implode(', ', $fields),
            $from,
            $this->where->buildConditions($params),
            $this->buildOrderBy(),
            $this->buildLimit()
        );
    }

    private function buildOrderBy()
    {
        if (!isset($this->order)) {
            return '';
        }

        $clauses = [];

        foreach ($this->order as $field => $direction) {
            $field = '`' . implode('`.`', explode('.', $field, 2)) . '`';

            if (in_array($direction, ['ASC', 'asc', true, 1], true)) {
                $direction = 'ASC';
            } elseif (in_array($direction, ['DESC', 'desc', false, 0], true)) {
                $direction = 'DESC';
            } else {
                throw new \RuntimeException("Invalid sort direction '$direction'");
            }

            $clauses[] = $field . ' ' . $direction;
        }

        return ' ORDER BY ' . implode(', ', $clauses);
    }

    private function buildLimit()
    {
        if (isset($this->offset)) {
            return sprintf(' LIMIT %d, %d', $this->offset, isset($this->limit) ? $this->limit : PHP_INT_MAX);
        } elseif (isset($this->limit)) {
            return sprintf(' LIMIT %d', $this->limit);
        }

        return '';
    }

    private function buildFields(array $fields, $alias)
    {
        foreach ($fields as $key => $field) {
            $fields[$key] = sprintf('`%s`.`%s` AS `%s_%s`', $alias, $field, $alias, $field);
        }

        return $fields;
    }
}
