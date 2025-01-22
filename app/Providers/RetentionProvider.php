<?php

namespace App\Providers;

use App\Interfaces\retention\RetentionRepository;
use App\Services\retention\RetentionService;
use Illuminate\Support\ServiceProvider;

class RetentionProvider extends ServiceProvider
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
        $this->app->bind(RetentionRepository::class, RetentionService::class);
    }
}
