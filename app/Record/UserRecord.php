<?php

namespace App\Record;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class UserRecord extends BaseRecord
{
    protected static $table = 'users';
    protected static $fields = ['id', 'name'];
}
