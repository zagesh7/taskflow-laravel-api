<?php
namespace App\Providers;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
class RouteServiceProvider extends ServiceProvider {
    public const HOME = '/';
    public function boot(): void {
        RateLimiter::for('api', fn($request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        $this->routes(function () { Route::middleware('api')->prefix('api')->group(base_path('routes/api.php')); });
    }
}
