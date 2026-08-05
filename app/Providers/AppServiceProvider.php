<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
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
        Gate::before(function ($user, $ability) {
            return ($user->hasRole('super_admin') || $user->hasRole('admin')) ? true : null;
        });

        ResetPassword::createUrlUsing(fn (object $notifiable, string $token) => route('matching.password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));

        // Batch-load all site settings in a single query
        // This prevents N+1 queries from site_setting() calls in views
        try {
            SiteSetting::loadAll();
        } catch (\Exception $e) {
            // Silently fail if DB is not available (e.g. during migrations)
        }
    }
}
