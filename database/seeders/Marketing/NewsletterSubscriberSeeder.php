<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\NewsletterSubscriber;
use Illuminate\Database\Seeder;

class NewsletterSubscriberSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = [
            [
                'email'                   => 'marieke@logistics-plus.nl',
                'status'                  => 'subscribed',
                'source'                  => 'blog',
                'subscribed_at'           => now()->subDays(60),
                'unsubscribed_at'         => null,
                'double_opt_in_confirmed' => true,
                'double_opt_in_sent_at'   => now()->subDays(60),
            ],
            [
                'email'                   => 'james@techspark.io',
                'status'                  => 'subscribed',
                'source'                  => 'demo',
                'subscribed_at'           => now()->subDays(45),
                'unsubscribed_at'         => null,
                'double_opt_in_confirmed' => true,
                'double_opt_in_sent_at'   => now()->subDays(45),
            ],
            [
                'email'                   => 'info@bakkerij-cornelissen.nl',
                'status'                  => 'subscribed',
                'source'                  => 'footer',
                'subscribed_at'           => now()->subDays(30),
                'unsubscribed_at'         => null,
                'double_opt_in_confirmed' => true,
                'double_opt_in_sent_at'   => now()->subDays(30),
            ],
            [
                'email'                   => 'hr@greenleaf-ventures.com',
                'status'                  => 'subscribed',
                'source'                  => 'blog',
                'subscribed_at'           => now()->subDays(20),
                'unsubscribed_at'         => null,
                'double_opt_in_confirmed' => true,
                'double_opt_in_sent_at'   => now()->subDays(20),
            ],
            [
                'email'                   => 'ops@rijndelta-logistics.nl',
                'status'                  => 'subscribed',
                'source'                  => 'footer',
                'subscribed_at'           => now()->subDays(10),
                'unsubscribed_at'         => null,
                'double_opt_in_confirmed' => false,
                'double_opt_in_sent_at'   => now()->subDays(10),
            ],
            [
                'email'                   => 'admin@puur-personeelsadvies.nl',
                'status'                  => 'subscribed',
                'source'                  => 'blog',
                'subscribed_at'           => now()->subDays(5),
                'unsubscribed_at'         => null,
                'double_opt_in_confirmed' => false,
                'double_opt_in_sent_at'   => now()->subDays(5),
            ],
            [
                'email'                   => 'contact@stegeman-bouw.nl',
                'status'                  => 'unsubscribed',
                'source'                  => 'footer',
                'subscribed_at'           => now()->subDays(90),
                'unsubscribed_at'         => now()->subDays(15),
                'double_opt_in_confirmed' => true,
                'double_opt_in_sent_at'   => now()->subDays(90),
            ],
            [
                'email'                   => 'finance@veld-en-meer.nl',
                'status'                  => 'unsubscribed',
                'source'                  => 'blog',
                'subscribed_at'           => now()->subDays(120),
                'unsubscribed_at'         => now()->subDays(45),
                'double_opt_in_confirmed' => true,
                'double_opt_in_sent_at'   => now()->subDays(120),
            ],
        ];

        foreach ($subscribers as $data) {
            NewsletterSubscriber::firstOrCreate(['email' => $data['email']], $data);
        }
    }
}
