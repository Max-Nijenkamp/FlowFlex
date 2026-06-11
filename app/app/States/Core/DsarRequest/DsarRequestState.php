<?php

declare(strict_types=1);

namespace App\States\Core\DsarRequest;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class DsarRequestState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Received::class)
            ->allowTransition(Received::class, InProgress::class)
            ->allowTransition(InProgress::class, Completed::class)
            ->allowTransition(InProgress::class, Rejected::class);
    }
}
