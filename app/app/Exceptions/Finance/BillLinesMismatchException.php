<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use Exception;

class BillLinesMismatchException extends Exception
{
    protected $message = 'Bill line amounts do not sum to the bill total.';
}
