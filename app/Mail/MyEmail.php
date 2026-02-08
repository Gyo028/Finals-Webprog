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
    public $bookingId;
    public string $eventDate; // New
    public string $eventType; // New

    public function __construct(array $data)
    {
        $this->clientName = $data['clientName'] ?? 'Client';
        $this->status     = $data['status'] ?? 'pending';
        $this->remarks    = $data['remarks'] ?? null;
        $this->bookingId  = $data['bookingId'] ?? null;
        $this->eventDate  = $data['eventDate'] ?? 'TBD'; // New
        $this->eventType  = $data['eventType'] ?? 'Event'; // New
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking_status', 
            with: [
                'clientName' => $this->clientName,
                'status'     => $this->status,
                'remarks'    => $this->remarks,
                'bookingId'  => $this->bookingId,
                'eventDate'  => $this->eventDate, // New
                'eventType'  => $this->eventType, // New
            ]
        );
    }
}