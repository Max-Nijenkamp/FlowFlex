---
type: roadmap-phase
color: "#F97316"
updated: 2026-07-03
---

# Phase 3 — v1 completions

Everything else tagged v1: remaining core modules + the full HR / Finance / CRM feature sets.

**40 modules · 120 features.** Work top-to-bottom; within a domain, modules are ordered fewest-dependencies-first. Tick a feature only after BOTH gates pass: AI gate (spec Test Checklist covered by green Pest tests + `/flowflex:verify`) AND your hand check.

## core

### API Clients — `core.api-clients`

Build: `/flowflex:start core.api-clients` · Done: `/flowflex:done core.api-clients` · Spec: [[../../domains/core/api-clients/_module|hub]] · Hard deps: none

- [ ] **Token Lifecycle (Create-once / Revoke)** ([[../../domains/core/api-clients/features/token-lifecycle|spec]]) — hand-check: open `ApiClientResource` at `/app/api-clients` (list + create).; admin creates a token → copies it once from the modal → uses it as a Bearer token; revokes a compromised token or all tokens.
- [ ] **Token Scopes (Abilities)** ([[../../domains/core/api-clients/features/token-scopes|spec]]) — hand-check: open the ability selector is part of the create form on `ApiClientResource` at `/app/api-clients`; enforcement itse; admin picks the abilities a token should carry at creation; at request time the middleware allows/denies per ability.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Data Import — `core.data-import`

Build: `/flowflex:start core.data-import` · Done: `/flowflex:done core.data-import` · Spec: [[../../domains/core/data-import/_module|hub]] · Hard deps: none

- [ ] **Column Mapping** ([[../../domains/core/data-import/features/column-mapping|spec]]) — hand-check: open Import wizard — `DataImportResource` Create page (`/app/data-imports/create`) rendered as a multi-step wizard ; pick a target → upload a file → app parses the header row → map each source column to a target field → click "Preview" to validate
- [ ] **Error Report** ([[../../domains/core/data-import/features/error-report|spec]]) — hand-check: open Import detail/view — `DataImportResource` view page (`/app/data-imports/{id}`); user opens a finished (or in-flight) import → reads live counts → clicks download to pull the tenant-scoped error CSV of failed ro
- [ ] **Importer Registry** ([[../../domains/core/data-import/features/importer-registry|spec]]) — hand-check: background — trigger it (domain modules call `ImporterRegistry::register($key, $importer)` during service-provider ), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Setup Wizard — `core.setup-wizard`

Build: `/flowflex:start core.setup-wizard` · Done: `/flowflex:done core.setup-wizard` · Spec: [[../../domains/core/setup-wizard/_module|hub]] · Hard deps: none

