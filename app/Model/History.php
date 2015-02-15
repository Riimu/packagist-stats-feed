<?php

namespace App\Model;

use App\Record\HistoryRecord;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class History extends BaseModel
{
    private $package;

    public function __construct(Package $package, $downloads, $stars)
    {
        parent::__construct(new HistoryRecord());
        $this->record['package_id'] = $package->getId();
        $this->record['downloads'] = $package->getDownloads();
        $this->record['stars'] = $package->getStars();
        $this->record['new_downloads'] = $downloads;
        $this->record['new_stars'] = $stars;
        $this->record->setTimestampField('date', new \DateTime());
    }

    public static function createFromRecord(HistoryRecord $record)
    {
        return parent::constructFromRecord($record);
    }

    public function setPackage(Package $package)
    {
        if ($package->getId() !== $this->getPackageId()) {
            throw new \InvalidArgumentException('Wrong owner package provided');
        }

        $this->package = $package;
    }

    public function getPackage()
    {
        if (!isset($this->package)) {
            throw new \RuntimeException('Owner package has not been set');
        }

        return $this->package;
    }

    public function getId()
    {
        return (int) $this->record['id'];
    }

    public function getPackageId()
    {
        return (int) $this->record['package_id'];
    }

    public function getDownloads()
    {
        return (int) $this->record['downloads'];
    }

    public function getNewDownloads()
    {
        $value = $this->record['new_downloads'];
        return $value === null ? null : (int) $value;
    }

    public function getStars()
    {
        return (int) $this->record['stars'];
    }

    public function getNewStars()
    {
        $value = $this->record['new_stars'];
        return $value === null ? null : (int) $value;
    }

    public function getDate()
    {
        return $this->record->getTimestampField('date');
    }
}
