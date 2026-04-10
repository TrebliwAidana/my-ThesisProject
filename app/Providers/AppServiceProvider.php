<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Services\ThemeService;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
            if (config('app.env') === 'production') {
        URL::forceScheme('https');
        }
        View::composer('*', function ($view) {
            $view->with('themeColor', ThemeService::current());
        });
    }
}