<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Strict Eloquent in development: Prevents N+1 queries, silently discarded fields, and accessing missing attributes
        Model::shouldBeStrict(! $this->app->isProduction());

        // Explicit unguard protection: Always rely on $fillable in models
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // Configure API Rate Limiting (60 requests/min per IP or 120 per authenticated user)
        RateLimiter::for('api', function (Request $request): Limit {
            return $request->user()
                ? Limit::perMinute(120)->by((string) $request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });

        // Ensure Windows environment variables are passed through so php artisan serve works seamlessly on Windows
        if (class_exists(\Illuminate\Foundation\Console\ServeCommand::class)) {
            \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables = array_values(array_unique(array_merge(
                \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables,
                array_keys($_ENV),
                array_keys($_SERVER)
            )));
        }
    }
}
