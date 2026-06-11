<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateDsarRequestData extends Data
{
    public function __construct(
        public readonly string $subject_email,
        public readonly string $request_type,
    ) {}

    /** @return array<string, array<int, string>> */
    public static function rules(): array
    {
        return [
            'subject_email' => ['required', 'email'],
            'request_type' => ['required', 'in:access,erasure'],
        ];
    }
}
