<?php

declare(strict_types=1);

namespace App\States\HR\ReviewCycle;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ReviewCycleState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Active::class)
            ->allowTransition(Active::class, Calibration::class)
            ->allowTransition(Calibration::class, Finalised::class);
    }
}
