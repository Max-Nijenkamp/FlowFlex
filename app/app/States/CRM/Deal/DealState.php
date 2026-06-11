<?php

declare(strict_types=1);

namespace App\States\CRM\Deal;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class DealState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Open::class)
            ->allowTransition(Open::class, Won::class)
            ->allowTransition(Open::class, Lost::class)
            ->allowTransition(Won::class, Open::class)
            ->allowTransition(Lost::class, Open::class);
    }
}
