<?php

namespace App\Providers;

use App\Interfaces\debitNote\DebitNoteRepository;
use App\Services\debitNote\DebitNoteService;
use Illuminate\Support\ServiceProvider;

class DebitNoteProvider extends ServiceProvider
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
        $this->app->bind(DebitNoteRepository::class, DebitNoteService::class);
    }
}
