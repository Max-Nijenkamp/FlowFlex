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
        return Inertia::render('Marketing/Home', [
            'domains' => $this->domainSummaries(),
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
            'content' => 'FlowFlex terms of service. Full legal copy lands before launch.', // *(assumed placeholder)*
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('Marketing/Privacy', [
            'content' => 'FlowFlex privacy policy. Full legal copy lands before launch.', // *(assumed placeholder)*
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

    /** @return array<int, array{name: string, description: string, modules: array<int, mixed>}> */
    private function domainFeatures(): array
    {
        $descriptions = [
            'core' => 'The platform layer: billing, roles, audit, files, API and more.',
            'hr' => 'From hiring to payroll — the full employee lifecycle.',
            'finance' => 'Ledger-first accounting with invoicing, AP/AR and reporting.',
            'crm' => 'Pipeline, sequences, scheduling and revenue intelligence.',
        ];

        return collect(config('flowflex.modules'))
            ->groupBy('domain')
            ->map(fn ($modules, string $domain) => [
                'name' => ucfirst($domain === 'hr' ? 'HR & People' : ($domain === 'crm' ? 'CRM & Sales' : ($domain === 'core' ? 'Core Platform' : 'Finance & Accounting'))),
                'description' => $descriptions[$domain] ?? '',
                'modules' => $modules->pluck('name')->all(),
            ])
            ->values()
            ->all();
    }
}
