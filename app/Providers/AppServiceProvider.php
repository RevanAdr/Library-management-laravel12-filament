<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Responses\LogoutResponse;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LogoutResponseContract::class,
            LogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
