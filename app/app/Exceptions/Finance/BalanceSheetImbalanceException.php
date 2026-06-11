<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use Exception;

class BalanceSheetImbalanceException extends Exception
{
    protected $message = 'Balance sheet does not balance — data corruption suspected.';
}
