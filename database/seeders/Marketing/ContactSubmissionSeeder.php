<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\ContactSubmission;
use Illuminate\Database\Seeder;

class ContactSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $submissions = [
            [
                'name'    => 'Pieter Jansen',
                'email'   => 'pieter@jansen-advies.nl',
                'subject' => 'sales',
                'message' => 'We are a 35-person consultancy looking for a combined HR and project management solution. We would love to understand how FlowFlex pricing works for our size. Could you send me a quote?',
                'status'  => 'new',
            ],
            [
                'name'    => 'Clara de Wit',
                'email'   => 'clara@dw-accountancy.nl',
                'subject' => 'billing',
                'message' => 'I received my invoice for April but the number of seats seems higher than expected. We had two employees leave last month. Can someone help me understand the pro-rating?',
                'status'  => 'replied',
            ],
            [
                'name'    => 'Ahmed El-Rashid',
                'email'   => 'ahmed@elrashid-partners.com',
                'subject' => 'partnership',
                'message' => 'We run an HR advisory practice with 50+ SME clients in the Netherlands. We are interested in discussing a referral or reseller arrangement. Please get in touch at your earliest convenience.',
                'status'  => 'new',
            ],
        ];

        foreach ($submissions as $data) {
            ContactSubmission::firstOrCreate(
                ['email' => $data['email'], 'subject' => $data['subject']],
                $data
            );
        }
    }
}
