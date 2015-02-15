<?php

namespace App\Repository;

use App\Model\History;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class HistoryRepository extends BaseRepository
{
    protected $model = 'App\Model\History';
    protected $record = 'App\Record\HistoryRecord';

    public function findLatestByIds($ids, $count)
    {
        return $this->findModels(['package_id' => ['IN', $ids]], ['date' => 'DESC'], $count);
    }

    public function save(History $history)
    {
        $this->saveModel($history);
    }
}
