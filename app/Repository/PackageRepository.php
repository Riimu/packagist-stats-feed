<?php

namespace App\Repository;

use App\Model\Package;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class PackageRepository extends BaseRepository
{
    protected $model = 'App\Model\Package';
    protected $record = 'App\Record\PackageRecord';

    public function findByUserId($userId)
    {
        return $this->findModels(['user_id' => ['=', $userId]]);
    }

    public function save(Package $package)
    {
        $this->saveModel($package);
    }
}
