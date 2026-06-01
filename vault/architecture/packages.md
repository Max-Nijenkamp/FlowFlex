---
type: architecture
category: packages
color: "#A78BFA"
---

# Package Evaluation

Status of 18 candidate packages beyond the confirmed core stack. Each entry: problem it solves, verdict, and rationale.

---

## Status Legend

| Status | Meaning |
|---|---|
| ✅ Use | Confirmed — solves a real named problem, add to composer.json |
| ⏳ Defer | Useful but not needed yet — revisit when the relevant domain starts |
| ❌ Skip | Overlaps existing stack, adds complexity without payoff |

---

## Core Stack Additions (Previously Omitted)

### `laravel/scout` ✅ Core stack

**Problem**: Meilisearch integration requires Scout as the Laravel wrapper. `laravel/scout` is the package that provides `Searchable` trait, `toSearchableArray()`, and the `search()` builder. Without it, Meilisearch integration does not exist.

**Note**: listed here because it was missing from the confirmed stack in `tech-stack.md`.

---

### `tightenco/ziggy` ✅ Use

**Problem**: Vue components that need to generate URLs to named Laravel routes (e.g. `route('hr.employees.create')`) have no way to do so without Ziggy. Without it, every URL is a hardcoded string in Vue — breaks when routes change.

**Use in**: All Vue + Inertia pages that render links or form actions to Laravel routes.

```javascript
// resources/js/app.ts
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'

createInertiaApp({
    setup({ app }) {
        app.use(ZiggyVue)
    },
})

// In Vue component
<Link :href="route('hr.employees.create')">New Employee</Link>
```

Generate the Ziggy routes file after route changes:
```bash
docker exec flowflex_app php artisan ziggy:generate
```

---

### `laravel/cashier` ❌ Skip

**Problem it claims to solve**: Stripe subscription management with a Laravel-native API.

**Why skip**: Cashier is designed for simple subscription products (one flat subscription per customer). FlowFlex has a per-user, per-module pricing model with dynamic subscription items that change whenever a company activates or deactivates a module. Cashier's subscription model does not map cleanly to this.

**What we use instead**: Raw `stripe/stripe-php` SDK with a custom `BillingService` that manages subscription items per module. This gives full control over the pricing logic.

**ADR**: See [[build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk]].

---

## Spatie Packages

### `spatie/laravel-model-states` ✅ Use

**Problem**: HR leave status, invoice status, deal stage — all have lifecycle transitions (draft → submitted → approved → rejected). Raw string enums allow invalid transitions. This enforces valid state machines with transition rules.

**Use in**: HR (leave, contracts), Finance (invoices, expenses), CRM (deal stages), Support (ticket status).

**Pattern**:
```php
class InvoiceStatus extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->allowTransition(Draft::class, Sent::class)
            ->allowTransition(Sent::class, [Paid::class, Overdue::class]);
    }
}
```

---

### `spatie/laravel-settings` ✅ Use

**Problem**: Company-level settings (timezone, locale, fiscal year start, notification preferences) need type-safe storage. Building a custom settings table per company is repetitive.

**Use in**: Core Platform (company settings), HR (leave policy settings), Finance (tax settings).

**Note**: Scope settings per company using the `team` driver with `company_id`.

---

### `spatie/laravel-sluggable` ✅ Use

**Problem**: Multiple models need unique slugs (job postings, wiki pages, event landing pages, product slugs in e-commerce). Manual slug generation is repetitive.

**Use in**: DMS (document slugs), Events, E-commerce (products), Marketing (landing pages, blog posts).

---

### `spatie/laravel-tags` ⏳ Defer

**Problem**: Polymorphic tagging on contacts, documents, tasks, leads. Useful across CRM, DMS, Projects.

**Defer until**: CRM or DMS domain starts. Tags add query complexity — don't add the dependency until there is a concrete use case in active development.

---

### `spatie/laravel-schemaless-attributes` ⏳ Defer

**Problem**: Custom fields per company on models (e.g. "custom employee attributes", "custom contact fields"). Avoids one migration per custom attribute.

