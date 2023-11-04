<?php

namespace App\Mail;

use App\Models\Space;
use App\Models\SpaceInvite;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class SpaceInvitation extends Mailable
{
    use Queueable, SerializesModels;

    private $space;
    private $invite;
    private $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Space $space, SpaceInvite $invite, User $user)
    {
        $this->space = $space;
        $this->invite = $invite;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config("app.name") . ' : Invitation for ' . $this->space->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.space.invitation',
            with: [
                "space_name" => $this->space->name,
                "user_name" => $this->user->first_name . " " . $this->user->last_name,
                "accept_url" => config("app.frontend_url")."/invitation/accept/?key=".$this->invite->id,
                "reject_url" => config("app.frontend_url")."/invitation/reject/?key=".$this->invite->id,
            ],
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
