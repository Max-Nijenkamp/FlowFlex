<?php

declare(strict_types=1);

namespace App\Support\Import;

interface ImporterInterface
{
    /** Field name => human label for the mapping UI. @return array<string, string> */
    public function fields(): array;

    /** Required field names. @return list<string> */
    public function requiredFields(): array;

    /**
     * Import one mapped row. Throw on validation failure — the job records the
     * error and continues; row failures never abort the import.
     *
     * @param  array<string, mixed>  $row
     */
    public function importRow(array $row): void;
}
