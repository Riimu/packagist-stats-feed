<?php return [
    'kit.securerandom' => ['App\DependencyLoader', 'loadSecureRandom'],
    'database' => ['App\DependencyLoader', 'loadDatabase'],

    'feedStorage' => ['App\DependencyLoader', 'loadFeedStorage'],

    'userRepository' => ['App\DependencyLoader', 'loadUserRepository'],
    'packageRepository' => ['App\DependencyLoader', 'loadPackageRepository'],
    'historyRepository' => ['App\DependencyLoader', 'loadHistoryRepository'],
] + (require __DIR__ . '/config.php') + (require __DIR__ . '/database.php');