- [ ] **Onboarding Steps** ([[../../domains/core/setup-wizard/features/onboarding-steps|spec]]) — hand-check: open "Setup" (`/app/setup` — the owner's forced first-login route until complete).; fill step → validate → next (state saved); back to revisit; step 4 opens the
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Internationalisation — `core.i18n`

Build: `/flowflex:start core.i18n` · Done: `/flowflex:done core.i18n` · Spec: [[../../domains/core/i18n/_module|hub]] · Hard deps: core.settings

- [ ] **Locale Formatting** ([[../../domains/core/i18n/features/locale-formatting|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Locale Selection & SetLocale Middleware** ([[../../domains/core/i18n/features/locale-middleware|spec]]) — hand-check: background — trigger it (every authenticated panel request passes through `SetLocale` before the response renders. ), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Webhooks — `core.webhooks`

Build: `/flowflex:start core.webhooks` · Done: `/flowflex:done core.webhooks` · Spec: [[../../domains/core/webhooks/_module|hub]] · Hard deps: foundation.queues

- [ ] **Delivery Log** ([[../../domains/core/webhooks/features/delivery-log|spec]]) — hand-check: open a relation manager on `WebhookEndpointResource` at `/app/webhook-endpoints` (per-endpoint deliveries table).; user opens an endpoint, reviews recent deliveries, inspects a failed payload to debug the receiver; read-only (no edit).
- [ ] **Endpoint Management** ([[../../domains/core/webhooks/features/endpoint-management|spec]]) — hand-check: open `WebhookEndpointResource` at `/app/webhook-endpoints` (list + create/edit).; create endpoint (copy the one-time secret), pick events, save; toggle active; click "Send test" to verify; rotate secret (signed-d
- [ ] **Retry & Backoff** ([[../../domains/core/webhooks/features/retry-backoff|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Signed Delivery** ([[../../domains/core/webhooks/features/signed-delivery|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Health Monitoring — `core.health-monitoring`

Build: `/flowflex:start core.health-monitoring` · Done: `/flowflex:done core.health-monitoring` · Spec: [[../../domains/core/health-monitoring/_module|hub]] · Hard deps: foundation.queues, foundation.panels

- [ ] **Health Endpoint** ([[../../domains/core/health-monitoring/features/health-endpoint|spec]]) — hand-check: background — trigger it (HTTP GET (uptime monitors, load balancer probes, `SystemStatusPage` polling).), then check the visible result named in the spec
- [ ] **Pulse Dashboard** ([[../../domains/core/health-monitoring/features/pulse-dashboard|spec]]) — hand-check: open `/pulse` (Laravel Pulse dashboard) and `/horizon` (Laravel Horizon dashboard), both external routes linked fro; staff navigate from `/admin` → open Pulse/Horizon → inspect metrics, drill into a failed job's stack trace, retry a failed job (Ho
- [ ] **System Status Page** ([[../../domains/core/health-monitoring/features/system-status-page|spec]]) — hand-check: open `SystemStatusPage` (`/app` panel, owner). Route: Filament custom-page route under `/app`. View: `resources/vie; owner opens the page → sees per-check status → page polls every 60s and re-renders check states. Read-only, no actions.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Data Privacy — `core.data-privacy`

Build: `/flowflex:start core.data-privacy` · Done: `/flowflex:done core.data-privacy` · Spec: [[../../domains/core/data-privacy/_module|hub]] · Hard deps: core.settings, foundation.queues, core.files, core.rbac, core.billing

- [ ] **Consent Log** ([[../../domains/core/data-privacy/features/consent-log|spec]]) — hand-check: open consent-log viewer under `/app` (Settings nav) — a read-mostly `ConsentLog` resource *(assumed: the spec names; staff opens the log to audit who consented to what and when, and whether consent is still active; primarily read/filter (records a
- [ ] **Data Export** ([[../../domains/core/data-privacy/features/data-export|spec]]) — hand-check: open `DataExportPage` — custom Filament page under `/app` (Settings nav); owner clicks Export → action dispatches the build → page polls until `result_path`/ZIP is ready → owner downloads. Per-subject acc
- [ ] **DSAR Queue** ([[../../domains/core/data-privacy/features/dsar-queue|spec]]) — hand-check: open `DsarRequestResource` — list/create/view under `/app` (Settings nav group); staff logs a request → the row appears `received` with a 30-day countdown → staff clicks Process (moves to `in-progress`, dispatch
- [ ] **Erasure Cascade** ([[../../domains/core/data-privacy/features/erasure-cascade|spec]]) — hand-check: background — trigger it (`DsarRequestResource` Process action on a `request_type = erasure` request dispatches `Pro), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## crm

### Customer Segments — `crm.customer-segments`

Build: `/flowflex:start crm.customer-segments` · Done: `/flowflex:done crm.customer-segments` · Spec: [[../../domains/crm/customer-segments/_module|hub]] · Hard deps: none

- [ ] **Feature — Dynamic vs Static Segments** ([[../../domains/crm/customer-segments/features/dynamic-vs-static|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Segment Builder** ([[../../domains/crm/customer-segments/features/segment-builder|spec]]) — hand-check: open Segment Builder page → reached from `SegmentResource` create/edit (custom builder page or a schema section on ; add/remove rule, pick field + operator (validated against the allowed registry incl. custom-field keys), toggle AND/OR, watch prev
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Email Integration — `crm.email-integration`

Build: `/flowflex:start crm.email-integration` · Done: `/flowflex:done crm.email-integration` · Spec: [[../../domains/crm/email-integration/_module|hub]] · Hard deps: none

- [ ] **Email Tracking** ([[../../domains/crm/email-integration/features/email-tracking|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Inbound Sync** ([[../../domains/crm/email-integration/features/inbound-sync|spec]]) — hand-check: background — trigger it (`SyncMailboxesCommand` (every 10 min) → per-connection `SyncMailboxJob`. Synced mail surfa), then check the visible result named in the spec
- [ ] **OAuth Connection** ([[../../domains/crm/email-integration/features/oauth-connection|spec]]) — hand-check: open Connect Inbox settings page → `/crm/settings/email` *(assumed route)*; provider callback handled by `EmailOAut; click Connect → provider consent redirect → callback verifies `state` + PKCE → connection row created; toggle `sync_enabled` / `de
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Leads — `crm.leads`

Build: `/flowflex:start crm.leads` · Done: `/flowflex:done crm.leads` · Spec: [[../../domains/crm/leads/_module|hub]] · Hard deps: none

- [ ] **Convert to Deal** ([[../../domains/crm/leads/features/convert-to-deal|spec]]) — hand-check: open `LeadResource` list/view at `/crm/leads`; convert is a row action, hidden once `status = converted`.; row action → confirm modal → single-transaction convert (`ConvertLeadAction`) → deep-link to the new deal.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Sales Sequences — `crm.sales-sequences`

Build: `/flowflex:start crm.sales-sequences` · Done: `/flowflex:done crm.sales-sequences` · Spec: [[../../domains/crm/sales-sequences/_module|hub]] · Hard deps: none

- [ ] **Feature — A/B Testing** ([[../../domains/crm/sales-sequences/features/ab-testing|spec]]) — hand-check: open variant config within the sequence builder / `SequenceResource` step editor; results on a per-variant stats wi; add/edit variant A/B copy; enable A/B on an email step; view per-variant results to pick a winner.
- [ ] **Feature — Enrolment Triggers** ([[../../domains/crm/sales-sequences/features/enrolment-triggers|spec]]) — hand-check: background — trigger it (UpsellSequenceListener` (`InvoicePaid`); manual enrol via a Contact/Deal action.), then check the visible result named in the spec
- [ ] **Feature — Step Advancement** ([[../../domains/crm/sales-sequences/features/step-advancement|spec]]) — hand-check: background — trigger it (`AdvanceSequencesCommand` (every 15 min) → `SequenceService::advanceDue()`. Enrolment stat), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Forecasting — `crm.forecasting`

Build: `/flowflex:start crm.forecasting` · Done: `/flowflex:done crm.forecasting` · Spec: [[../../domains/crm/forecasting/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Feature — Forecast Categories** ([[../../domains/crm/forecasting/features/forecast-categories|spec]]) — hand-check: open `ForecastPage` (CRM panel, Forecasting nav group), route `/crm/forecast`.; rep sets a deal's forecast category (`SetForecastCategoryAction`) on open deals; managers view bottom-up totals per category and p
- [ ] **Feature — Weighted Pipeline** ([[../../domains/crm/forecasting/features/weighted-pipeline|spec]]) — hand-check: open `ForecastWidget` on `ForecastPage` (CRM panel, Forecasting nav group), route `/crm/forecast`.; filter by period and owner/team; hover chart segments for weighted values.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Price Management — `crm.price-management`

Build: `/flowflex:start crm.price-management` · Done: `/flowflex:done crm.price-management` · Spec: [[../../domains/crm/price-management/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Feature — CPQ Price Resolution** ([[../../domains/crm/price-management/features/cpq-resolution|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Volume Discounts** ([[../../domains/crm/price-management/features/volume-discounts|spec]]) — hand-check: open volume-discounts relation manager on `ProductResource`; route under `/crm/products/{product}` (CRM panel).; add a tier; edit percent/threshold; unique `(product_id, min_quantity)` enforced; only the highest qualifying tier applies at reso
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Revenue Intelligence — `crm.revenue-intelligence`

Build: `/flowflex:start crm.revenue-intelligence` · Done: `/flowflex:done crm.revenue-intelligence` · Spec: [[../../domains/crm/revenue-intelligence/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Feature — Deal Health Scoring** ([[../../domains/crm/revenue-intelligence/features/deal-health-scoring|spec]]) — hand-check: open health-score widget on the deal view + at-risk queue on `DealHealthResource` within `/crm`; scoring runs as a ; view score + explainable factors; open at-risk queue; manual recalc (admin)
- [ ] **Feature — Win/Loss Analysis** ([[../../domains/crm/revenue-intelligence/features/win-loss-analysis|spec]]) — hand-check: open `WinLossPage` (Intelligence nav group) + `RevenueIntelligenceDashboard` within `/crm`; date-range filter; drill into reason / competitor; results cached per range (TTL 1h)
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Appointment Scheduling — `crm.appointment-scheduling`

Build: `/flowflex:start crm.appointment-scheduling` · Done: `/flowflex:done crm.appointment-scheduling` · Spec: [[../../domains/crm/appointment-scheduling/_module|hub]] · Hard deps: core.billing, core.rbac, foundation.email

- [ ] **Feature — Calendar Sync** ([[../../domains/crm/appointment-scheduling/features/calendar-sync|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Public Booking** ([[../../domains/crm/appointment-scheduling/features/public-booking|spec]]) — hand-check: open `Booking/Show.vue` + `Booking/Confirm.vue` — route `/book/{company-slug}/{meeting-slug}`; pick a day, pick a slot, submit name/email/notes; Stripe PaymentIntent for priced types
- [ ] **Feature — Round-Robin Assignment** ([[../../domains/crm/appointment-scheduling/features/round-robin|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Deal Rooms — `crm.deal-rooms`

Build: `/flowflex:start crm.deal-rooms` · Done: `/flowflex:done crm.deal-rooms` · Spec: [[../../domains/crm/deal-rooms/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [ ] **Feature — Engagement Tracking** ([[../../domains/crm/deal-rooms/features/engagement-tracking|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Tokenised Access** ([[../../domains/crm/deal-rooms/features/tokenised-access|spec]]) — hand-check: open `DealRoom/Show.vue` — route `/room/{token}`; buyer opens documents (view logged), toggles buyer-side action items; no uploads in v1 *(assumed)*
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Referral Program — `crm.referral-program`

Build: `/flowflex:start crm.referral-program` · Done: `/flowflex:done crm.referral-program` · Spec: [[../../domains/crm/referral-program/_module|hub]] · Hard deps: core.billing, core.rbac, core.notifications

- [ ] **Feature — Referral Tracking** ([[../../domains/crm/referral-program/features/referral-tracking|spec]]) — hand-check: open `ReferralResource` within `/crm`; public capture route *(assumed — currently under-specified, see ../unknowns); staff view/filter referrals; referee submits email on the capture route (fraud checks at register)
- [ ] **Feature — Reward Fulfilment** ([[../../domains/crm/referral-program/features/reward-fulfilment|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CRM Contracts — `crm.contracts`

Build: `/flowflex:start crm.contracts` · Done: `/flowflex:done crm.contracts` · Spec: [[../../domains/crm/contracts/_module|hub]] · Hard deps: core.billing, core.rbac, core.files, core.notifications

- [ ] **Feature — Contract Lifecycle** ([[../../domains/crm/contracts/features/contract-lifecycle|spec]]) — hand-check: open `ContractResource` (list + view/edit) in the CRM panel; lifecycle actions (Send, Sign-off, Terminate) as recor; create-from-deal prefill; Send (`draft→sent`); Sign-off (PDF upload → `sent→signed`, sets `signed_at`); Terminate (required reason
- [ ] **Feature — Renewal Tracking** ([[../../domains/crm/contracts/features/renewal-tracking|spec]]) — hand-check: open Renewals page (Pipeline nav group), route `/crm/renewals`; plus `ContractRenewalWidget` on the CRM dashboard.; renew (set new end/renewal dates) or terminate per row; filter by window; jump to the contract.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Quotes — `crm.quotes`

Build: `/flowflex:start crm.quotes` · Done: `/flowflex:done crm.quotes` · Spec: [[../../domains/crm/quotes/_module|hub]] · Hard deps: crm.deals, core.billing, core.rbac, foundation.queues

- [ ] **Quote PDF Generation** ([[../../domains/crm/quotes/features/pdf-generation|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Public Accept / Decline** ([[../../domains/crm/quotes/features/public-acceptance|spec]]) — hand-check: open `Quotes/Accept.vue` at signed route `GET /quote/{quote}/accept?signature=…` (guest guard, throttled), served b; prospect clicks Accept or Decline; single-quote-scoped token; expired token/quote shows an "expired" state.
- [ ] **Quote Versioning** ([[../../domains/crm/quotes/features/versioning|spec]]) — hand-check: open `QuoteResource` view page; "New version" action + a version-history relation/repeatable in the infolist.; "New version" clones line items into a new version and locks the old; opening a locked version is read-only.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## finance

### Fixed Assets — `finance.assets`

Build: `/flowflex:start finance.assets` · Done: `/flowflex:done finance.assets` · Spec: [[../../domains/finance/fixed-assets/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac

- [ ] **Feature — Depreciation** ([[../../domains/finance/fixed-assets/features/depreciation|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Disposal** ([[../../domains/finance/fixed-assets/features/disposal|spec]]) — hand-check: open dispose action on `FixedAssetResource` under `/finance/assets`.; trigger dispose, enter proceeds + date, confirm; blocked if already disposed.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Tax Management — `finance.tax`

Build: `/flowflex:start finance.tax` · Done: `/flowflex:done finance.tax` · Spec: [[../../domains/finance/tax-management/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac

- [ ] **Feature — Tax Rates & Classes** ([[../../domains/finance/tax-management/features/tax-rates|spec]]) — hand-check: open `TaxRateResource` (+ tax classes) — `/finance/tax/rates`; create/edit rates and classes; toggle active; set reverse-charge; soft-delete referenced rates.
- [ ] **Feature — Tax Period Report & VAT Return** ([[../../domains/finance/tax-management/features/tax-report|spec]]) — hand-check: open `TaxReturnPage` — `/finance/tax/return`; pick a period; review output − input = net payable; file the period (snapshots + locks it).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Accounts Payable — `finance.ap`

Build: `/flowflex:start finance.ap` · Done: `/flowflex:done finance.ap` · Spec: [[../../domains/finance/accounts-payable/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.files

- [ ] **AP Aging Report** ([[../../domains/finance/accounts-payable/features/ap-aging|spec]]) — hand-check: open "AP Aging" (`ApAgingPage`) under `/finance/ap/aging`; filter by supplier/date, drill into a bill; drives payment prioritisation + the early-payment-discount window (no writes)
- [ ] **Bill Approval Workflow** ([[../../domains/finance/accounts-payable/features/bill-approval|spec]]) — hand-check: open `BillResource` under `/finance/ap/bills` (list + view/edit, approve action, status badge); approve/reject via action; status machine `draft → approved → scheduled → paid` drives available actions
- [ ] **Payment Runs** ([[../../domains/finance/accounts-payable/features/payment-runs|spec]]) — hand-check: open "Payment run" under `/finance/ap/payment-runs`; pick bills → preview batch total (line-sum reconciliation) → execute run (atomic, all-or-none); download SEPA/CSV
- [ ] **3-Way Match** ([[../../domains/finance/accounts-payable/features/three-way-match|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Accounts Receivable — `finance.ar`

Build: `/flowflex:start finance.ar` · Done: `/flowflex:done finance.ar` · Spec: [[../../domains/finance/accounts-receivable/_module|hub]] · Hard deps: finance.invoicing, core.billing, core.rbac, core.notifications

- [ ] **Feature — AR Aging Report** ([[../../domains/finance/accounts-receivable/features/aging-report|spec]]) — hand-check: open "AR Aging" — `ArAgingPage` (`/finance/ar/aging`); pick an optional customer filter; drill an account → its open invoices
- [ ] **Feature — Automated Dunning** ([[../../domains/finance/accounts-receivable/features/dunning|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Payment Allocation** ([[../../domains/finance/accounts-receivable/features/payment-allocation|spec]]) — hand-check: open "Allocate payment" slide-over (`/finance/ar/allocate`); pick customer, enter per-invoice amounts, live validation that `sum(allocations) === amount_cents` and each ≤ that invoice's open 
- [ ] **Feature — Write-Off** ([[../../domains/finance/accounts-receivable/features/write-off|spec]]) — hand-check: open "Write off" action + modal from an AR invoice list (`/finance/ar`); select an uncollectable invoice → open write-off modal → enter reason → confirm; amount is never caller-supplied
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Budgets — `finance.budgets`

Build: `/flowflex:start finance.budgets` · Done: `/flowflex:done finance.budgets` · Spec: [[../../domains/finance/budgets/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.notifications

- [ ] **Feature — Budget Versioning & Approval** ([[../../domains/finance/budgets/features/budget-versioning|spec]]) — hand-check: open `BudgetResource` under `/finance/budgets`; edit budget lines in-grid, `approve` (draft → approved, lines become immutable), `revise` (spawns a new immutable version), `copyF
- [ ] **Feature — Budget vs Actual Variance** ([[../../domains/finance/budgets/features/variance-tracking|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Financial Reporting — `finance.reporting`

Build: `/flowflex:start finance.reporting` · Done: `/flowflex:done finance.reporting` · Spec: [[../../domains/finance/financial-reporting/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.settings

- [ ] **Feature — Core Financial Statements** ([[../../domains/finance/financial-reporting/features/statements|spec]]) — hand-check: open `ProfitLossPage` (`/finance/reports/pnl`), `BalanceSheetPage` (`/finance/reports/balance-sheet`), `CashFlowSta; pick period, drill a line down to journal entries, export Excel/PDF (rate-limited).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Forecasting — `finance.forecasting`

Build: `/flowflex:start finance.forecasting` · Done: `/flowflex:done finance.forecasting` · Spec: [[../../domains/finance/forecasting/_module|hub]] · Hard deps: finance.ledger, finance.budgets, core.billing, core.rbac

- [ ] **Feature — Scenario Modelling & Three-Way Comparison** ([[../../domains/finance/forecasting/features/scenario-modelling|spec]]) — hand-check: open `ForecastResource` under `/finance/forecasting`; `ForecastComparisonPage` under `/finance/forecasting/comparis; edit assumptions + projected lines, switch scenario, compare projected vs actual vs budget.
- [ ] **Feature — Seed From Actuals** ([[../../domains/finance/forecasting/features/seed-from-actuals|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Multi-Currency — `finance.currency`

Build: `/flowflex:start finance.currency` · Done: `/flowflex:done finance.currency` · Spec: [[../../domains/finance/multi-currency/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.settings

- [ ] **Feature — Exchange Rates** ([[../../domains/finance/multi-currency/features/exchange-rates|spec]]) — hand-check: open `ExchangeRateResource` under `/finance/currency/rates`; `CurrencyResource` under `/finance/currency/currencies; enter a manual rate for a (from, to, effective_date); activate/deactivate currencies; browse rate history.
- [ ] **Feature — FX Gain/Loss** ([[../../domains/finance/multi-currency/features/fx-gain-loss|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Cash Flow — `finance.cashflow`

Build: `/flowflex:start finance.cashflow` · Done: `/flowflex:done finance.cashflow` · Spec: [[../../domains/finance/cash-flow/_module|hub]] · Hard deps: finance.invoicing, finance.bank, core.billing, core.rbac, core.notifications

- [ ] **Feature — 13-Week Cash Flow Projection** ([[../../domains/finance/cash-flow/features/cash-flow-projection|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## hr

### Compensation & Benefits — `hr.compensation-benefits`

Build: `/flowflex:start hr.compensation-benefits` · Done: `/flowflex:done hr.compensation-benefits` · Spec: [[../../domains/hr/compensation-benefits/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Feature — Benefit Enrollment** ([[../../domains/hr/compensation-benefits/features/benefit-enrollment|spec]]) — hand-check: open "Benefit Enrollments" (`/hr/benefit-enrollments`); HR enrolls an employee in an active benefit and unenrolls (sets `unenrolled_at`); double active enrollment rejected
- [ ] **Feature — Benefits Catalog** ([[../../domains/hr/compensation-benefits/features/benefits-catalog|spec]]) — hand-check: open "Benefits" (`/hr/benefits`) — Payroll nav group; HR defines benefits (insurance/pension/allowance) with `cost_per_month_cents` and `employer_contribution_cents`
- [ ] **Feature — Compensation Bands** ([[../../domains/hr/compensation-benefits/features/compensation-bands|spec]]) — hand-check: open "Compensation Bands" (`/hr/compensation-bands`); HR sets min/mid/max per grade/department; cross-field validation `min ≤ mid ≤ max`; reviews compa-ratio of employees vs midpoint
- [ ] **Feature — Salary History** ([[../../domains/hr/compensation-benefits/features/salary-history|spec]]) — hand-check: open employee-view relation tab "Salary History" (`/hr/employees/{employee}` → Salary History) + a bulk comp-review; HR adjusts a salary (atomically updates payroll profile + appends one history row); runs annual bulk comp review (per-row try/catc
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Employee Self-Service — `hr.employee-self-service`

Build: `/flowflex:start hr.employee-self-service` · Done: `/flowflex:done hr.employee-self-service` · Spec: [[../../domains/hr/employee-self-service/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **My Documents** ([[../../domains/hr/employee-self-service/features/my-documents|spec]]) — hand-check: open "My Documents" (`/app/my-documents`); browse own documents; preview; download.
- [ ] **My Leave** ([[../../domains/hr/employee-self-service/features/my-leave|spec]]) — hand-check: open "My Leave" (`/app/my-leave`); view balance; submit a leave request; browse own request history; open a request row for detail.
- [ ] **My Onboarding** ([[../../domains/hr/employee-self-service/features/my-onboarding|spec]]) — hand-check: open "My Onboarding" (`/app/my-onboarding`); view plan progress; open a task; mark an employee-role task complete.
- [ ] **My Payslips** ([[../../domains/hr/employee-self-service/features/my-payslips|spec]]) — hand-check: open "My Payslips" (`/app/my-payslips`); browse own payslips; download a payslip PDF.
- [ ] **My Profile** ([[../../domains/hr/employee-self-service/features/my-profile|spec]]) — hand-check: open "My Profile" (`/app/my-profile`); view own record; toggle edit on own-slice fields; update phone/personal_email/emergency contacts; upload profile photo.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Workforce Planning — `hr.workforce-planning`

Build: `/flowflex:start hr.workforce-planning` · Done: `/flowflex:done hr.workforce-planning` · Spec: [[../../domains/hr/workforce-planning/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Feature — Budget vs Actual** ([[../../domains/hr/workforce-planning/features/budget-vs-actual|spec]]) — hand-check: open "Workforce Planning" (`/hr/workforce-planning`) — the `WorkforcePlanningDashboard` comparison view; switch scenario preset multiplier; change period; read plan-vs-actual variance; column set adapts to soft-dep availability
- [ ] **Feature — Headcount Plans** ([[../../domains/hr/workforce-planning/features/headcount-plans|spec]]) — hand-check: open "Headcount Plans" (`/hr/headcount-plans`); create a plan per department+period, edit targets/budget, soft-delete; duplicate `(company, department, period)` rejected on save
- [ ] **Feature — Planned Roles** ([[../../domains/hr/workforce-planning/features/planned-roles|spec]]) — hand-check: open "Planned Roles" (`/hr/planned-roles`); create a role under a plan; run the Approve action (→ approved, triggers requisition handoff if recruitment active); run Mark Fill
- [ ] **Feature — Requisition Handoff** ([[../../domains/hr/workforce-planning/features/requisition-handoff|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### HR Analytics — `hr.hr-analytics`

Build: `/flowflex:start hr.hr-analytics` · Done: `/flowflex:done hr.hr-analytics` · Spec: [[../../domains/hr/hr-analytics/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac

- [ ] **Cost Analytics** ([[../../domains/hr/hr-analytics/features/cost-analytics|spec]]) — hand-check: open hosted on the "HR Analytics" dashboard (`/hr/analytics`) as a cost chart widget; change the header period filter to re-scope; hover a band for its aggregate cost tooltip
- [ ] **Headcount Analytics** ([[../../domains/hr/hr-analytics/features/headcount-analytics|spec]]) — hand-check: open hosted on the "HR Analytics" dashboard (`/hr/analytics`) — this feature ships several apex-chart widgets, not ; change the header period filter to re-scope all charts; hover a series for the point tooltip; export chart PNG / data CSV from the
- [ ] **Leave Analytics** ([[../../domains/hr/hr-analytics/features/leave-analytics|spec]]) — hand-check: open hosted on the "HR Analytics" dashboard (`/hr/analytics`) as `LeaveUtilisationWidget`; change the header period filter to re-scope; hover a bar for taken/allocated tooltip per leave type
- [ ] **Turnover & Attrition** ([[../../domains/hr/hr-analytics/features/turnover-attrition|spec]]) — hand-check: open hosted on the "HR Analytics" dashboard (`/hr/analytics`) as `TurnoverWidget`; change the header period filter to recompute the rate; hover for the terminations/avg-headcount breakdown tooltip
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Org Chart — `hr.org-chart`

Build: `/flowflex:start hr.org-chart` · Done: `/flowflex:done hr.org-chart` · Spec: [[../../domains/hr/org-chart/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac

- [ ] **Feature — Department Filter** ([[../../domains/hr/org-chart/features/department-filter|spec]]) — hand-check: open filter control on the Org Chart page (`/hr/org-chart`) — part of `OrgChartPage`, not a standalone page.; Pick a department to scope the tree; clear to return to the full company tree.
- [ ] **Feature — Export (PNG/PDF)** ([[../../domains/hr/org-chart/features/export|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Manager Reassignment** ([[../../domains/hr/org-chart/features/manager-reassignment|spec]]) — hand-check: open reassign action on the Org Chart page (`/hr/org-chart`); Open the reassign action on a node, pick a new manager, save.
- [ ] **Feature — Org Tree Visualization** ([[../../domains/hr/org-chart/features/org-tree-visualization|spec]]) — hand-check: open "Org Chart" (`/hr/org-chart`); Expand/collapse nodes, click node → open employee profile, search to highlight a node.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Time & Attendance — `hr.time-attendance`

Build: `/flowflex:start hr.time-attendance` · Done: `/flowflex:done hr.time-attendance` · Spec: [[../../domains/hr/time-attendance/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac

- [ ] **Feature — Overtime Detection** ([[../../domains/hr/time-attendance/features/overtime-detection|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Time Entries (Clock-in/out & Manual Logging)** ([[../../domains/hr/time-attendance/features/time-entries|spec]]) — hand-check: open "Time Entries" (`/hr/time-entries`); clock in / clock out (widget); log a manual entry (`logEntry`); edit break minutes; view daily totals.
- [ ] **Feature — Timesheet Approval Workflow** ([[../../domains/hr/time-attendance/features/timesheet-approval-workflow|spec]]) — hand-check: open "Timesheet Approvals" (`/hr/timesheets`); `submitWeek` (all days closed, locks entries); manager approve (fires `TimesheetApproved`) or reject (returns to employee, unlocks
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### DEI Metrics — `hr.dei-metrics`

Build: `/flowflex:start hr.dei-metrics` · Done: `/flowflex:done hr.dei-metrics` · Spec: [[../../domains/hr/dei-metrics/_module|hub]] · Hard deps: hr.profiles, core.privacy, core.billing, core.rbac

- [ ] **Anonymized snapshots** ([[../../domains/hr/dei-metrics/features/anonymized-snapshots|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Consent management** ([[../../domains/hr/dei-metrics/features/consent-management|spec]]) — hand-check: open consent controls on "My Profile" (`/app/my-profile`) DEI section — same self-service page as declaration; tick consent to enable submission; click Withdraw → confirm → `WithdrawDeiConsentAction` deletes own attribute rows and logs the w
- [ ] **Encrypted DEI attributes** ([[../../domains/hr/dei-metrics/features/dei-attributes-encrypted|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **DEI dashboard (aggregates)** ([[../../domains/hr/dei-metrics/features/dei-dashboard-aggregates|spec]]) — hand-check: open "DEI Dashboard" (`/hr/dei-dashboard`) — `DeiDashboardPage`; pick period/dimension to view a snapshot; read aggregate charts; no drill-down to individuals is ever possible
- [ ] **Self-declaration** ([[../../domains/hr/dei-metrics/features/self-declaration|spec]]) — hand-check: open a DEI section on "My Profile" (`/app/my-profile`) — employee self-service, not the `/hr` staff panel; employee ticks consent, picks values from the allowed option lists, submits; own-record only — cannot declare for anyone else; can
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Employee Feedback — `hr.employee-feedback`

Build: `/flowflex:start hr.employee-feedback` · Done: `/flowflex:done hr.employee-feedback` · Spec: [[../../domains/hr/employee-feedback/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Feedback Requests** ([[../../domains/hr/employee-feedback/features/feedback-requests|spec]]) — hand-check: open request action on the feedback area (`/hr/feedback` → "Request feedback"); employee requests feedback from a colleague/manager (`RequestFeedbackAction`); the target is notified and responds by creating fee
- [ ] **Feedback Records** ([[../../domains/hr/employee-feedback/features/feedback|spec]]) — hand-check: open "Feedback" (`/hr/feedback`); employee gives feedback (praise/constructive/coaching-note) to a colleague; visibility auto-forced by type; can link a goal/cycle 
- [ ] **1-on-1 Meetings** ([[../../domains/hr/employee-feedback/features/one-on-ones|spec]]) — hand-check: open "1-on-1s" (`/hr/one-on-ones`); manager logs a 1:1 (`LogOneOnOneAction`; reportee must report to the current manager *(assumed)*), edits agenda/notes, toggles act
- [ ] **Recognition Feed** ([[../../domains/hr/employee-feedback/features/recognition-feed|spec]]) — hand-check: open "Recognition" (`/hr/recognition`) — `RecognitionFeedPage`; team members read the feed; new public praise appears live via polling; constructive/coaching-note feedback never surfaces
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Payroll — `hr.payroll`

Build: `/flowflex:start hr.payroll` · Done: `/flowflex:done hr.payroll` · Spec: [[../../domains/hr/payroll/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Feature — Deductions & Employer Cost** ([[../../domains/hr/payroll/features/deductions|spec]]) — hand-check: open "Deduction Types" (`/hr/deduction-types`); HR defines percent/flat deduction types, toggles `is_employer_contribution`, and reviews per-run employer cost on the run page
- [ ] **Feature — Event-Driven Inputs** ([[../../domains/hr/payroll/features/event-driven-inputs|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Ledger Journal Posting** ([[../../domains/hr/payroll/features/ledger-journal-posting|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Payroll Run Lifecycle** ([[../../domains/hr/payroll/features/payroll-run-lifecycle|spec]]) — hand-check: open "Payroll Runs" (`/hr/payroll-runs`) + run detail (`/hr/payroll-runs/{run}`); create a draft run for a period; process (dispatches `GeneratePayslipsJob`); approve (approver ≠ creator *(assumed)*, fires event,
- [ ] **Feature — Payslip Generation & PDF** ([[../../domains/hr/payroll/features/payslip-generation|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Salary, IBAN & Amount Encryption** ([[../../domains/hr/payroll/features/salary-iban-encryption|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Performance Reviews — `hr.performance-reviews`

Build: `/flowflex:start hr.performance-reviews` · Done: `/flowflex:done hr.performance-reviews` · Spec: [[../../domains/hr/performance-reviews/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Feature — Goals & OKRs** ([[../../domains/hr/performance-reviews/features/goals-okrs|spec]]) — hand-check: open "My Goals" (`/hr/my-goals`) *(assumed self-service nav)*; HR views goals within the owning review; employee updates own goal progress (0–100); HR/manager sets ratings during calibration
- [ ] **Feature — PDF Export** ([[../../domains/hr/performance-reviews/features/pdf-export|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Review Cycles** ([[../../domains/hr/performance-reviews/features/review-cycles|spec]]) — hand-check: open "Review Cycles" (`/hr/review-cycles`); HR creates a cycle, activates it (generates the self+manager review matrix), advances through calibration to finalised
- [ ] **Feature — Review Forms & State Machine** ([[../../domains/hr/performance-reviews/features/review-forms-state-machine|spec]]) — hand-check: open review fill flow within `ReviewResource` (`/hr/reviews/{review}`); state transitions triggered from `ReviewCyc; HR advances cycle state (locks submissions, freezes ratings, triggers PDFs); reviewers fill/submit only while `active`
- [ ] **Feature — Self & Manager Reviews (360)** ([[../../domains/hr/performance-reviews/features/self-and-manager-reviews|spec]]) — hand-check: open "Reviews" (`/hr/reviews`) + review submit/compare view (`/hr/reviews/{review}`); reviewer submits their own assigned review while cycle is `active`; HR calibrates ratings (`CalibrateRatingData`, note required on
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Shift Scheduling — `hr.shift-scheduling`

Build: `/flowflex:start hr.shift-scheduling` · Done: `/flowflex:done hr.shift-scheduling` · Spec: [[../../domains/hr/shift-scheduling/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Feature — Coverage Gaps** ([[../../domains/hr/shift-scheduling/features/coverage-gaps|spec]]) — hand-check: open "Shift Schedule" (`/hr/shift-schedule`) — gaps highlighted inline on the calendar; click a highlighted gap to assign an employee (delegates to shift-assignment); scan the week for uncovered roles before publishing
- [ ] **Feature — Leave Conflict Blocking** ([[../../domains/hr/shift-scheduling/features/leave-conflict-blocking|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Shift Assignment** ([[../../domains/hr/shift-scheduling/features/shift-assignment|spec]]) — hand-check: open "Shift Schedule" (`/hr/shift-schedule`) — assignment happens inline on the calendar page; `createShift` with optional `employee_id`; drag a shift onto an employee row to assign; reassign/clear assignment.
- [ ] **Feature — Shift Calendar** ([[../../domains/hr/shift-scheduling/features/shift-calendar|spec]]) — hand-check: open "Shift Schedule" (`/hr/shift-schedule`); drag-drop to assign/move shifts; publish the week (`publishWeek`, notifies assigned employees); copy previous week (`copyWeek`).
- [ ] **Feature — Swap Requests** ([[../../domains/hr/shift-scheduling/features/swap-requests|spec]]) — hand-check: open "Swap Requests" (`/hr/shift-swap-requests`); `requestSwap` (pick own shift + recipient); recipient `acceptSwap`; manager `approveSwap` (reassigns shifts, sets `manager_approve
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Recruitment — `hr.recruitment`

Build: `/flowflex:start hr.recruitment` · Done: `/flowflex:done hr.recruitment` · Spec: [[../../domains/hr/recruitment/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.files, core.notifications

- [ ] **Feature — Applicant Pipeline (Kanban)** ([[../../domains/hr/recruitment/features/applicant-pipeline-kanban|spec]]) — hand-check: open "Applicant Pipeline" (`/hr/applicant-pipeline`); drag a card between columns = `moveStage($applicantId, $state)`, guarded by `ApplicantState`; `→ rejected` optionally sends reject
- [ ] **Feature — Applicant → Employee Conversion (Hire)** ([[../../domains/hr/recruitment/features/applicant-to-employee-conversion|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Interview Scheduling** ([[../../domains/hr/recruitment/features/interview-scheduling|spec]]) — hand-check: open "Interviews" (`/hr/interviews`); schedule/edit an interview; assign interviewers; record outcome + notes; interviewer notification mails queued.
- [ ] **Feature — Job Requisitions** ([[../../domains/hr/recruitment/features/job-requisitions|spec]]) — hand-check: open "Job Requisitions" (`/hr/job-requisitions`); `openRequisition` (create); edit; flip status draft → open → closed; toggle careers-page publish.
- [ ] **Feature — Offers** ([[../../domains/hr/recruitment/features/offers|spec]]) — hand-check: open "Offers" (`/hr/offers`); `makeOffer` (create draft); `sendOffer` (draft → sent, sets `sent_at`); mark accepted/declined (records `accepted_at`).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean
