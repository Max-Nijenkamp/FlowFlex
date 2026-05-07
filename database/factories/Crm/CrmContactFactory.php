<?php

namespace Database\Factories\Crm;

use App\Enums\Crm\ContactType;
use App\Models\Company;
use App\Models\Crm\CrmContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmContact>
 */
class CrmContactFactory extends Factory
{
    protected $model = CrmContact::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'email'      => $this->faker->unique()->safeEmail(),
            'phone'      => $this->faker->optional()->phoneNumber(),
            'job_title'  => $this->faker->optional()->jobTitle(),
            'type'       => $this->faker->randomElement(ContactType::cases())->value,
        ];
    }

    public function lead(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContactType::Lead->value,
        ]);
    }

    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContactType::Customer->value,
        ]);
    }
}