**Defer until**: A domain explicitly needs per-company custom fields. CRM custom contact fields is the most likely trigger. Do not add speculatively.

---

### `spatie/laravel-health` ✅ Use

**Problem**: Health check endpoint for uptime monitoring and readiness probes. Pairs with Laravel Pulse. Needed at launch.

**Use in**: Core Platform (system health page in `/app` or `/admin`).

---

### `spatie/laravel-query-builder` ⏳ Defer

**Problem**: API filter/sort/include handling for external API consumers. Cleaner than manual `if ($request->has('filter'))` chains.

**Defer until**: External REST API is being built. The Inertia + Filament path does not need this — Filament handles its own filtering. Only relevant when building the public API layer.

---

## Laravel Actions

### `lorisleiva/laravel-actions` ✅ Use

**Problem**: Interface → ServiceProvider → Service is over-engineered for simple single-operation use cases (e.g. "send a password reset email", "mark notification as read", "deactivate a module"). A single-class action handles all of these without boilerplate.

**Decision**: Hybrid pattern.
- **Use Actions** for: single-step operations, no alternative implementations needed, used in one context only
- **Use Interface→Service** for: domain operations with multiple steps, testable swappable implementations, cross-domain dependencies

See [[architecture/patterns/actions-pattern]] for the decision rule and code examples.

---

## Sushi

### `calebporzio/sushi` ✅ Use

**Problem**: Module catalog, country lists, currency codes, and timezone lists are static data that should be queryable via Eloquent but should not live in the database (they never change, don't need migrations, and don't need soft deletes).

**Use in**: Core Platform (`ModuleCatalog` model backed by static array), country/currency pickers across Finance and HR.

---

## Filament Plugins

### `pxlrbt/filament-excel` ✅ Use

**Problem**: Every domain needs Excel exports from its Filament tables (employee list, invoice list, contact list). Without this, every resource needs custom export code.

**Use in**: All domains that have list resources with export requirements (HR, Finance, CRM, at minimum).

---

### `awcodes/filament-tiptap-editor` ✅ Use

**Problem**: Rich text editing is needed in DMS (wiki pages), LMS (course content), Marketing (email templates), and Communications (broadcast messages). The existing `@tiptap/vue-3` is for Vue pages, not Filament forms.

**Use in**: DMS, LMS, Marketing, Communications — any Filament form with a rich text field.

---

### `saade/filament-fullcalendar` ✅ Use

**Problem**: Calendar view is needed in HR Leave (team availability calendar), Events (event schedule), and Projects (milestone timeline). Building a Filament calendar widget from scratch is significant work.

**Use in**: HR (leave calendar), Events (event calendar), Projects (sprint/milestone calendar).

---

### `leandrocfe/filament-apex-charts` ✅ Use

**Problem**: Analytics widgets, Finance dashboards, HR headcount trends. The core stack has `chart.js` for Vue pages but Filament widgets need a native integration. ApexCharts is more flexible than Chart.js for dashboard-style widgets.

**Use in**: Analytics, Finance (revenue charts), HR (headcount), Core (dashboard widgets).

---

### `rmsramos/activitylog` ✅ Use

**Problem**: Spatie Activity Log captures audit records but there is no UI to browse them. Without this, the Core audit log resource needs to be built from scratch.

**Use in**: Core Platform (`/app` audit log resource).

---

### `codewithdennis/filament-select-tree` ⏳ Defer

**Problem**: Tree-select field for parent department selection in org chart, product category trees in E-commerce.

**Defer until**: HR org chart or E-commerce domain starts. The field is only needed when hierarchical selects exist in Filament forms.

---

### `livewire/volt` ❌ Skip

**Problem it claims to solve**: Single-file Livewire components for simpler widget code.

**Why skip**: Filament 5 uses class-based Livewire components. Volt's single-file syntax conflicts with Filament's widget/page class conventions. Mixing paradigms adds cognitive overhead without a payoff. Filament widget code is already concise.

---

---

## Additional Packages (Round 2)

### `spatie/laravel-pdf` ✅ Use

