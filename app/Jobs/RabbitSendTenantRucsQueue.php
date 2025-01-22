<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RabbitSendTenantRucsQueue implements ShouldQueue
{
    use Queueable;

    public $ruc;

    /**
     * Create a new job instance.
     */
    
    public function __construct($ruc)
    {
        $this->queue = 'ruc.queues';
        $this->ruc = $ruc;

    }

    /**
     * Execute the job.
     */
    public function handle(): void{}
}
