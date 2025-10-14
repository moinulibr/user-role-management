<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    // ... (অন্যান্য কোড, যেমন: HOME প্রপার্টি) ...

    public function boot(): void
    {
        // Alias রেজিস্ট্রেশনের লজিকটি এখান থেকে সরিয়ে দিন
        // শুধু ডিফল্ট রুট কনফিগারেশন রাখুন:
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        parent::boot();
    }
}
