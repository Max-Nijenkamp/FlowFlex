<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\KanbanBoard;
use App\Models\Projects\KanbanColumn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KanbanColumn>
 */
class KanbanColumnFactory extends Factory
{
    protected $model = KanbanColumn::class;

    public function definition(): array
    {
        static $order = 0;

        return [
            'company_id'      => Company::factory(),
            'board_id'        => KanbanBoard::factory(),
            'name'            => fake()->randomElement(['To Do', 'In Progress', 'In Review', 'Done']),
            'color'           => fake()->hexColor(),
            'sort_order'      => $order++,
            'wip_limit'       => null,
            'maps_to_status'  => null,
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }
}
