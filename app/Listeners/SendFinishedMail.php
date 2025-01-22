<?php

namespace App\Listeners;

use App\Events\FinishedSincronized;
use App\Mail\FinishedSyncMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendFinishedMail
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
    public function handle(FinishedSincronized $event): void
    {
        try{
            $mail = new FinishedSyncMail($event->message);
            Mail::to($event->email)->send($mail);
        }catch (\Exception $e){
            Log::error('Error al enviar correo de finalización de sincronización: '.$e->getMessage());
        }
    }
}
