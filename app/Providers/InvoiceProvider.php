<?php

namespace App\Providers;

use App\Interfaces\invoice\InvoiceRepository;
use App\Services\invoice\InvoiceService;
use Illuminate\Support\ServiceProvider;

class InvoiceProvider extends ServiceProvider
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
        $this->app->bind(InvoiceRepository::class, InvoiceService::class);
    }
}
