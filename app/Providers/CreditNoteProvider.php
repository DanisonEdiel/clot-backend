<?php

namespace App\Providers;

use App\Interfaces\creditNote\CreditNoteRepository;
use App\Services\creditNote\CreditNoteService;
use Illuminate\Support\ServiceProvider;

class CreditNoteProvider extends ServiceProvider
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
        $this->app->bind(CreditNoteRepository::class, CreditNoteService::class);
    }
}
