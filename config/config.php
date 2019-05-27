<?php

return [

    'route_actions' => [
        'app' => [
            'guard'     => 'web',
            'guest'     => \App\Controllers\HomeController::class . '@__invoke',
            'auth'      => \App\Controllers\DashboardController::class . '@__invoke',
        ],
    ],

];
