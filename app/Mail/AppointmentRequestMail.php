<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $doctorName;
    public $childName;
    public $appointmentDate;
    public $appointmentTime;
    public $appointmentID;
    public $nurseName;

    /**
     * Create a new message instance.
     */
    public function __construct($doctorName, $childName, $appointmentDate, $appointmentTime, $appointmentID, $nurseName)
    {
        $this->doctorName = $doctorName;
        $this->childName = $childName;
        $this->appointmentDate = $appointmentDate;
        $this->appointmentTime = $appointmentTime;
        $this->appointmentID = $appointmentID;
        $this->nurseName = $nurseName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Appointment Request - Action Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.AppointmentRequest',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}