**Problem**: Invoice PDF generation (Finance), payslip PDFs (HR), quote PDFs (CRM). Every domain that generates a document needs PDF output. `barryvdh/laravel-dompdf` is the legacy choice; `spatie/laravel-pdf` uses Chromium under the hood for pixel-perfect CSS rendering.

**Use in**: Finance (invoices, expense reports), HR (payslips, contracts, offer letters), CRM (quotes).

**Note**: Requires a Chromium binary in the container. Add to the Docker image.

```php
use Spatie\LaravelPdf\Facades\Pdf;

Pdf::view('pdfs.invoice', ['invoice' => $invoice])
    ->name("invoice-{$invoice->invoice_number}.pdf")
    ->save(Storage::path("companies/{$invoice->company_id}/invoices/"));
```

---

### `maatwebsite/laravel-excel` ✅ Use

**Problem**: `pxlrbt/filament-excel` handles Filament table exports, but bulk queue-based exports (export all 50,000 employees for an HRIS migration) need the underlying `maatwebsite/laravel-excel` library for chunked, streaming, and queued export jobs.

**Use in**: Any domain with large data exports. Import processing for [[domains/core/data-import]].

```php
class EmployeeExport implements FromQuery, WithHeadings, ShouldQueue
{
    use Exportable;

    public function query(): Builder
    {
        return Employee::query()->where('company_id', $this->companyId);
    }
}
```

---

### `spatie/laravel-backup` ✅ Use

**Problem**: Automated daily backups of the PostgreSQL database and S3/R2 files. Without this, a data loss event has no recovery path.

**Use in**: Infrastructure — runs as a scheduled command, not domain-specific.

**Config**: backup to a secondary R2 bucket. Retention: keep daily backups for 7 days, weekly for 4 weeks.

```bash
# In Kernel.php
$schedule->command('backup:run')->dailyAt('01:00');
$schedule->command('backup:clean')->dailyAt('01:30');
$schedule->command('backup:monitor')->dailyAt('09:00');
```

---

### `sentry/sentry-laravel` ✅ Use

**Problem**: Production error tracking. Laravel Pulse shows that exceptions are happening but not what they are or which user triggered them. Sentry captures full stack traces, user context, breadcrumbs, and performance traces.

**Config**: 
```php
// .env production
SENTRY_LARAVEL_DSN=https://...@sentry.io/...
SENTRY_TRACES_SAMPLE_RATE=0.1  // 10% of requests for performance monitoring
```

Tag every Sentry event with `company_id` and `user_id` for tenant-scoped debugging:

```php
// In SetCompanyContext middleware, after setting company
\Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($company, $user): void {
    $scope->setTag('company_id', $company->id);
    $scope->setTag('company_name', $company->name);
    $scope->setUser(['id' => $user->id, 'email' => $user->email]);
});
```

---

### `propaganistas/laravel-phone` ✅ Use

**Problem**: Phone numbers collected in HR (employee phone), CRM (contact phone), Communications (WhatsApp numbers) must be validated and stored in E.164 format. Raw strings like "06-12345678" cannot be used with WhatsApp Business API or SMS providers.

**Use in**: Any model with a phone field. Validate in Data classes, store as E.164.

```php
// In CreateContactData
#[PhoneNumber(region: 'NL')]
public readonly string $phone;

// Stored as: +31612345678
```

---

### `nunomaduro/larastan` ✅ Use (dev)

**Problem**: PHP 8.4 strict types are only as useful as static analysis enforcement. PHPStan/Larastan catches type errors, undefined properties, and wrong return types before runtime — catching entire categories of bugs that tests might miss.

**Level**: Start at level 6, push toward level 9 over time.

```bash
# phpstan.neon
includes:
    - vendor/nunomaduro/larastan/extension.neon

parameters:
    paths:
        - app
    level: 6
    ignoreErrors:
        - '#Call to an undefined method Illuminate\\Database\\Eloquent\\Builder#'
```

Run in CI: `php artisan analyse` (or `./vendor/bin/phpstan analyse`).

---

### `laravel/pint` ✅ Use (dev)

