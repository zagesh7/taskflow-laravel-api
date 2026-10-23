<?php
namespace App\Http;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
class Kernel extends HttpKernel {
    protected $middleware = [\Illuminate\Http\Middleware\HandleCors::class, \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class];
    protected $middlewareGroups = ['api' => [\Illuminate\Routing\Middleware\SubstituteBindings::class]];
    protected $middlewareAliases = ['auth' => \Illuminate\Auth\Middleware\Authenticate::class, 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, 'role' => \App\Http\Middleware\RequireRole::class];
}
