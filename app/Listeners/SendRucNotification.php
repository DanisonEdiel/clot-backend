<?php

namespace App\Listeners;

use App\Events\RucCreated;
use App\Jobs\RabbitRucCreated;

class SendRucNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RucCreated $event): void
    {
        RabbitRucCreated::dispatch($event->data);
    }
}
