<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use Exception;

class BudgetApprovedException extends Exception
{
    protected $message = 'Approved budgets are immutable — create a revision instead.';
}
