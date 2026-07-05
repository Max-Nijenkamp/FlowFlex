<?php

declare(strict_types=1);

namespace App\States\Finance\Invoice;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * draft → sent → partially_paid → paid; sent/partially_paid can go
 * overdue and still be paid; only draft/sent/overdue can be voided
 * (finance.invoicing/invoice-lifecycle).
 */
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
            ->allowTransition(Sent::class, Voided::class)
            ->allowTransition(PartiallyPaid::class, Paid::class)
            ->allowTransition(PartiallyPaid::class, Overdue::class)
            ->allowTransition(Overdue::class, PartiallyPaid::class)
            ->allowTransition(Overdue::class, Paid::class)
            ->allowTransition(Overdue::class, Voided::class)
            ->allowTransition(Draft::class, Voided::class);
    }
}
