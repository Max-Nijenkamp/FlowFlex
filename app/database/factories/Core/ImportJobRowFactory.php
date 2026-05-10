<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Core\ImportJob;
use App\Models\Core\ImportJobRow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImportJobRow>
 */
class ImportJobRowFactory extends Factory
{
    protected $model = ImportJobRow::class;

    public function definition(): array
    {
        return [
            'import_job_id' => ImportJob::factory(),
            'row_number'    => fake()->numberBetween(1, 1000),
            'status'        => 'pending',
            'raw_data'      => ['name' => fake()->name(), 'email' => fake()->safeEmail()],
            'mapped_data'   => null,
            'errors'        => null,
        ];
    }

    public function imported(): static
    {
        return $this->state(['status' => 'imported']);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
            'errors' => ['email' => ['The email is invalid.']],
        ]);
    }
}
