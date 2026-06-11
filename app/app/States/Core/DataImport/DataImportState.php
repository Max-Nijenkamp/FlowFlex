<?php

declare(strict_types=1);

namespace App\States\Core\DataImport;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class DataImportState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransition(Pending::class, Processing::class)
            ->allowTransition(Processing::class, Complete::class)
            ->allowTransition(Processing::class, Failed::class);
    }
}
