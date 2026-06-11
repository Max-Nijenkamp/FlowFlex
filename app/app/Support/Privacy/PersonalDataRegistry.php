<?php

declare(strict_types=1);

namespace App\Support\Privacy;

/**
 * Modules register their PII tables/fields in their ServiceProviders:
 *   $registry->register('hr.profiles', [
 *       'hr_employees' => ['email_column' => 'personal_email', 'fields' => [...], 'erasure' => 'anonymise'],
 *   ]);
 * Drives both DSAR export scope and the erasure cascade.
 */
class PersonalDataRegistry
{
    /** @var array<string, array<string, array<string, mixed>>> moduleKey => table => config */
    private array $entries = [];

    /** @param array<string, array<string, mixed>> $tablesFields */
    public function register(string $moduleKey, array $tablesFields): void
    {
        $this->entries[$moduleKey] = $tablesFields;
    }

    /** @return array<string, array<string, mixed>> table => config across all modules */
    public function tables(): array
    {
        return array_merge(...array_values($this->entries) ?: [[]]);
    }

    /** @return array<string, array<string, mixed>> tables holding rows for a subject email */
    public function tablesForSubject(): array
    {
        return $this->tables();
    }
}
