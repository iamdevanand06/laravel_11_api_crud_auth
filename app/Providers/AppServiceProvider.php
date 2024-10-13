<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\Carbon;


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
        // $this->registerPolicies();

        // Passport::routes();
        Passport::personalAccessTokensExpireIn(Carbon::now()->addMinute(5));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
    }
}
