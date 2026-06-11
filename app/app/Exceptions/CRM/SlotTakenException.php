<?php

declare(strict_types=1);

namespace App\Exceptions\CRM;

use Exception;

class SlotTakenException extends Exception
{
    protected $message = 'That time slot has just been taken.';
}
