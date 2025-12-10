<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AdminRoute;
use App\Livewire\Search;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach (glob(__DIR__ . '/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('adminonly', AdminRoute::class);
        Schema::defaultStringLength(191);
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'lightworx');
        Paginator::useBootstrapFive();
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');
        Livewire::component('search', Search::class);
        Blade::componentNamespace('App\\Resources\\Views\\Components', 'lightworx');
        Config::set('auth.providers.users.model','App\Models\User');
        Relation::morphMap([
            'invoice' => 'App\Models\Invoice',
            'quote' => 'App\Models\Quote'
        ]);
        Config::set('mail.default',setting('mailer'));
        Config::set('mail.mailers.' . setting('mailer') . '.host',setting('mail_host'));
        Config::set('mail.mailers.' . setting('mailer') . '.port',setting('mail_port'));
        Config::set('mail.mailers.' . setting('mailer') . '.username',setting('mail_username'));
        Config::set('mail.mailers.' . setting('mailer') . '.password',setting('mail_password'));
        Config::set('mail.mailers.' . setting('mailer') . '.encryption',setting('mail_encryption'));
        Config::set('mail.from.address',setting('mail_from_address'));
        Config::set('mail.from.name',setting('mail_from_name'));    
        Config::set('mail.reply_to.address',setting('mail_from_address'));
        Config::set('mail.reply_to.name',setting('mail_from_name'));
    }
}