**Problem**: Code style consistency across a solo project is easy to lose. Pint enforces PSR-12 + Laravel conventions automatically.

```bash
# Run before every commit
./vendor/bin/pint

# pint.json — extends Laravel preset with project-specific rules
{
    "preset": "laravel",
    "rules": {
        "ordered_imports": true,
        "no_unused_imports": true
    }
}
```

Add to CI: `./vendor/bin/pint --test` (fails if any file would be reformatted).

---

### `pestphp/pest-plugin-livewire` ✅ Use (dev)

**Problem**: Filament resources are Livewire components. Testing them with raw HTTP assertions misses Livewire-specific behaviour (validation errors, form state, action results). This plugin adds Pest helpers for Livewire assertions.

```php
use function Pest\Livewire\livewire;

it('validates required fields on employee create', function () {
    livewire(CreateEmployee::class)
        ->fillForm(['first_name' => ''])
        ->call('create')
        ->assertHasFormErrors(['first_name' => 'required']);
});
```

---

### `dedoc/scramble` ✅ Use

**Problem**: API documentation written by hand goes stale. Scramble auto-generates OpenAPI 3.1 docs from Laravel routes + PHPDoc + Data class type hints. Zero maintenance.

**Docs URL**: `/docs/api` (restricted to admin or API key).

```php
// config/scramble.php
'info' => [
    'version' => '1.0.0',
    'title' => 'FlowFlex API',
],
'middleware' => ['auth:sanctum'],
```

No JSDoc needed — Scramble reads `spatie/laravel-data` Data class properties for request/response schemas automatically.

---

### `laravel/socialite` ⏳ Defer

**Problem**: Google/Microsoft SSO is the standard auth expectation for SME companies with 50+ employees using G Suite or Microsoft 365. Without SSO, IT departments push back on adoption.

**Defer until**: Phase 2. SSO adds OAuth complexity and per-provider testing overhead. For MVP, email/password + 2FA is sufficient.

**When adding**: add Google and Microsoft providers. Store `provider_id` and `provider` on `users`. Company admins can force SSO-only login in Company Settings.

---

### `brick/money` ✅ Use

**Problem**: We store all monetary values as integers (cents). When doing arithmetic on money values (add tax, apply discount, calculate totals), naive integer math fails for non-50-cent rounding. `brick/money` provides type-safe money arithmetic with correct rounding modes.

```php
use Brick\Money\Money;

$subtotal = Money::ofMinor(10000, 'EUR');  // €100.00
$tax = $subtotal->multipliedBy('0.21', RoundingMode::HALF_UP);  // €21.00
$total = $subtotal->plus($tax);            // €121.00

// Store back to DB
$invoice->total_cents = $total->getMinorAmount()->toInt(); // 12100
```

**Use in**: Finance (invoice totals, tax calculation), CRM (quote totals), HR (salary calculations).

---

### `htmlpurifier/htmlpurifier` ✅ Use

**Problem**: Tiptap rich text produces HTML that must be sanitized before storage to prevent stored XSS (see [[architecture/security]]). HTMLPurifier is the standard PHP library for this.

**Already documented in security.md but confirm as an explicit dependency.**

```bash
composer require ezyang/htmlpurifier
```

---

## Additional Packages (Round 3 — feature-specific)

### `simplesoftwareio/simple-qrcode` ✅ Use

**Problem**: Event tickets need QR codes for check-in scanning; registration check-in scans the QR. No QR generation in the core stack.

**Use in**: Events (ticket QR, registration check-in), optionally Workplace (visitor badges).

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
$png = QrCode::format('png')->size(300)->generate($registration->qr_code);
```

---

### `spatie/icalendar-generator` ✅ Use

**Problem**: Event registration confirmations include a calendar invite (`.ics`). Appointment scheduling (CRM) and meeting bookings also need `.ics` attachments.

**Use in**: Events (registration confirmation), CRM (appointment scheduling), Workplace (room booking confirmations).

```php
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

$ics = Calendar::create()
    ->event(Event::create($event->name)
        ->startsAt($event->start_at)
        ->endsAt($event->end_at))
    ->get();
