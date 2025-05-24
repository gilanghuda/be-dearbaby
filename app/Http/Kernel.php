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
        'web' => [
            // ...existing code...
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // 'throttle:api',
            // ...existing code...
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'CustomAuthMiddleware' => \App\Http\Middleware\CustomAuthMiddleware::class,
    ];
}