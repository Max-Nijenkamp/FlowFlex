<?php

declare(strict_types=1);

namespace App\Data\Core;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;

class AcceptInvitationData extends Data
{
    public function __construct(
        public readonly string $token,
        public readonly string $first_name,
        public readonly string $last_name,
        #[\SensitiveParameter]
        public readonly string $password,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'token' => ['required', 'uuid'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'password' => ['required', Password::min(12)->uncompromised()],
        ];
    }
}
