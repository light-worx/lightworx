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
        if (Schema::hasTable('filament_settings')){
            Config::set('mail.default',setting('mailer', ['value' => 'smtp','label' => 'Mailer type','category' => 'Email','setting_type' => 'list','options' => ['smtp','mailgun','ses','postmark']]));
            Config::set('mail.mailers.' . setting('mailer') . '.host',setting('mail_host', ['label' => 'Host','category' => 'Email']));
            Config::set('mail.mailers.' . setting('mailer') . '.port',setting('mail_port', ['label' => 'Port','category' => 'Email']));
            Config::set('mail.mailers.' . setting('mailer') . '.username',setting('mail_username', ['label' => 'Username','category' => 'Email']));
            Config::set('mail.mailers.' . setting('mailer') . '.password',setting('mail_password', ['label' => 'Password','setting_type' => 'password','category' => 'Email']));
            Config::set('mail.mailers.' . setting('mailer') . '.encryption',setting('mail_encryption', ['value' => 'ssl','label' => 'Encryption','category' => 'Email','setting_type' => 'list','options' => ['ssl','tls']]));
            Config::set('mail.from.address',setting('mail_from_address', ['label' => 'From address','category' => 'Email']));
            Config::set('mail.from.name',setting('mail_from_name', ['label' => 'From name','category' => 'Email']));    
            Config::set('mail.reply_to.address',setting('mail_from_address', ['label' => 'Reply-to address','category' => 'Email']));
            Config::set('mail.reply_to.name',setting('mail_from_name', ['label' => 'Reply-to name','category' => 'Email']));
        }
    }
}
