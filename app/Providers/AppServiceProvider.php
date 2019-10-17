<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Observers\ChatObserver;
use App\Observers\GroupObserver;
use App\Garage\ClientSide\ClientSide;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use App\Garage\ClientSide\Facades\ClientSide as ClientSideFacade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ClientSide::class, function ($app) {
            return new ClientSide();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerSharedData();

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
        Chat::observe(ChatObserver::class);

        QueryBuilder::macro('toRawSql', function () {
            return array_reduce($this->getBindings(), function ($sql, $binding) {
                return preg_replace(
                    '/\?/',
                    is_numeric($binding) ? $binding : "'".$binding."'" ,
                    $sql,
                    1
                );
            }, $this->toSql());
        });

        QueryBuilder::macro('rawQuery', function (string $query) {
            if (Str::startsWith(strtolower($query), 'select ')) {
                $query = Str::replaceFirst('select ', '', $query);
            }

            return $this->selectRaw($query);
        });

        Builder::macro('toRawSql', function () {
            return $this->getQuery()->toRawSql();
        });
    }

    /**
     * Register shared data.
     *
     * @return void
     */
    protected function registerSharedData()
    {
        ClientSideFacade::share('oldInput', function () {
            $oldInput = Arr::wrap(request()->session()->getOldInput());
            return Arr::except($oldInput, '_token');
        });
        ClientSideFacade::share('errors', function () {
            /** @var \Illuminate\Support\ViewErrorBag $errors */
            $errors = request()->session()->get('errors');

            return $errors ? $errors->toArray() : [];
        });
        ClientSideFacade::share('baseUrl', url('/'));
        ClientSideFacade::share('lang', str_replace('_', '-', $this->app->getLocale()));
        ClientSideFacade::share('user', function () {
            return auth()->user();
        });
    }
}
