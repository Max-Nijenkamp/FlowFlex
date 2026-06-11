<?php

declare(strict_types=1);

namespace App\States\Finance\Invoice;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class InvoiceState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Sent::class)
            ->allowTransition(Sent::class, PartiallyPaid::class)
            ->allowTransition(Sent::class, Paid::class)
            ->allowTransition(Sent::class, Overdue::class)
            ->allowTransition(PartiallyPaid::class, Paid::class)
            ->allowTransition(PartiallyPaid::class, Overdue::class)
            ->allowTransition(Overdue::class, Paid::class)
            ->allowTransition(Overdue::class, PartiallyPaid::class)
            ->allowTransition(Draft::class, Voided::class)
            ->allowTransition(Sent::class, Voided::class)
            ->allowTransition(Overdue::class, Voided::class);
    }
}
