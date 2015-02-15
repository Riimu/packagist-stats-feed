<?php

namespace App\Record;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class HistoryRecord extends BaseRecord
{
    protected static $table = 'history';
    protected static $fields = ['id', 'package_id', 'downloads', 'stars', 'new_downloads', 'new_stars', 'date'];
}
