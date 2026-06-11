<?php

declare(strict_types=1);

namespace App\Data\Core;

use Spatie\LaravelData\Data;

class CreateInvitationData extends Data
{
    public function __construct(
        public readonly string $email,
        public readonly string $role,
    ) {}

    /** @return array<string, array<int, string>> */
    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'role' => ['required', 'string', 'not_in:owner'], // owners transferred, not invited
        ];
    }
}
