<?php

declare(strict_types=1);

namespace App\Exceptions\Core;

use RuntimeException;

class InvalidInvitationTokenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('This invitation link is invalid, expired, or already used.');
    }
}
