<?php

declare(strict_types=1);

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<JournalEntry> */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'reference' => 'REF-'.fake()->unique()->numberBetween(1000, 99999),
            'description' => fake()->sentence(3),
            'entry_date' => now()->toDateString(),
            'status' => 'posted',
        ];
    }
}
