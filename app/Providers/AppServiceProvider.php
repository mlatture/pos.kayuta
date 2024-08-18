<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
         Schema::defaultStringLength(191);
         Blade::directive('hasPermission', function (string $permission) {
             return "<?php if(auth()->user()->hasPermission($permission)): ?>";
         });
         Blade::directive('endHasPermission', function () {
             return "<?php endif; ?>";
         });

//         if (! $this->app->runningInConsole()) {
//             // 'key' => 'value'
//             $settings = Setting::all('key', 'value')
//                 ->keyBy('key')
//                 ->transform(function ($setting) {
//                     return $setting->value;
//                 })
//                 ->toArray();
//             config([
//                'settings' => $settings
//             ]);
//
//             config(['app.name' => config('settings.app_name')]);
//         }

         Paginator::useBootstrap();
     }
}
