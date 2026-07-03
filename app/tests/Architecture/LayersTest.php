<?php

declare(strict_types=1);

arch('no debug helpers ship in app/')
    ->expect(['dd', 'dump', 'var_dump', 'ray'])
    ->not->toBeUsed();

arch('strict types everywhere in app/')
    ->expect('App')
    ->toUseStrictTypes();

arch('models live in App\Models')
    ->expect('App\Models')
    ->toBeClasses();
