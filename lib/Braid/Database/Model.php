<?php

namespace Riimu\Braid\Database;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2014, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Model
{
    /** @var Record Database record for the model */
    protected $record;

    protected function __construct(Record $record)
    {
        $this->record = $record;
    }

    protected static function constructFromRecord(Record $record)
    {
        $model = (new \ReflectionClass(get_called_class()))->newInstanceWithoutConstructor();
        $model->record = $record;
        return $model;
    }

    public function getRecord()
    {
        return $this->record;
    }
}
