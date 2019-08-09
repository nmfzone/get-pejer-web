<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Arr;
use App\Observers\GroupObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::macro('getClassNameAliasForMorph', function ($class) {
            if (is_object($class)) {
                $class = get_class($class);
            }

            return Arr::get(array_flip(Relation::morphMap()), $class, $class);
        });

        Relation::morphMap([
            'group' => Group::class,
            'user' => User::class,
        ]);

        Group::observe(GroupObserver::class);
    }
}
