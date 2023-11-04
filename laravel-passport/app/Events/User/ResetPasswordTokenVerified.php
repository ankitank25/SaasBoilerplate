<?php

namespace App\Events\User;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordTokenVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $token;

    /**
     * Create a new event instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
