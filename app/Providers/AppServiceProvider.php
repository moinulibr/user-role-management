<?php

namespace App\Providers;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AuthorizePermission;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

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
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permission', AuthorizePermission::class);
    }
}
