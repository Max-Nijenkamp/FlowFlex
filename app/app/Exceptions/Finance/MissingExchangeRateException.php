<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use Exception;

class MissingExchangeRateException extends Exception
{
    protected $message = 'No exchange rate available for the requested date.';
}
