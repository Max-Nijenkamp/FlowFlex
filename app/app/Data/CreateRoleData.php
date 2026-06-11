<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateRoleData extends Data
{
    /** @param list<string> $permissions */
    public function __construct(
        public readonly string $name,
        public readonly array $permissions = [],
    ) {}

    /** @return array<string, array<int, string>> */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
