<?php

namespace App\Model;

use App\Record\UserRecord;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class User extends BaseModel
{
    public function __construct($name)
    {
        parent::__construct(new UserRecord());
        $this->record['name'] = (string) $name;
    }

    public static function createFromRecord(UserRecord $record)
    {
        return parent::constructFromRecord($record);
    }

    public function getId()
    {
        return (int) $this->record['id'];
    }
}
