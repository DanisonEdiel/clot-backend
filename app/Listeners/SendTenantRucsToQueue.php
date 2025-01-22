<?php

namespace App\Listeners;

use App\Events\TenantRucsSend;
use App\Jobs\RabbitSendTenantRucsQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTenantRucsToQueue
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
    public function handle(TenantRucsSend $event): void
    {
        RabbitSendTenantRucsQueue::dispatch($event->ruc);
    }
}
