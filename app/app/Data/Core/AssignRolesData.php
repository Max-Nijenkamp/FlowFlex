<?php

declare(strict_types=1);

namespace App\Data\Core;

use Spatie\LaravelData\Data;

class AssignRolesData extends Data
{
    /** @param list<string> $roles */
    public function __construct(
        public readonly string $user_id,
        public readonly array $roles,
    ) {}

    /** @return array<string, array<int, string>> */
    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'string', 'exists:users,id'],
            'roles' => ['array'],
            'roles.*' => ['string'],
        ];
    }
}
