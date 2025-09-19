<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        $modules = config('constants.role_modules', []);



        Gate::define('reservation-management', function ($user) {
            return $user->hasPermission('reservation_mgmt');
        });

        foreach ($modules as $key => $mod) {
            Gate::define($mod['value'], function ($user) use ($mod) {
                return $user->hasPermission($mod['value']);
            });
        }
        //
    }
}
