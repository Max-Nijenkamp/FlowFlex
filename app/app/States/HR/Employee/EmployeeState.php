<?php

declare(strict_types=1);

namespace App\States\HR\Employee;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class EmployeeState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Active::class)
            ->allowTransition(Active::class, OnLeave::class)
            ->allowTransition(Active::class, Suspended::class)
            ->allowTransition(Active::class, Terminated::class)
            ->allowTransition(OnLeave::class, Active::class)
            ->allowTransition(OnLeave::class, Terminated::class)
            ->allowTransition(Suspended::class, Active::class)
            ->allowTransition(Suspended::class, Terminated::class);
    }
}
