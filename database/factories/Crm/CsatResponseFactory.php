<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\CsatResponse;
use App\Models\Crm\CsatSurvey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CsatResponse>
 */
class CsatResponseFactory extends Factory
{
    protected $model = CsatResponse::class;

    public function definition(): array
    {
        return [
            'company_id'     => Company::factory(),
            'csat_survey_id' => CsatSurvey::factory(),
            'rating'         => $this->faker->numberBetween(1, 5),
            'comment'        => $this->faker->optional()->sentence(),
            'responded_at'   => $this->faker->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
