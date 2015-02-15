<?php

namespace App\Repository;

use App\Model\User;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class UserRepository extends BaseRepository
{
    protected $model = 'App\Model\User';
    protected $record = 'App\Record\UserRecord';

    public function findByName($name)
    {
        return current($this->findModels(['name' => ['=', $name]]));
    }

    public function save(User $user)
    {
        return $this->saveModel($user);
    }
}
