<?php

declare(strict_types=1);

namespace App\Exceptions\CRM;

use Exception;

class SelfReferralException extends Exception
{
    protected $message = 'You cannot refer yourself.';
}
