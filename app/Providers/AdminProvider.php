<?php

namespace App\Providers;

use App\Interfaces\admin\AdminRepository;
use App\Services\admin\AdminService;
use Illuminate\Support\ServiceProvider;

class AdminProvider extends ServiceProvider
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
        $this->app->bind(AdminRepository::class, AdminService::class);
    }
}
