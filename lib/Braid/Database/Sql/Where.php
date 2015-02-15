<?php

namespace Riimu\Braid\Database\Sql;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Where
{
    private $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function buildConditions(& $params)
    {
        $conditions = [];

        foreach ($this->conditions as $field => $definition) {
            $conditions[] = $this->buildWhereValue($field, $definition[0], $definition[1], $params);
        }

        return implode(' AND ', $conditions);
    }

    private function buildWhereValue($field, $operator, $value, & $params)
    {
        $field = '`' . implode('`.`', explode('.', $field, 2)) . '`';

        switch ((string) $operator) {
            case '=':
                if ($value === null) {
                    return $field . ' IS NULL';
                }

                $params[] = $value;
                return $field . ' = ?';
            case '<':
                $params[] = $value;
                return $field . ' < ?';
            case 'IN':
                foreach ($value as $option) {
                    $params[] = $option;
                }
                return sprintf('%s IN (%s)', $field, implode(', ', array_fill(0, count($value), '?')));
            default:
                throw new \InvalidArgumentException("Unknown where operator '$operator'");
        }
    }
}
