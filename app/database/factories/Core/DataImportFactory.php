<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\DataImport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataImport>
 */
class DataImportFactory extends Factory
{
    protected $model = DataImport::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'target' => 'test.records',
            'filename' => 'import.csv',
            'column_map' => ['Name' => 'name'],
            'imported_by' => User::factory(),
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => [
            'company_id' => $company->id,
            'imported_by' => User::factory()->forCompany($company),
        ]);
    }
}
