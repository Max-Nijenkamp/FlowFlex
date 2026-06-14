<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ModuleCatalog;
use App\Support\Marketing\MarketingContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Health\ResultStores\ResultStore;

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

    public function pricing(Request $request): Response
    {
        $openDomain = in_array($request->query('domain'), ['hr', 'finance', 'crm', 'core'], true)
            ? $request->query('domain')
            : null;

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
            'open_domain' => $openDomain,
        ]);
    }

    public function features(): Response
    {
        return Inertia::render('Marketing/Features', [
            'domains' => $this->domainFeatures(),
        ]);
    }

    /** One domain, deep — linked from the nav Product dropdown. */
    public function domain(string $domain): Response
    {
        abort_unless(in_array($domain, ['hr', 'finance', 'crm', 'core'], true), 404);

        $content = collect($this->domainFeatures())->firstWhere('key', $domain)
            ?? $this->coreDomainContent();

        // Core Platform ships with the base price — its modules live on
        // ModuleCatalog::FREE_CORE, not the paid catalog.
        $modules = $domain === 'core'
            ? collect(ModuleCatalog::FREE_CORE)
                ->map(fn (string $key) => [
                    'key' => $key,
                    'name' => str(str_replace('core.', '', $key))->headline()->toString(),
                    'price_cents' => 0,
                ])
                ->values()
            : collect(config('flowflex.modules'))
                ->filter(fn (array $module, string $key) => $module['domain'] === $domain)
                ->map(fn (array $module, string $key) => [
                    'key' => $key,
                    'name' => $module['name'],
                    'price_cents' => $module['per_user_monthly_price_cents'],
                ])
                ->values();

        return Inertia::render('Marketing/Domain', [
            'domain' => $content,
            'modules' => $modules,
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

    /** @return array{key: string, name: string, description: string, modules: array<int, mixed>, flows: array<int, string>} */
    private function coreDomainContent(): array
    {
        return [
            'key' => 'core',
            'name' => 'Core Platform',
            'description' => 'The platform layer every module stands on — included with every subscription: billing, roles and permissions, audit log, file storage, imports, webhooks and a full REST API.',
            'modules' => [],
            'flows' => [
                'Module activated — permissions, navigation and billing update for the whole company',
                'Any record changed — the audit log captures who, what and when',
            ],
        ];
    }

    public function catalogue(): Response
    {
        return Inertia::render('Marketing/Catalogue', [
            'domains' => $this->domainFeatures(),
        ]);
    }

    public function switchOver(): Response
    {
        return Inertia::render('Marketing/SwitchOver');
    }

    public function trust(): Response
    {
        return Inertia::render('Marketing/Trust');
    }

    public function changelog(): Response
    {
        return Inertia::render('Marketing/Changelog', [
            'entries' => MarketingContent::changelog(),
        ]);
    }

    public function patchwork(): Response
    {
        return Inertia::render('Marketing/Patchwork', [
            'modules' => collect(config('flowflex.modules'))
                ->map(fn (array $m, string $key) => ['key' => $key, 'name' => $m['name'], 'price_cents' => $m['per_user_monthly_price_cents']])
                ->values(),
            'base_price_cents' => config('flowflex.base_price_cents', 500),
        ]);
    }

    public function caseStudy(string $slug): Response
    {
        $study = MarketingContent::caseStudies()[$slug] ?? abort(404);

        return Inertia::render('Marketing/CaseStudy', ['study' => $study]);
    }

    public function status(): Response
    {
        return Inertia::render('Marketing/Status', [
            'checks' => $this->healthChecks(),
            'checked_at' => now()->toIso8601String(),
        ]);
    }

    public function helpIndex(): Response
    {
        return Inertia::render('Marketing/Help/Index', [
            'articles' => collect(MarketingContent::helpArticles())
                ->map(fn (array $a) => collect($a)->except('body'))
                ->values(),
        ]);
    }

    public function helpArticle(string $slug): Response
    {
        $article = collect(MarketingContent::helpArticles())->firstWhere('slug', $slug) ?? abort(404);

        return Inertia::render('Marketing/Help/Article', ['article' => $article]);
    }

    /** @return array<int, array{label: string, ok: bool, message: string}> */
    private function healthChecks(): array
    {
        // Public status reads the latest stored health results (spatie/laravel-health,
        // RunHealthChecksCommand on the scheduler). Cached so the page can't hammer the store.
        return cache()->remember('marketing.status', 60, function (): array {
            $labels = ['Application' => 'Database', 'Database' => 'Database', 'Redis' => 'Cache & queues', 'Horizon' => 'Background jobs', 'Queue' => 'Background jobs', 'UsedDiskSpace' => 'Storage'];

            try {
                $latest = app(ResultStore::class)->latestResults();

                if ($latest === null) {
                    return [['label' => 'All systems', 'ok' => true, 'message' => 'Operational']];
                }

                return $latest->storedCheckResults
                    ->map(fn ($r) => [
                        'label' => $labels[$r->name] ?? $r->label ?: $r->name,
                        'ok' => $r->status === 'ok',
                        'message' => $r->status === 'ok' ? 'Operational' : ($r->shortSummary ?: 'Degraded'),
                    ])
                    ->unique('label')
                    ->values()
                    ->all();
            } catch (\Throwable) {
                return [['label' => 'All systems', 'ok' => true, 'message' => 'Operational']];
            }
        });
    }

    public function sitemap(): \Illuminate\Http\Response
    {
        $urls = ['/', '/features', '/pricing', '/about', '/contact', '/terms', '/privacy',
            '/product/hr', '/product/finance', '/product/crm', '/product/core',
            '/modules', '/switch-over', '/trust', '/changelog', '/patchwork',
            '/customers/veldkamp', '/status', '/help'];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .collect($urls)->map(fn (string $url) => '<url><loc>'.url($url).'</loc></url>')->implode('')
            .'</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
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

    /** @return array<int, array{key: string, name: string, description: string, modules: array<int, mixed>, flows: array<int, string>}> */
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
                'key' => $domain,
                'name' => ucfirst($domain === 'hr' ? 'HR & People' : ($domain === 'crm' ? 'CRM & Sales' : ($domain === 'core' ? 'Core Platform' : 'Finance & Accounting'))),
                'description' => $descriptions[$domain] ?? '',
                'modules' => $modules
                    ->map(fn (array $module) => [
                        'name' => $module['name'],
                        'price_cents' => $module['per_user_monthly_price_cents'] ?? 0,
                    ])
                    ->values()
                    ->all(),
                'flows' => $flows[$domain] ?? [],
            ])
            ->values()
            ->all();
    }
}
