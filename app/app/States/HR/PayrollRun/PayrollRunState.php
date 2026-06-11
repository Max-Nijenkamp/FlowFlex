<?php

declare(strict_types=1);

namespace App\States\HR\PayrollRun;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class PayrollRunState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Processing::class)
            ->allowTransition(Processing::class, Draft::class)
            ->allowTransition(Processing::class, Approved::class)
            ->allowTransition(Approved::class, Archived::class);
    }
}
