<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
        if (env('APP_ENV') != 'local') {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addMinute(15));
            Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        } else {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addHour());
            Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        }
        // Token Expire End
    }
}
