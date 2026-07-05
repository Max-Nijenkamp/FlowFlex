<?php

declare(strict_types=1);

namespace App\States\Crm\Deal;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/** open → won | lost. Closed deals never move again (crm.deals spec). */
abstract class DealState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Open::class)
            ->allowTransition(Open::class, Won::class)
            ->allowTransition(Open::class, Lost::class);
    }
}
