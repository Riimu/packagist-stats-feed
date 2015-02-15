<?php

namespace App\Feed;

use App\Model\History;
use App\Model\Package;
use App\Model\User;
use App\Repository\UserRepository;
use App\Repository\PackageRepository;
use App\Repository\HistoryRepository;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FeedStorage
{
    private $userRepository;
    private $packageRepository;
    private $historyRepository;

    private $storedPackages;

    public function __construct(
        UserRepository $userRepository,
        PackageRepository $packageRepository,
        HistoryRepository $historyRepository
    ) {
        $this->userRepository = $userRepository;
        $this->packageRepository = $packageRepository;
        $this->historyRepository = $historyRepository;
    }

    public function store($name, array $packages)
    {
        $user = $this->userRepository->findByName($name);

        if (!$user) {
            $user = new User($name);
            $this->userRepository->save($user);
        }

        $storedPackages = $this->packageRepository->findByUserId($user->getId());
        $updates = false;

        foreach ($storedPackages as $storedPackage) {
            if (($key = $this->findPackage($storedPackage->getName(), $packages)) === false) {
                continue;
            }

            if ($this->hasUpdated($packages[$key], $storedPackage)) {
                $downloads = $packages[$key]->getDownloads() - $storedPackage->getDownloads();
                $stars = $packages[$key]->getStars() - $storedPackage->getStars();

                $storedPackage->setFromParsedPackage($packages[$key]);
                $this->packageRepository->save($storedPackage);

                $history = new History($storedPackage, $downloads, $stars);
                $this->historyRepository->save($history);

                $updates = true;
            }

            unset($packages[$key]);
        }

        foreach ($packages as $package) {
            $storedPackage = new Package($user, $package);
            $this->packageRepository->save($storedPackage);
            $storedPackages[] = $storedPackage;
            $updates = true;

            $history = new History($storedPackage, null, null);
            $this->historyRepository->save($history);
        }

        $this->storedPackages = $storedPackages;

        return $updates;
    }

    private function findPackage($name, array $packages)
    {
        foreach ($packages as $key => $package) {
            if ($package->getName() === $name) {
                return $key;
            }
        }

        return false;
    }

    private function hasUpdated($parsed, $stored)
    {
        if ($parsed->getDownloads() !== $stored->getDownloads()) {
            return true;
        } elseif ($parsed->getStars() !== $stored->getStars()) {
            return true;
        }

        return false;
    }

    public function getEntries()
    {
        $byId = [];
        $ids = array_map(function (Package $package) use (& $byId) {
            $id = $package->getId();
            $byId[$id] = $package;
            return $id;
        }, $this->storedPackages);

        $entries = $this->historyRepository->findLatestByIds($ids, max(count($ids), 50));

        array_map(function (History $history) use ($byId) {
            $history->setPackage($byId[$history->getPackageId()]);
        }, $entries);

        return $entries;
    }
}
