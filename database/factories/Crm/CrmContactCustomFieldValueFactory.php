<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmContactCustomField;
use App\Models\Crm\CrmContactCustomFieldValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmContactCustomFieldValue>
 */
class CrmContactCustomFieldValueFactory extends Factory
{
    protected $model = CrmContactCustomFieldValue::class;

    public function definition(): array
    {
        return [
            'company_id'                  => Company::factory(),
            'crm_contact_id'              => CrmContact::factory(),
            'crm_contact_custom_field_id' => CrmContactCustomField::factory(),
            'value'                       => $this->faker->words(2, true),
        ];
    }
}
