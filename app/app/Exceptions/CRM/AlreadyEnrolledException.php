<?php

declare(strict_types=1);

namespace App\Exceptions\CRM;

use Exception;

class AlreadyEnrolledException extends Exception
{
    protected $message = 'Contact already has an active enrolment in this sequence.';
}
