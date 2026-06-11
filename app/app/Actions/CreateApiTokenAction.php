<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateApiTokenData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateApiTokenAction
{
    use AsAction;

    /** Returns the plain token — shown once, stored hashed by Sanctum. */
    public function handle(CreateApiTokenData $data): string
    {
        $user = Auth::guard('web')->user();

        $token = $user->createToken(
            name: $data->name,
            abilities: $data->abilities,
            expiresAt: $data->expires_at !== null ? Carbon::parse($data->expires_at) : null,
        );

        $token->accessToken->forceFill(['created_by' => $user->id])->save();

        return $token->plainTextToken;
    }
}
