<?php

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Document;
use App\Models\Projects\DocumentFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $ext = $this->faker->randomElement(['pdf', 'docx', 'xlsx', 'png']);

        return [
            'company_id'       => Company::factory(),
            'folder_id'        => DocumentFolder::factory(),
            'title'            => $this->faker->sentence(3),
            'original_filename'=> $this->faker->slug(2) . '.' . $ext,
            'mime_type'        => match($ext) {
                'pdf'  => 'application/pdf',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'png'  => 'image/png',
            },
            'file_size_bytes' => $this->faker->numberBetween(1024, 10485760),
            'version_number'  => 1,
            'is_starred'      => false,
            'tags'            => [],
        ];
    }

    public function starred(): static
    {
        return $this->state(fn (array $attributes) => ['is_starred' => true]);
    }
}
