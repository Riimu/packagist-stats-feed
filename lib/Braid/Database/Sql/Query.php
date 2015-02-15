<?php

namespace Riimu\Braid\Database\Sql;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class Query
{
    public function execute(\PDO $pdo)
    {
        $stmt = $pdo->prepare($this->buildSql($params));
        $stmt->execute($params);
        return $stmt;
    }

    abstract public function buildSql(& $params);

    protected function buildQueryValue($value, & $params)
    {
        if ($value === null) {
            $sql = 'NULL';
        } else {
            $sql = '?';
            $params[] = $value;
        }

        return $sql;
    }
}
