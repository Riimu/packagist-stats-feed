<?php

namespace App\Record;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PackageRecord extends BaseRecord
{
    protected static $table = 'packages';
    protected static $fields = ['id', 'user_id', 'name', 'description', 'url', 'downloads', 'stars'];
}
