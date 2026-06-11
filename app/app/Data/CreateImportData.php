<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateImportData extends Data
{
    /** @param array<string, string> $column_map source column => target field */
    public function __construct(
        public readonly string $target,
        public readonly string $stored_path,
        public readonly string $filename,
        public readonly array $column_map,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        // Target registration + module activation + required-field coverage
        // validated in StartImportAction against the ImporterRegistry.
        return [
            'target' => ['required', 'string'],
            'stored_path' => ['required', 'string'],
            'filename' => ['required', 'string'],
            'column_map' => ['required', 'array', 'min:1'],
        ];
    }
}
