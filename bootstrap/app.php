<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        // $middleware->alias([
        //     'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        //     'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        //     'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        // ]);
        $middleware->alias([
            'global.checklist' => \App\Http\Middleware\CheckGlobalChecklistAccess::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
            // Ambil URL lengkap saat ini (misal: http://app1.sso.test/laporan-gaji)
            $currentUrl = $request->fullUrl();

            // Encode agar aman saat dikirim lewat URL
            $encodedUrl = urlencode($currentUrl);

            // Lempar ke Portal dengan parameter 'redirect_to'
            return env('APP_PORTAL_URL') . 'login?redirect_to=' . $encodedUrl;
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
