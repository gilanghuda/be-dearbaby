<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
    'api' => [
        \Illuminate\Http\Middleware\HandleCors::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];


    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        'CustomAuthMiddleware' => \App\Http\Middleware\CustomAuthMiddleware::class,
    ];
}