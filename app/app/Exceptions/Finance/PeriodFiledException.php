<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use Exception;

class PeriodFiledException extends Exception
{
    protected $message = 'Tax period has been filed and is locked.';
}
