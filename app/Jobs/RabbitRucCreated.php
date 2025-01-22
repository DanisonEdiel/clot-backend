<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue as BaseRabbitMQQueue;

class RabbitRucCreated implements ShouldQueue
{
    use Queueable;

    private $data;

    public function __construct($ruc)
    {
        $this->queue = 'ruc.created';
        $this->data = $ruc;
    }
    public function handle(): void {}
}
