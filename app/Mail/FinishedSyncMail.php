<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FinishedSyncMail extends Mailable
{
    use Queueable, SerializesModels;

    private $message;
    /**
     * Create a new message instance.
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->view('emails.finished_sync_email')
            ->with([
                'sexo' => $this->message,
            ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
