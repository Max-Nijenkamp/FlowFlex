<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Thin Inertia controllers (<10 lines per method — way-of-working). */
class MarketingController extends Controller
{
    public function home(): Response
    {
        $modules = collect(config('flowflex.modules'));

        return Inertia::render('Marketing/Home', [
            'domains' => $this->domainSummaries(),
            'module_count' => $modules->count(),
            'sample_modules' => $modules
                ->map(fn (array $module, string $key) => [
                    'key' => $key, 'name' => $module['name'], 'domain' => $module['domain'],
                ])
                ->filter(fn (array $m) => $m['domain'] !== 'core')
                ->take(14)
                ->values(),
        ]);
    }

    public function pricing(): Response
    {
        $modules = collect(config('flowflex.modules'))
            ->map(fn (array $module, string $key) => [
                'key' => $key,
                'name' => $module['name'],
                'domain' => $module['domain'],
                'price_cents' => $module['per_user_monthly_price_cents'],
            ])
            ->values();

        return Inertia::render('Marketing/Pricing', [
            'modules' => $modules,
            'base_price_cents' => config('flowflex.base_price_cents', 500), // *(assumed)*
        ]);
    }

    public function features(): Response
    {
        return Inertia::render('Marketing/Features', [
            'domains' => $this->domainFeatures(),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('Marketing/About');
    }

    public function contact(): Response
    {
        return Inertia::render('Marketing/Contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        // Honeypot — bots fill the hidden field; drop silently.
        if ($request->filled('website')) {
            return redirect('/contact');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // v1: log for follow-up; routes into Comms inbox in Phase 2.
        logger()->info('marketing.contact', $request->only(['name', 'email', 'message']));

        return redirect('/contact')->with('success', 'Thanks — we will get back to you.');
    }

    public function terms(): Response
    {
        return Inertia::render('Marketing/Terms', [
            'content' => 'These terms govern your use of FlowFlex. You keep ownership of your data at all times; we process it only to provide the service. Modules are billed per user per month and can be changed monthly. Full legal copy is being finalised with counsel before commercial launch.', // *(assumed placeholder)*
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('Marketing/Privacy', [
            'content' => 'FlowFlex is hosted in the EU and built GDPR-first: data subject access requests, consent records and erasure cascades are product features, not paperwork. We never sell data and collect only what the service needs. Full policy is being finalised with counsel before commercial launch.', // *(assumed placeholder)*
        ]);
    }

    /** @return array<int, array{name: string, modules: int}> */
    private function domainSummaries(): array
    {
        return collect(config('flowflex.modules'))
            ->groupBy('domain')
            ->map(fn ($modules, string $domain) => [
                'name' => ucfirst($domain === 'hr' ? 'HR & People' : ($domain === 'crm' ? 'CRM & Sales' : ($domain === 'core' ? 'Core Platform' : 'Finance & Accounting'))),
                'modules' => $modules->count(),
            ])
            ->values()
            ->all();
    }

    /** @return array<int, array{name: string, description: string, modules: array<int, mixed>, flows: array<int, string>}> */
    private function domainFeatures(): array
    {
        $descriptions = [
            'core' => 'The platform layer every module stands on: billing, roles and permissions, audit log, file storage, imports, webhooks and a full REST API.',
            'hr' => 'The full employee lifecycle — recruit on your own careers page, onboard with checklists, track leave and time, run payroll, review performance.',
            'finance' => 'Ledger-first accounting. Send invoices, manage bills and budgets, watch 13 weeks of cash, close the books with reports that always balance.',
            'crm' => 'From first touch to signed contract — pipeline, quotes, sequences, scheduling, deal rooms and revenue intelligence that tells you which deals are slipping.',
        ];

        $flows = [
            'hr' => [
                'Offer accepted — the salary lands in the next payroll run',
                'Leave approved — shifts unassign and coverage gaps get flagged',
                'Payroll approved — wages post straight to the general ledger',
            ],
            'finance' => [
                'Invoice paid — the customer\'s lifetime value updates in CRM',
                'Expense approved — the cost posts to the right ledger account',
            ],
            'crm' => [
                'Deal won — a draft invoice appears in Finance with the deal value',
                'Quote accepted — the deal moves and the paperwork starts itself',
            ],
            'core' => [],
        ];

        return collect(config('flowflex.modules'))
            ->groupBy('domain')
            ->map(fn ($modules, string $domain) => [
                'name' => ucfirst($domain === 'hr' ? 'HR & People' : ($domain === 'crm' ? 'CRM & Sales' : ($domain === 'core' ? 'Core Platform' : 'Finance & Accounting'))),
                'description' => $descriptions[$domain] ?? '',
                'modules' => $modules->pluck('name')->all(),
                'flows' => $flows[$domain] ?? [],
            ])
            ->values()
            ->all();
    }
}
