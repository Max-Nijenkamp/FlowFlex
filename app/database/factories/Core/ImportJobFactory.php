<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\ImportJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImportJob>
 */
class ImportJobFactory extends Factory
{
    protected $model = ImportJob::class;

    public function definition(): array
    {
        $company = Company::factory()->create();

        return [
            'company_id'         => $company->id,
            'created_by'         => User::factory()->create(['company_id' => $company->id])->id,
            'entity_type'        => fake()->randomElement(['users', 'employees', 'contacts', 'products']),
            'status'             => 'pending',
            'duplicate_strategy' => 'skip',
            'total_rows'         => 0,
            'imported_rows'      => 0,
            'skipped_rows'       => 0,
            'failed_rows'        => 0,
            'column_mapping'     => null,
            'file_path'          => '/tmp/' . fake()->uuid() . '.csv',
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attr) => [
            'company_id' => $company->id,
            'created_by' => User::factory()->create(['company_id' => $company->id])->id,
        ]);
    }

    public function done(): static
    {
        return $this->state([
            'status'      => 'done',
            'started_at'  => now()->subMinutes(5),
            'finished_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status'      => 'failed',
            'started_at'  => now()->subMinutes(5),
            'finished_at' => now(),
        ]);
    }
}
