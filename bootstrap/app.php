<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckLogin;
use App\Http\Middleware\NoCache;
use App\Http\Middleware\CheckPermission; // 1. تأكد من استدعاء الـ Middleware الجديد هنا

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {

        $middleware->alias([
            'no.cache' => NoCache::class,
            'check.login' => CheckLogin::class,
            'check_perm' => CheckPermission::class, // 2. أضفنا اسم المستعار للمصفوفة هنا
        ]);
    
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();