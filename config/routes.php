<?php return [
    '/' => [
        'controller' => 'App\Controller\IndexController',
        'method' => 'indexAction',
    ],
    '/info/' => [
        'controller' => 'App\Controller\IndexController',
        'method' => 'infoAction',
    ],
    '/feed/' => [
        'params' => 1,
        'controller' => 'App\Controller\FeedController',
        'method' => 'feedAction',
    ]
];