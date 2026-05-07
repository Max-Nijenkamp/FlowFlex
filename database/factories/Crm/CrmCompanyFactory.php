<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\CrmCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmCompany>
 */
class CrmCompanyFactory extends Factory
{
    protected $model = CrmCompany::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name'       => $this->faker->company(),
            'website'    => $this->faker->optional()->url(),
            'phone'      => $this->faker->optional()->phoneNumber(),
            'industry'   => $this->faker->optional()->randomElement([
                'Technology', 'Finance', 'Healthcare', 'Retail', 'Manufacturing',
                'Construction', 'Education', 'Media', 'Consulting',
            ]),
        ];
    }
}