```

---

### `spatie/laravel-tags` ✅ Use (promoted from Defer)

**Problem**: Polymorphic tagging is now referenced across CRM (contacts, deals), Support (tickets), DMS, Communications (conversations), Projects (tasks). It is broadly used — promote from Defer to Use.

**Use in**: CRM, Support, Projects, Communications, DMS.

---

### `spatie/laravel-schemaless-attributes` ✅ Use (promoted from Defer)

**Problem**: Custom per-company fields are needed in CRM (custom contact fields) and other domains. Promoted to Use — add when CRM custom fields are built.

**Use in**: CRM (custom contact/deal fields), extensible to other domains.

---

### `bacon/bacon-qr-code` ⏳ Defer

Lower-level QR library. `simplesoftwareio/simple-qrcode` wraps it with a cleaner API. Only use directly if `simple-qrcode` proves limiting.

---

### OCR for Document Intelligence — external API, no composer package

Document Intelligence (`ai.document-intelligence`) uses an external OCR/vision API (Google Document AI, AWS Textract, or the LLM provider's vision capability) — not a composer package. Configure as an API integration. No PHP OCR library bundled (Tesseract via `thiagoalessio/tesseract_ocr` is an option for self-hosted OCR but adds a system binary dependency — defer unless on-prem OCR is required).

---

## Summary Table

| Package | Status | First Use Domain |
|---|---|---|
| `spatie/laravel-model-states` | ✅ Use | HR (leave status) |
| `spatie/laravel-settings` | ✅ Use | Core Platform |
| `spatie/laravel-sluggable` | ✅ Use | DMS / Events |
| `spatie/laravel-tags` | ✅ Use | CRM, Support, Projects, Comms, DMS |
| `spatie/laravel-schemaless-attributes` | ✅ Use | CRM custom fields |
| `spatie/laravel-health` | ✅ Use | Core Platform |
| `spatie/laravel-query-builder` | ⏳ Defer | Public API layer |
| `lorisleiva/laravel-actions` | ✅ Use | All domains |
| `calebporzio/sushi` | ✅ Use | Core Platform |
| `pxlrbt/filament-excel` | ✅ Use | HR, Finance, CRM |
| `awcodes/filament-tiptap-editor` | ✅ Use | DMS, LMS, Marketing |
| `saade/filament-fullcalendar` | ✅ Use | HR, Events, Projects |
| `leandrocfe/filament-apex-charts` | ✅ Use | Analytics, Finance |
| `rmsramos/activitylog` | ✅ Use | Core Platform |
| `codewithdennis/filament-select-tree` | ⏳ Defer | HR, E-commerce |
| `livewire/volt` | ❌ Skip | — |
| `filament/spatie-laravel-media-library-plugin` | ✅ Core stack | Already confirmed |
| `bezhansalleh/filament-shield` | ✅ Core stack | Already confirmed |
| `spatie/laravel-pdf` | ✅ Use | Finance, HR, CRM |
| `maatwebsite/laravel-excel` | ✅ Use | Core (import), all export jobs |
| `spatie/laravel-backup` | ✅ Use | Infrastructure |
| `sentry/sentry-laravel` | ✅ Use | Production (all domains) |
| `propaganistas/laravel-phone` | ✅ Use | HR, CRM, Communications |
| `nunomaduro/larastan` | ✅ Use (dev) | All |
| `laravel/pint` | ✅ Use (dev) | All |
| `pestphp/pest-plugin-livewire` | ✅ Use (dev) | All Filament testing |
| `dedoc/scramble` | ✅ Use | API docs |
| `laravel/socialite` | ⏳ Defer | Auth (Phase 2) |
| `brick/money` | ✅ Use | Finance, CRM, HR |
| `ezyang/htmlpurifier` | ✅ Use | DMS, Communications, Marketing |
| `simplesoftwareio/simple-qrcode` | ✅ Use | Events (tickets, check-in) |
| `spatie/icalendar-generator` | ✅ Use | Events, CRM scheduling, Workplace |
| `bacon/bacon-qr-code` | ⏳ Defer | (underlying QR lib) |
