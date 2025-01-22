<?php

namespace App\Listeners;

use App\Events\RucSend;
use App\Jobs\RabbitRucToQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRucToQueue
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
    public function handle(RucSend $event): void
    {
        RabbitRucToQueue::dispatch($event->data);
    }
}
