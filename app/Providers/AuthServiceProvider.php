<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::cookie(Str::slug(env('APP_NAME', 'laravel'), '_') . '_token');

        Passport::tokensExpireIn(Carbon::now()->addYears(1));
        Passport::refreshTokensExpireIn(Carbon::now()->addYears(1)->addDays(10));
    }
}
