<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class AppServiceProvider extends ServiceProvider { public function register(): void {} public function boot(): void {
    Route::get('/', fn () => response()->json([
        'service' => 'TaskFlow Laravel API',
        'status' => 'ok',
        'api' => '/api',
        'health' => '/api/health',
    ]));
    Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
} }
