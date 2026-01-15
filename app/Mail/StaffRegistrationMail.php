<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fullName;
    public $staffID;
    public $email;
    public $temporaryPassword;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct($fullName, $staffID, $email, $temporaryPassword, $role)
    {
        $this->fullName = $fullName;
        $this->staffID = $staffID;
        $this->email = $email;
        $this->temporaryPassword = $temporaryPassword;
        $this->role = $role;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->role === 'doctor' 
            ? 'Welcome to Smart Child Care - Doctor Account Created' 
            : 'Welcome to Smart Child Care - Nurse Account Created';
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.StaffRegistration',
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
