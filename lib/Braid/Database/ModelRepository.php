<?php

namespace Riimu\Braid\Database;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class ModelRepository extends Repository
{
    /** @var string Name of the record class used by the repository */
    protected $record;

    /** @var string Name of the model class used by the repository */
    protected $model;

    protected function findModels(array $where, $order = null, $limit = null, $offset = null)
    {
        return array_map(
            [$this->model, 'createFromRecord'],
            $this->selectRecord($this->record, $where, $order, $limit, $offset)
        );
    }

    protected function saveModel(Model $model)
    {
        $this->saveRecord($model->getRecord());
    }
}
