<?php

declare(strict_types=1);

namespace App\States\Hr\LeaveRequest;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * draft → submitted → approved|rejected; the requester can cancel a
 * draft, submitted, or approved request (hr.leave/leave-request-workflow).
 */
abstract class LeaveRequestState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Submitted::class)
            ->allowTransition(Draft::class, Cancelled::class)
            ->allowTransition(Submitted::class, Approved::class)
            ->allowTransition(Submitted::class, Rejected::class)
            ->allowTransition(Submitted::class, Cancelled::class)
            ->allowTransition(Approved::class, Cancelled::class);
    }
}
