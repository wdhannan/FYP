<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParentRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $parentName;
    public $parentID;
    public $email;
    public $temporaryPassword;
    public $childName;

    /**
     * Create a new message instance.
     */
    public function __construct($parentName, $parentID, $email, $temporaryPassword, $childName)
    {
        $this->parentName = $parentName;
        $this->parentID = $parentID;
        $this->email = $email;
        $this->temporaryPassword = $temporaryPassword;
        $this->childName = $childName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Digital Child Health Record System - Your Account Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ParentRegistration',
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

