<?php

declare(strict_types=1);

namespace App\Data\Core;

use Spatie\LaravelData\Data;

class CreateApiTokenData extends Data
{
    /** @param list<string> $abilities */
    public function __construct(
        public readonly string $name,
        public readonly array $abilities,
        public readonly ?string $expires_at = null,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['string', 'regex:/^[a-z-]+:(read|write)$/'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
