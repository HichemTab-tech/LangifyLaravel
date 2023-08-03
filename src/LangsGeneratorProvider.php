<?php

namespace HichemtabTech\LangifyLaravel;

use Illuminate\Support\ServiceProvider;

class LangsGeneratorProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $consolePath = __DIR__.'/Console/Commands';
        $publishPath = app_path('Console/Commands');
        $this->publishes([$consolePath => $publishPath], 'hichemtab-tech-langify-laravel');
    }
}