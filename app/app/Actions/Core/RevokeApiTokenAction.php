<?php

declare(strict_types=1);

namespace App\Actions\Core;

use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class RevokeApiTokenAction
{
    use AsAction;

    public function handle(string $tokenId): void
    {
        Auth::guard('web')->user()->tokens()->whereKey($tokenId)->delete();
    }
}
