<?php

namespace App;

use App\Feed\FeedStorage;
use Riimu\Braid\Application\Container;
use Riimu\Kit\SecureRandom\SecureRandom;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DependencyLoader
{
    public static function loadFeedStorage(Container $container)
    {
        return new FeedStorage(
            $container->get('userRepository'),
            $container->get('packageRepository'),
            $container->get('historyRepository')
        );
    }

    public static function loadUserRepository(Container $container)
    {
        return self::loadRepository('App\Repository\UserRepository', $container);
    }

    public static function loadPackageRepository(Container $container)
    {
        return self::loadRepository('App\Repository\PackageRepository', $container);
    }

    public static function loadHistoryRepository(Container $container)
    {
        return self::loadRepository('App\Repository\HistoryRepository', $container);
    }

    private static function loadRepository($repository, Container $container)
    {
        $instance = new $repository($container->get('database'));
        $instance->setTablePrefix($container->get('database.prefix'));
        return $instance;
    }

    public static function loadSecureRandom()
    {
        return new SecureRandom();
    }

    public static function loadDatabase(Container $container)
    {
        $db = new \PDO(
            sprintf(
                "mysql:dbname=%s;host=%s;charset=utf8",
                $container->get('database.name'),
                $container->get('database.host')
            ),
            $container->get('database.user'),
            $container->get('database.pass'),
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '" . date('P') . "'",
            ]
        );

        $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        return $db;
    }
}
