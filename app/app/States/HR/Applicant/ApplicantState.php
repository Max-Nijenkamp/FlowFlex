<?php

declare(strict_types=1);

namespace App\States\HR\Applicant;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ApplicantState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Applied::class)
            ->allowTransition(Applied::class, Screening::class)
            ->allowTransition(Screening::class, Interview::class)
            ->allowTransition(Interview::class, Offer::class)
            ->allowTransition(Offer::class, Hired::class)
            ->allowTransition(Applied::class, Rejected::class)
            ->allowTransition(Screening::class, Rejected::class)
            ->allowTransition(Interview::class, Rejected::class)
            ->allowTransition(Offer::class, Rejected::class);
    }
}
