<?php

namespace App\Events;

use App\Models\Ruc;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RucCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public function __construct($ruc)
    {
        $this->data = $ruc;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
