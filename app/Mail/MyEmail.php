<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $status;
    public ?string $remarks;

    public function __construct(array $data)
    {
        $this->clientName = $data['clientName'] ?? 'Client';
        $this->status     = $data['status'] ?? 'pending';
        $this->remarks    = $data['remarks'] ?? null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Status Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_status',
            with: [
                'clientName' => $this->clientName,
                'status'     => $this->status,
                'remarks'    => $this->remarks,
            ]
        );
    }
}
