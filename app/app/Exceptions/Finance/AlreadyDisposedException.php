<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use Exception;

class AlreadyDisposedException extends Exception
{
    protected $message = 'Asset has already been disposed.';
}
