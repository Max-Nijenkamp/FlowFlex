<?php

declare(strict_types=1);

namespace App\States\Finance\Expense;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ExpenseState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Submitted::class)
            ->allowTransition(Submitted::class, Approved::class)
            ->allowTransition(Submitted::class, Rejected::class)
            ->allowTransition(Approved::class, Reimbursed::class);
    }
}
