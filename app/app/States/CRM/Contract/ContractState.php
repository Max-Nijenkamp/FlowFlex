<?php

declare(strict_types=1);

namespace App\States\CRM\Contract;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ContractState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Sent::class)
            ->allowTransition(Sent::class, Signed::class)
            ->allowTransition(Signed::class, Active::class)
            ->allowTransition(Active::class, Expired::class)
            ->allowTransition(Active::class, Terminated::class);
    }
}
