<?php

declare(strict_types=1);

namespace App\States\Finance\Bill;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class BillState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Approved::class)
            ->allowTransition(Approved::class, Scheduled::class)
            ->allowTransition(Approved::class, Paid::class)
            ->allowTransition(Scheduled::class, Paid::class);
    }
}
