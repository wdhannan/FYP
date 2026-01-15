<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $parentName;
    public $childName;
    public $doctorName;
    public $appointmentDate;
    public $appointmentTime;
    public $appointmentID;

    /**
     * Create a new message instance.
     */
    public function __construct($parentName, $childName, $doctorName, $appointmentDate, $appointmentTime, $appointmentID)
    {
        $this->parentName = $parentName;
        $this->childName = $childName;
        $this->doctorName = $doctorName;
        $this->appointmentDate = $appointmentDate;
        $this->appointmentTime = $appointmentTime;
        $this->appointmentID = $appointmentID;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Upcoming Appointment Reminder - Smart Child Care',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.AppointmentReminder',
        );
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
