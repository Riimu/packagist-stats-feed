<?php

namespace Riimu\Braid\Database\Sql;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Delete extends Query
{
    private $table;
    private $where;

    public function __construct($table, Where $where)
    {
        $this->table = $table;
        $this->where = $where;
    }

    public function buildSql(& $params)
    {
        return sprintf(
            'DELETE FROM `%s` WHERE %s',
            $this->table,
            $this->where->buildConditions($params)
        );
    }
}
