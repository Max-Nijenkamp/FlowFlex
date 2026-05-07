<?php

namespace Database\Seeders;

use Database\Seeders\Marketing\BlogCategorySeeder;
use Database\Seeders\Marketing\BlogPostSeeder;
use Database\Seeders\Marketing\ChangelogEntrySeeder;
use Database\Seeders\Marketing\ContactSubmissionSeeder;
use Database\Seeders\Marketing\DemoRequestSeeder;
use Database\Seeders\Marketing\FaqEntrySeeder;
use Database\Seeders\Marketing\HelpArticleSeeder;
use Database\Seeders\Marketing\HelpCategorySeeder;
use Database\Seeders\Marketing\NewsletterSubscriberSeeder;
use Database\Seeders\Marketing\OpenRoleSeeder;
use Database\Seeders\Marketing\TeamMemberSeeder;
use Database\Seeders\Marketing\TestimonialSeeder;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
            TestimonialSeeder::class,
            FaqEntrySeeder::class,
            TeamMemberSeeder::class,
            OpenRoleSeeder::class,
            ChangelogEntrySeeder::class,
            HelpCategorySeeder::class,
            HelpArticleSeeder::class,
            DemoRequestSeeder::class,
            NewsletterSubscriberSeeder::class,
            ContactSubmissionSeeder::class,
        ]);
    }
}
