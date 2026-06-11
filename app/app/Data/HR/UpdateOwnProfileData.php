<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class UpdateOwnProfileData extends Data
{
    /** @param list<array{name: string, relationship: string, phone: string, email?: string|null}> $emergency_contacts */
    public function __construct(
        public readonly ?string $phone = null,
        public readonly ?string $personal_email = null,
        public readonly array $emergency_contacts = [],
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'phone' => ['nullable', 'phone:AUTO'],
            'personal_email' => ['nullable', 'email'],
            'emergency_contacts' => ['array', 'max:3'],
            'emergency_contacts.*.name' => ['required', 'string', 'max:150'],
            'emergency_contacts.*.relationship' => ['required', 'string', 'max:100'],
            'emergency_contacts.*.phone' => ['required', 'string', 'max:30'],
            'emergency_contacts.*.email' => ['nullable', 'email'],
        ];
    }
}
