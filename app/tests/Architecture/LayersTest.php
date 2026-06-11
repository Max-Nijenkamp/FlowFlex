<?php

declare(strict_types=1);

// FlowFlex architecture guard rails. See architecture/patterns/testing-pattern.
// Layer-specific rules (Data, Services, Controllers) are added as those layers
// are introduced by the first business modules.

arch('no debug statements leak into the codebase')
    ->expect(['dd', 'dump', 'var_dump', 'ray', 'die'])
    ->not->toBeUsed();

arch('strict types everywhere in app')
    ->expect('App')
    ->toUseStrictTypes();

arch('all Eloquent models use ULIDs and soft deletes')
    ->expect('App\Models')
    ->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUlids')
    ->toUseTrait('Illuminate\Database\Eloquent\SoftDeletes');
