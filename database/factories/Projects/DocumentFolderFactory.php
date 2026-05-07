<?php

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\DocumentFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentFolder>
 */
class DocumentFolderFactory extends Factory
{
    protected $model = DocumentFolder::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'name'        => $this->faker->randomElement(['Contracts', 'HR Documents', 'Finance', 'Projects', 'Marketing', 'Legal']),
            'description' => $this->faker->sentence(),
        ];
    }
}
