<?php

declare(strict_types=1);

namespace App\Exceptions\CRM;

use Exception;

class RoomUnavailableException extends Exception
{
    protected $message = 'This deal room link is no longer available.';
}
