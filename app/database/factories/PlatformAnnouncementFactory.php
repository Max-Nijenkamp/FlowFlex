<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Admin;
use App\Models\PlatformAnnouncement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlatformAnnouncement>
 */
class PlatformAnnouncementFactory extends Factory
{
    protected $model = PlatformAnnouncement::class;

    public function definition(): array
    {
        return [
            'title'        => fake()->sentence(6),
            'body'         => fake()->paragraphs(2, true),
            'target'       => 'all',
            'target_value' => null,
            'created_by'   => Admin::factory(),
            'sent_at'      => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(['sent_at' => now()]);
    }

    public function targetCompany(string $companyId): static
    {
        return $this->state([
            'target'       => 'company',
            'target_value' => $companyId,
        ]);
    }
}
