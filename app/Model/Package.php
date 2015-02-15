<?php

namespace App\Model;

use App\Feed\ParsedPackage;
use App\Record\PackageRecord;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class Package extends BaseModel
{
    public function __construct(User $user, ParsedPackage $package)
    {
        parent::__construct(new PackageRecord());
        $this->record['user_id'] = $user->getId();
        $this->setFromParsedPackage($package);
    }

    public static function createFromRecord(PackageRecord $record)
    {
        return parent::constructFromRecord($record);
    }

    public function setFromParsedPackage(ParsedPackage $package)
    {
        $this->record->setValues([
            'name' => $package->getName(),
            'description' => $package->getDescription(),
            'downloads' => $package->getDownloads(),
            'stars' => $package->getStars(),
            'url' => $package->getUrl(),
        ]);
    }

    public function getId()
    {
        return (int) $this->record['id'];
    }

    public function getName()
    {
        return (string) $this->record['name'];
    }

    public function getDescription()
    {
        $description = $this->record['description'];
        return $description ===  null ? null : (string) $description;
    }

    public function getDownloads()
    {
        return (int) $this->record['downloads'];
    }

    public function getStars()
    {
        return (int) $this->record['stars'];
    }

    public function getUrl()
    {
        return (string) $this->record['url'];
    }
}
