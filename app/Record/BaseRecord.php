<?php

namespace App\Record;

use Riimu\Braid\Database\Record;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class BaseRecord extends Record
{
    protected static $primaryKeys = ['id'];

    public function getDatabaseValues()
    {
        if ($this->isNew()) {
            $this->setTimestampField('created', new \DateTime());
        }

        return parent::getDatabaseValues();
    }
    public static function getFields()
    {
        return array_merge(parent::getFields(), ['created', 'updated']);
    }
}
