<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ponytail: extend PHP exec time only for HTTP requests (not artisan serve process itself)
        $middleware->append(\App\Http\Middleware\ExtendExecutionTime::class);

        $middleware->alias([
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'profile.completed' => \App\Http\Middleware\EnsureProfileCompleted::class,
            'seller.verified' => \App\Http\Middleware\EnsureSellerVerified::class,
            'order.participant' => \App\Http\Middleware\EnsureOrderParticipant::class,
            'conversation.participant' => \App\Http\Middleware\EnsureConversationParticipant::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
