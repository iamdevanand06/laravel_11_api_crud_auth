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
        // Token Expire Start
        Passport::personalAccessTokensExpireIn(Carbon::now()->addMinute(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        // Token Expire End
    }
}
