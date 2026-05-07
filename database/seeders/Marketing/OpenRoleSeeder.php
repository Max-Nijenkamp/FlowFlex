<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\OpenRole;
use Illuminate\Database\Seeder;

class OpenRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'title'        => 'Senior Full-Stack Engineer',
                'slug'         => 'senior-full-stack-engineer',
                'department'   => 'Engineering',
                'location'     => 'Remote (NL/EU)',
                'type'         => 'full-time',
                'salary_range' => 'EUR 70,000 – 90,000',
                'about_role'   => 'We are looking for a Senior Full-Stack Engineer to join our small but growing engineering team. You will work on the Laravel backend, the Filament admin panels, and the Inertia/Vue frontend that powers FlowFlex. You will have significant influence over architecture decisions and be a key contributor to our open engineering culture.',
                'responsibilities' => "Build and maintain features across the full stack (Laravel, Filament, Inertia, Vue).\nCollaborate closely with the CTO on architecture and technical direction.\nReview pull requests and mentor junior engineers as the team grows.\nParticipate in on-call rotation (low frequency, well-compensated).\nContribute to our internal tooling and developer experience.",
                'requirements'  => "5+ years of professional experience with Laravel.\nStrong understanding of relational databases (PostgreSQL preferred).\nExperience with modern frontend frameworks (Vue or React).\nPassion for clean, well-tested code.\nBased in the Netherlands or EU, with authorisation to work in the EU.",
                'nice_to_have'  => "Experience with Filament v3+.\nFamiliarity with multi-tenant SaaS architecture.\nPrevious experience at a bootstrapped or early-stage startup.",
                'how_to_apply'  => 'Send your CV and a brief note on why FlowFlex interests you to careers@flowflex.io. No recruiters please.',
                'status'        => 'open',
                'published_at'  => now()->subDays(14),
            ],
            [
                'title'        => 'Customer Success Manager',
                'slug'         => 'customer-success-manager',
                'department'   => 'Customer Success',
                'location'     => 'Hybrid (Amsterdam)',
                'type'         => 'full-time',
                'salary_range' => 'EUR 45,000 – 60,000',
                'about_role'   => 'As our first dedicated Customer Success Manager, you will own the post-sale experience for FlowFlex customers. You will onboard new workspaces, build long-term relationships, identify expansion opportunities, and be the voice of the customer inside the company.',
                'responsibilities' => "Own the onboarding process for all new FlowFlex customers.\nConduct regular check-in calls and quarterly business reviews.\nIdentify at-risk accounts and take proactive action to retain them.\nCollect and relay product feedback to the engineering and product teams.\nDevelop self-serve resources including help articles, video walkthroughs, and FAQs.",
                'requirements'  => "2+ years in a customer success or account management role, ideally at a B2B SaaS company.\nExcellent written and spoken Dutch and English.\nEmpathetic and consultative communication style.\nAbility to learn and explain software products clearly to non-technical users.\nBased in or near Amsterdam (hybrid, 2–3 days in office).",
                'nice_to_have'  => "Experience with HR software or workforce management tools.\nFamiliarity with HubSpot or similar CRM.\nPrevious experience at a company with fewer than 50 employees.",
                'how_to_apply'  => 'Send your CV and a brief cover letter to careers@flowflex.io. Tell us about a customer you turned around from at-risk to advocate.',
                'status'        => 'open',
                'published_at'  => now()->subDays(7),
            ],
        ];

        foreach ($roles as $data) {
            OpenRole::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
