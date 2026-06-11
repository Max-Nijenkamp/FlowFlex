<?php

declare(strict_types=1);

namespace App\States\BillingInvoice;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class BillingInvoiceState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Open::class)
            ->allowTransition(Open::class, Paid::class)
            ->allowTransition(Open::class, PastDue::class)
            ->allowTransition(PastDue::class, Paid::class)
            ->allowTransition(PastDue::class, Uncollectible::class);
    }
}
