<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            //'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'                   => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic'             => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.api'               => \App\Http\Middleware\ApiAuth::class,
        'bindings'               => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'                    => \Illuminate\Auth\Middleware\Authorize::class,
        'comment.owner'          => \App\Http\Middleware\CommentOwnerMiddleware::class,
        'discord'                => \App\Http\Middleware\DiscordMiddleware::class,
        'discord.token'          => \App\Http\Middleware\DiscordKeyMiddleware::class,
        'guest'                  => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'guild.member'           => \App\Http\Middleware\GuildMemberMiddleware::class,
        'guild.admin'            => \App\Http\Middleware\GuildAdminMiddleware::class,
        'guild.owner'            => \App\Http\Middleware\GuildOwnerMiddleware::class,
        'guild.event'            => \App\Http\Middleware\GuildEventMiddleware::class,
        'hook.owner'             => \App\Http\Middleware\HookOwnerMiddleware::class,
            'throttle'           => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
