<?php

declare(strict_types=1);

namespace App\Support\States;

use App\Models\User;
use App\Support\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\ModelStates\Transition;

/**
 * Base class for every state-machine transition (architecture/patterns/states):
 * extending it auto-writes a `state-transition` audit row carrying from/to —
 * no per-domain wiring (core.audit-log/audit-logger).
 */
abstract class AuditedTransition extends Transition
{
    abstract public function model(): Model;

    abstract public function fromState(): string;

    abstract public function toState(): string;

    public function handle(): Model
    {
        $model = $this->apply();
        $causer = Auth::user();

        app(AuditLogger::class)->log(
            'state-transition',
            $model,
            $causer instanceof User ? $causer : null,
            ['from' => $this->fromState(), 'to' => $this->toState()],
        );

        return $model;
    }

    /** Perform the actual state change; returns the mutated model. */
    abstract protected function apply(): Model;
}
