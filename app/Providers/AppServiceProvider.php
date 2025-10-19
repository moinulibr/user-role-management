<?php

namespace App\Providers;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AuthorizePermission;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\View;
use App\View\Composers\SidebarComposer;

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
        // এখানে View Composer রেজিস্ট্রেশন করা হচ্ছে
        // 'layouts.sidebar' ভিউটি রেন্ডার হওয়ার আগে SidebarComposer ক্লাসটি রান হবে।
        View::composer('layouts.sidebar', SidebarComposer::class);

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('permission', AuthorizePermission::class);
    }
}
