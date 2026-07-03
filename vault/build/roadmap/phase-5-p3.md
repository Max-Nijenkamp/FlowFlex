---
type: roadmap-phase
color: "#F97316"
updated: 2026-07-03
---

# Phase 5 — p3 + later domains

Marketing, Operations, Procurement, IT, Legal, Analytics, AI, LMS, Customer Success, E-commerce, Events, Workplace remainder.

**76 modules · 240 features.** Work top-to-bottom; within a domain, modules are ordered fewest-dependencies-first. Tick a feature only after BOTH gates pass: AI gate (spec Test Checklist covered by green Pest tests + `/flowflex:verify`) AND your hand check.

## ai

### AI Model Configuration — `ai.model-config`

Build: `/flowflex:start ai.model-config` · Done: `/flowflex:done ai.model-config` · Spec: [[../../domains/ai/model-config/_module|hub]] · Hard deps: none

- [ ] **LLM Gateway** ([[../../domains/ai/model-config/features/llm-gateway|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Provider Config** ([[../../domains/ai/model-config/features/provider-config|spec]]) — hand-check: open "AI Model Configuration" (`/ai` → Settings → AI Model Configuration) *(route slug assumed)*; pick provider → model options refresh; save → API key test-call validates before persist; key field shows "•••• set" placeholder, 
- [ ] **Usage Dashboard** ([[../../domains/ai/model-config/features/usage-dashboard|spec]]) — hand-check: open "AI Usage" (`/ai` → Settings → Usage) *(route slug assumed)*; period filter (this month / last 30d / custom); toggle by-feature vs by-user breakdown; hover chart segment → tooltip with tokens 
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### AI Copilot — `ai.copilot`

Build: `/flowflex:start ai.copilot` · Done: `/flowflex:done ai.copilot` · Spec: [[../../domains/ai/copilot/_module|hub]] · Hard deps: ai.config, core.billing, core.rbac

- [ ] **Chat Console** ([[../../domains/ai/copilot/features/chat-console|spec]]) — hand-check: open "Copilot" (`/app/ai/copilot`) *(route slug assumed)*; type + send → optimistic user bubble → streaming assistant reply; click a conversation → load its history; new chat → fresh thread
- [ ] **Draft & Summarise** ([[../../domains/ai/copilot/features/draft-and-summarise|spec]]) — hand-check: open within "Copilot" (`/app/ai/copilot`) — invoked via prompt or quick-action chips ("Draft reply", "Summarise thi; click "Summarise this record" from a panel context → context passed → streamed summary; edit/copy the generated draft.
- [ ] **Tool Registry** ([[../../domains/ai/copilot/features/tool-registry|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Workflow Builder — `ai.workflow-builder`

Build: `/flowflex:start ai.workflow-builder` · Done: `/flowflex:done ai.workflow-builder` · Spec: [[../../domains/ai/workflow-builder/_module|hub]] · Hard deps: core.billing, core.rbac, foundation.queues

- [ ] **Action Registry** ([[../../domains/ai/workflow-builder/features/action-registry|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Flow Editor** ([[../../domains/ai/workflow-builder/features/flow-editor|spec]]) — hand-check: open "Workflow builder" (`/app/ai/workflows/builder`) *(route slug assumed)*; add node → pick from the (module-gated) trigger/action picker → configure; connect nodes; save → graph validated; toggle active; o
- [ ] **Run History** ([[../../domains/ai/workflow-builder/features/run-history|spec]]) — hand-check: open "Run history" (`/app/ai/workflows/runs`) *(route slug assumed)*; per-run detail at `/app/ai/workflows/runs/{id; filter by workflow / status / date; open a run → node-by-node trace; copy a failed node's error.
- [ ] **Trigger Registry** ([[../../domains/ai/workflow-builder/features/trigger-registry|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Document Intelligence — `ai.document-intelligence`

Build: `/flowflex:start ai.document-intelligence` · Done: `/flowflex:done ai.document-intelligence` · Spec: [[../../domains/ai/document-intelligence/_module|hub]] · Hard deps: ai.config, core.billing, core.rbac, core.files, foundation.queues

- [ ] **Apply to Record** ([[../../domains/ai/document-intelligence/features/apply-to-record|spec]]) — hand-check: open Apply button within "Review extraction" (`/app/ai/extractions/{id}/review`) *(route slug assumed)*; click Apply → confirm modal → target service call → on success, link to the created record; on validation failure, show the target
- [ ] **Review & Confirm** ([[../../domains/ai/document-intelligence/features/review-and-confirm|spec]]) — hand-check: open "Review extraction" (`/app/ai/extractions/{id}/review`) *(route slug assumed)*; click a flagged field → edit inline; hover a confidence chip → exact score; Confirm → validates presence of required fields → `sta
- [ ] **Upload & Extract** ([[../../domains/ai/document-intelligence/features/upload-and-extract|spec]]) — hand-check: open "Extractions" (`/app/ai/extractions`) *(route slug assumed)*; upload file → row appears as `processing`; status badge updates when the job finishes (poll/refresh — no realtime broadcast specce
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## analytics

### Custom Dashboards — `analytics.dashboards`

Build: `/flowflex:start analytics.dashboards` · Done: `/flowflex:done analytics.dashboards` · Spec: [[../../domains/analytics/dashboards/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Dashboard Builder** ([[../../domains/analytics/dashboards/features/dashboard-builder|spec]]) — hand-check: open `DashboardBuilderPage` at `/analytics/dashboards/{dashboard}/build` *(route assumed)* — custom Filament page (; drag widget from picker → drop on grid → configure metric + filters in a slide-over; drag/resize placed widgets → optimistic layou
- [ ] **Dashboard Sharing** ([[../../domains/analytics/dashboards/features/dashboard-sharing|spec]]) — hand-check: open action on `DashboardResource` + the builder top bar.; owner flips the share toggle → dashboard becomes team-visible read-only (optimistic + confirm); non-owner opening a shared dashboa
- [ ] **MetricRegistry** ([[../../domains/analytics/dashboards/features/metric-registry|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Widget Rendering** ([[../../domains/analytics/dashboards/features/widget-rendering|spec]]) — hand-check: open none of its own; rendered on dashboard-builder's canvas and on shared dashboards.; hover → exact values; manual refresh → re-resolve (skeleton while loading); date-range change (dashboard-level) → all widgets re-r
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Report Builder — `analytics.report-builder`

Build: `/flowflex:start analytics.report-builder` · Done: `/flowflex:done analytics.report-builder` · Spec: [[../../domains/analytics/report-builder/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Report Composer** ([[../../domains/analytics/report-builder/features/report-composer|spec]]) — hand-check: open `ReportBuilderPage` at `/analytics/reports/build` *(route assumed)*.; select source -> column list populates; toggle columns; add filter rows (field/operator/value, AND-OR); set grouping + aggregation
- [ ] **Report Runner** ([[../../domains/analytics/report-builder/features/report-runner|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Saved Reports** ([[../../domains/analytics/report-builder/features/saved-reports|spec]]) — hand-check: open `ReportResource` at `/analytics/reports`.
- [ ] **Source Registry** ([[../../domains/analytics/report-builder/features/source-registry|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Cross-Domain Data Views — `analytics.data-views`

Build: `/flowflex:start analytics.data-views` · Done: `/flowflex:done analytics.data-views` · Spec: [[../../domains/analytics/data-views/_module|hub]] · Hard deps: analytics.dashboards, core.billing, core.rbac

- [ ] **Drill-Down** ([[../../domains/analytics/data-views/features/drill-down|spec]]) — hand-check: open rendered inside `DataViewsPage`; drill result opens in a slide-over or expanded panel.; click aggregate row → slide-over opens with `drillDown()` records (skeleton while loading); close → return to the view.
- [ ] **View Explorer** ([[../../domains/analytics/data-views/features/view-explorer|spec]]) — hand-check: open `DataViewsPage` at `/analytics/data-views` *(route assumed)* — gallery; selecting a view renders it in-page.; click a view card → resolve + render; change date range → recompute (skeleton while loading); click an aggregate row → drill-down 
- [ ] **View Export** ([[../../domains/analytics/data-views/features/view-export|spec]]) — hand-check: open action on `DataViewsPage`.; click export → (large set) queued job → toast "preparing…" → notification + download link when ready; small set → immediate downlo
- [ ] **View Registry** ([[../../domains/analytics/data-views/features/view-registry|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### KPI Tracking — `analytics.kpi-tracking`

Build: `/flowflex:start analytics.kpi-tracking` · Done: `/flowflex:done analytics.kpi-tracking` · Spec: [[../../domains/analytics/kpi-tracking/_module|hub]] · Hard deps: analytics.dashboards, core.billing, core.rbac, core.notifications

- [ ] **KPI Definition** ([[../../domains/analytics/kpi-tracking/features/kpi-definition|spec]]) — hand-check: open `KpiResource` at `/analytics/kpis`.
- [ ] **KPI Visualisation** ([[../../domains/analytics/kpi-tracking/features/kpi-visualisation|spec]]) — hand-check: open `KpiDashboardPage` at `/analytics/kpis/dashboard` *(route assumed)*.; select category → filter cards; click a KPI → expand trend + history; hover gauge → exact value + delta vs target.
- [ ] **Snapshot Capture** ([[../../domains/analytics/kpi-tracking/features/snapshot-capture|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Threshold Alerts** ([[../../domains/analytics/kpi-tracking/features/threshold-alerts|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Scheduled Exports — `analytics.scheduled-exports`

Build: `/flowflex:start analytics.scheduled-exports` · Done: `/flowflex:done analytics.scheduled-exports` · Spec: [[../../domains/analytics/scheduled-exports/_module|hub]] · Hard deps: analytics.reports, core.billing, core.rbac, foundation.queues, foundation.email

- [ ] **Delivery Log** ([[../../domains/analytics/scheduled-exports/features/delivery-log|spec]]) — hand-check: open delivery-log relation on `ScheduledExportResource` (+ a "view log" row action).; open schedule → log tab; click a successful row → download the tenant-scoped file (signed link if large); read failure error.
- [ ] **Recurring Generation** ([[../../domains/analytics/scheduled-exports/features/recurring-generation|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Schedule Management** ([[../../domains/analytics/scheduled-exports/features/schedule-management|spec]]) — hand-check: open `ScheduledExportResource` at `/analytics/exports`.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## customer-success

### Success Analytics — `cs.analytics`

Build: `/flowflex:start cs.analytics` · Done: `/flowflex:done cs.analytics` · Spec: [[../../domains/customer-success/success-analytics/_module|hub]] · Hard deps: cs.health, core.billing, core.rbac

- [ ] **CS Dashboard** ([[../../domains/customer-success/success-analytics/features/cs-dashboard|spec]]) — hand-check: open "CS Dashboard" at `/crm/cs-dashboard` (Customer Success nav group).; change date range → all sections refresh; export report; drill from a widget into the owning module's resource.
- [ ] **Retention & NRR** ([[../../domains/customer-success/success-analytics/features/retention-nrr|spec]]) — hand-check: open fragments on `CsDashboardPage` (`/crm/cs-dashboard`).; respond to the dashboard's date-range filter; NRR widget hidden when invoicing inactive.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Churn Risk Alerts — `cs.churn`

Build: `/flowflex:start cs.churn` · Done: `/flowflex:done cs.churn` · Spec: [[../../domains/customer-success/churn-risk/_module|hub]] · Hard deps: cs.health, core.billing, core.rbac, core.notifications

- [ ] **At-Risk Queue** ([[../../domains/customer-success/churn-risk/features/at-risk-queue|spec]]) — hand-check: open "Churn Risk" at `/crm/churn-risk` (Customer Success nav group).; filter by level/CSM · open row → factor detail · Run recovery playbook (confirm → `PlaybookService::run`) · Resolve (note → `resol
- [ ] **Rule-Based Detection** ([[../../domains/customer-success/churn-risk/features/rule-based-detection|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### CS Playbooks — `cs.playbooks`

Build: `/flowflex:start cs.playbooks` · Done: `/flowflex:done cs.playbooks` · Spec: [[../../domains/customer-success/playbooks/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac, core.notifications

- [ ] **Auto Triggers** ([[../../domains/customer-success/playbooks/features/auto-triggers|spec]]) — hand-check: background — trigger it (Command`. Resulting runs appear in Playbook Runs.), then check the visible result named in the spec
- [ ] **Playbook Builder** ([[../../domains/customer-success/playbooks/features/playbook-builder|spec]]) — hand-check: open "Playbooks" at `/crm/playbooks` (Customer Success nav group).; create/edit playbook; add/reorder steps in the repeater; toggle active; trigger-specific config fields appear on trigger change.
- [ ] **Playbook Runs** ([[../../domains/customer-success/playbooks/features/playbook-runs|spec]]) — hand-check: open "Playbook Runs" at `/crm/playbook-runs` (Customer Success nav group).; launch run (from playbook or churn one-click) · check off / skip steps · cancel run · filter by status/account.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### QBR Management — `cs.qbr`

Build: `/flowflex:start cs.qbr` · Done: `/flowflex:done cs.qbr` · Spec: [[../../domains/customer-success/qbr/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac, core.notifications

- [ ] **Action Items** ([[../../domains/customer-success/qbr/features/action-items|spec]]) — hand-check: open within QBR detail at `/crm/qbrs/{qbr}` → "Action items" relation.; add item · mark done · reassign owner / change due date · filter open/overdue.
- [ ] **Deck Preparation** ([[../../domains/customer-success/qbr/features/deck-preparation|spec]]) — hand-check: open QBR deck view under `QbrResource` at `/crm/qbrs/{qbr}` → "Deck" tab.; Prepare deck (snapshot → `deck_data`) · re-prepare (refresh snapshot) · pre-QBR checklist toggles.
- [ ] **QBR Scheduling** ([[../../domains/customer-success/qbr/features/qbr-scheduling|spec]]) — hand-check: open "QBRs" at `/crm/qbrs` (Customer Success nav group).; schedule QBR · Prepare deck / Record outcomes actions · cancel · filter by account/status.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Customer Health Scores — `customer-success.health-scores`

Build: `/flowflex:start customer-success.health-scores` · Done: `/flowflex:done customer-success.health-scores` · Spec: [[../../domains/customer-success/health-scores/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac, core.notifications, foundation.queues

- [ ] **Composite Scoring** ([[../../domains/customer-success/health-scores/features/composite-scoring|spec]]) — hand-check: open `HealthDashboardPage` at `/crm/health` (Customer Success nav group). `HealthScoreResource` (read-only simple-r; filter/segment by tier; open an account to view its breakdown; save weights → `ConfigureHealthData` → `cs_health_config`. Scores t
- [ ] **Tier-Drop Alerts** ([[../../domains/customer-success/health-scores/features/tier-drop-alerts|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### NPS Surveys — `cs.nps`

Build: `/flowflex:start cs.nps` · Done: `/flowflex:done cs.nps` · Spec: [[../../domains/customer-success/nps/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac, core.notifications, foundation.email, foundation.queues

- [ ] **Public Collector** ([[../../domains/customer-success/nps/features/public-collector|spec]]) — hand-check: open `/nps/{token}` (public, no panel guard).; pick/confirm score → optional comment → submit (POST) → thank-you state. Score buttons in email deep-link with the value pre-selec
- [ ] **Sentiment Scoring** ([[../../domains/customer-success/nps/features/sentiment-scoring|spec]]) — hand-check: open "NPS" at `/crm/nps` (Customer Success nav group).; date/survey range filter; drill into a survey's responses (→ `NpsResponseResource`).
- [ ] **Survey Send** ([[../../domains/customer-success/nps/features/survey-send|spec]]) — hand-check: open "NPS Surveys" at `/crm/nps-surveys` (Customer Success nav group).; create draft · pick audience · Send row action (confirm → `NpsService::send`) · view per-survey stats.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## dms

### Wiki — `dms.wiki`

Build: `/flowflex:start dms.wiki` · Done: `/flowflex:done dms.wiki` · Spec: [[../../domains/dms/wiki/_module|hub]] · Hard deps: core.billing, dms.wiki, core.rbac

- [ ] **Page Access Control** ([[../../domains/dms/wiki/features/page-access-control|spec]]) — hand-check: open "Access" section within the Page Editor form (`/dms/wiki-pages/{record}/edit`).; switch to `restricted` → reveal + require the list; save → scope applied immediately across tree/search/viewer.
- [ ] **Page Editor** ([[../../domains/dms/wiki/features/page-editor|spec]]) — hand-check: open "Wiki" — `WikiPageResource` create/edit (`/dms/wiki-pages/create`, `/{record}/edit`).; type body → purify on submit; pick parent → cycle-checked; save → version snapshot + reindex → redirect to viewer.
- [ ] **Page History** ([[../../domains/dms/wiki/features/page-history|spec]]) — hand-check: open "Versions" relation tab within a wiki page (`/dms/wiki-pages/{record}/edit` → Versions).; click a version → preview its body; Restore row action → confirm → body reverted + new snapshot; no create/edit (append-only).
- [ ] **Page Tree** ([[../../domains/dms/wiki/features/page-tree|spec]]) — hand-check: open nested nav rail within "Wiki" (`/dms/wiki`).; click node → open that page in the viewer; expand/collapse branch; (editor reuses the same tree as a parent select).
- [ ] **Wiki Search** ([[../../domains/dms/wiki/features/wiki-search|spec]]) — hand-check: open within "Wiki" (`/dms/wiki?q=`).; type → debounced query → results; click result → open that page in the viewer; clear → back to the current page.
- [ ] **Wiki Viewer** ([[../../domains/dms/wiki/features/wiki-viewer|spec]]) — hand-check: open "Wiki" — `WikiViewerPage` (`/dms/wiki/{slug}`).; click TOC heading → smooth-scroll to section; click internal link → navigate to target page; click nav node → switch page; edit/fa
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## ecommerce

### Abandoned Cart — `ecommerce.abandoned-cart`

Build: `/flowflex:start ecommerce.abandoned-cart` · Done: `/flowflex:done ecommerce.abandoned-cart` · Spec: [[../../domains/ecommerce/abandoned-cart/_module|hub]] · Hard deps: none

- [ ] **Recover Cart** ([[../../domains/ecommerce/abandoned-cart/features/recover-cart|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Orders — `ecommerce.orders`

Build: `/flowflex:start ecommerce.orders` · Done: `/flowflex:done ecommerce.orders` · Spec: [[../../domains/ecommerce/orders/_module|hub]] · Hard deps: none

- [ ] **Fulfil Order** ([[../../domains/ecommerce/orders/features/fulfil-order|spec]]) — hand-check: open "Fulfilment" (`/ecommerce/orders/fulfilment`), nav group Orders — `OrderFulfilmentPage`.; select order → mark lines shipped + enter tracking → `fulfil` → card moves out of the queue (optimistic); partial ships leave the 
- [ ] **Place Order** ([[../../domains/ecommerce/orders/features/place-order|spec]]) — hand-check: open checkout at `/shop/{company-slug}/checkout` (Vue + Inertia, owned by storefront); resulting order viewed at `E; submit checkout → `place` → payment intent → on success `markPaid`; admin "Mark paid" action when payments inactive.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Payments — `ecommerce.payments`

Build: `/flowflex:start ecommerce.payments` · Done: `/flowflex:done ecommerce.payments` · Spec: [[../../domains/ecommerce/payments/_module|hub]] · Hard deps: none

- [ ] **Process Payment** ([[../../domains/ecommerce/payments/features/process-payment|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Refund** ([[../../domains/ecommerce/payments/features/refund|spec]]) — hand-check: open "Refund" action on `EcPaymentResource` / order view (`/ecommerce/payments`), nav group Orders.; click "Refund" → modal → confirm → Stripe refund → order refund flow → payment row updates cumulative refunded amount.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Product Catalogue — `ecommerce.products`

Build: `/flowflex:start ecommerce.products` · Done: `/flowflex:done ecommerce.products` · Spec: [[../../domains/ecommerce/products/_module|hub]] · Hard deps: none

- [ ] **Manage Catalogue** ([[../../domains/ecommerce/products/features/manage-catalogue|spec]]) — hand-check: open `EcProductResource` (`/ecommerce/products`) + `EcCategoryResource` (`/ecommerce/categories`), nav group Catalo; create/edit form; "Publish" row/header action (draft → active), gated `ecommerce.products.publish`; archive action; category tree 
- [ ] **Stock Linkage** ([[../../domains/ecommerce/products/features/stock-linkage|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Promotions & Coupons — `ecommerce.promotions`

Build: `/flowflex:start ecommerce.promotions` · Done: `/flowflex:done ecommerce.promotions` · Spec: [[../../domains/ecommerce/promotions/_module|hub]] · Hard deps: none

- [ ] **Apply Discount** ([[../../domains/ecommerce/promotions/features/apply-discount|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Manage Coupons** ([[../../domains/ecommerce/promotions/features/manage-coupons|spec]]) — hand-check: open `CouponResource` (`/ecommerce/coupons`) + `EcPromotionResource` (`/ecommerce/promotions`), nav group Marketing; create/edit coupon or promotion; toggle active; view redemptions; validity + limit validation inline.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Product Reviews — `ecommerce.reviews`

Build: `/flowflex:start ecommerce.reviews` · Done: `/flowflex:done ecommerce.reviews` · Spec: [[../../domains/ecommerce/reviews/_module|hub]] · Hard deps: none

- [ ] **Moderate Review** ([[../../domains/ecommerce/reviews/features/moderate-review|spec]]) — hand-check: open `ReviewResource` (`/ecommerce/reviews`), nav group Catalogue, with a "Pending" queue tab.; approve/reject row action (busts cache); reply opens a modal; bulk approve/reject.
- [ ] **Submit Review** ([[../../domains/ecommerce/reviews/features/submit-review|spec]]) — hand-check: open review form at `/shop/{company-slug}/review/{token}` and inline on the product page (Vue + Inertia, storefront; submit → server verifies token + purchase → "thanks, pending moderation"; helpful vote increments `helpful_count` (rate-limited).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Storefront — `ecommerce.storefront`

Build: `/flowflex:start ecommerce.storefront` · Done: `/flowflex:done ecommerce.storefront` · Spec: [[../../domains/ecommerce/storefront/_module|hub]] · Hard deps: none

- [ ] **Browse & Cart** ([[../../domains/ecommerce/storefront/features/browse-and-cart|spec]]) — hand-check: open `Shop/Index.vue` (`/shop/{slug}`), `Shop/Product.vue` (`/shop/{slug}/p/{product-slug}`), `Shop/Cart.vue` (`/sh; search/filter; select variant; add to cart (optimistic) → server re-validates; adjust qty; proceed to checkout.
- [ ] **Checkout** ([[../../domains/ecommerce/storefront/features/checkout|spec]]) — hand-check: open `Shop/Checkout.vue` (`/shop/{slug}/checkout`) + `Shop/Confirmation.vue` (`/shop/{slug}/confirmation/{order}`).; enter details → apply coupon (server-validated) → confirm payment → order placed → confirmation. Guest checkout honored per settin
- [ ] **Configure Storefront** ([[../../domains/ecommerce/storefront/features/configure-storefront|spec]]) — hand-check: open `StorefrontSettingsPage` (`/ecommerce/storefront/settings`) + `StorefrontPageResource` (`/ecommerce/storefront; edit + save each tab (validated); build the nav menu (drag/reorder *(assumed)*); publish/unpublish content pages.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Product Variants — `ecommerce.variants`

Build: `/flowflex:start ecommerce.variants` · Done: `/flowflex:done ecommerce.variants` · Spec: [[../../domains/ecommerce/variants/_module|hub]] · Hard deps: none

- [ ] **Generate Variants** ([[../../domains/ecommerce/variants/features/generate-variants|spec]]) — hand-check: open Variants tab of the product edit screen (`/ecommerce/products/{id}/edit`), nav group Catalogue.; edit options → "Generate variants" → table populates (existing rows preserved); inline-edit cells; bulk-select → set price/stock.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## events

### Venues — `events.venues`

Build: `/flowflex:start events.venues` · Done: `/flowflex:done events.venues` · Spec: [[../../domains/events/venues/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Rooms** ([[../../domains/events/venues/features/rooms|spec]]) — hand-check: open rooms relation manager on `VenueResource`.; add room → name + capacity; edit/delete.
- [ ] **Venue Directory** ([[../../domains/events/venues/features/venue-directory|spec]]) — hand-check: open `VenueResource` list + form at `/app/events/venues` (nav group "Settings").; create/edit venue; view usage; delete guarded.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Events — `events.events`

Build: `/flowflex:start events.events` · Done: `/flowflex:done events.events` · Spec: [[../../domains/events/events/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [ ] **Agenda & Sessions** ([[../../domains/events/events/features/agenda-sessions|spec]]) — hand-check: open "Agenda" (`/app/events/events/{event}/agenda`) — an agenda/timeline builder for the event.; drag session card to a new time/room → validate within event window → save; click card → edit slide-over; add session inline.
- [ ] **Event CRUD & Lifecycle** ([[../../domains/events/events/features/event-crud|spec]]) — hand-check: open `EventResource` list + form at `/app/events/events`.; `Publish` and `Cancel` header/row actions (gated + confirmed); status badge reflects state machine; capacity field toggles unlimit
- [ ] **Public Landing Page** ([[../../domains/events/events/features/public-landing|spec]]) — hand-check: open "Event Landing" (`/e/{company}/{slug}`) — Vue + Inertia, ui-strategy row #16.; select ticket → register (Inertia form POST); add-to-calendar `.ics`; venue directions link.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Event Analytics — `events.event-analytics`

Build: `/flowflex:start events.event-analytics` · Done: `/flowflex:done events.event-analytics` · Spec: [[../../domains/events/event-analytics/_module|hub]] · Hard deps: events.events, events.registrations, core.billing, core.rbac

- [ ] **Event Dashboard** ([[../../domains/events/event-analytics/features/event-dashboard|spec]]) — hand-check: open "Event Dashboard" (`/app/events/analytics`) — `EventAnalyticsDashboard`, ui-strategy row #6 + apex charts.; change event/range → widgets refresh (cached); toggle comparison; export report.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Speakers — `events.speakers`

Build: `/flowflex:start events.speakers` · Done: `/flowflex:done events.speakers` · Spec: [[../../domains/events/speakers/_module|hub]] · Hard deps: events.events, core.billing, core.rbac, core.files

- [ ] **Session Assignment** ([[../../domains/events/speakers/features/session-assignment|spec]]) — hand-check: open session-speakers relation manager on the `EventResource` sessions (assignment picker with confirmation badges); assign speaker → invited; confirm/decline toggle → badge updates.
- [ ] **Speaker Directory** ([[../../domains/events/speakers/features/speaker-directory|spec]]) — hand-check: open `SpeakerResource` list + form at `/app/events/speakers` (nav group "Speakers").; create/edit speaker; copy submit link; view assignments.
- [ ] **Speaker Self-Submit** ([[../../domains/events/speakers/features/speaker-submit|spec]]) — hand-check: open "Speaker Submit" (`/speakers/submit/{token}`) — Vue + Inertia, ui-strategy row #16.; upload photo (client preview) → save → success screen.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Sponsors — `events.sponsors`

Build: `/flowflex:start events.sponsors` · Done: `/flowflex:done events.sponsors` · Spec: [[../../domains/events/sponsors/_module|hub]] · Hard deps: events.events, core.billing, core.rbac, core.files

- [ ] **Deliverables Tracking** ([[../../domains/events/sponsors/features/deliverables|spec]]) — hand-check: open deliverables relation manager on `SponsorResource`.; add deliverable → set due date; toggle done; overdue badge.
- [ ] **Sponsor Management** ([[../../domains/events/sponsors/features/sponsor-management|spec]]) — hand-check: open `SponsorResource` list + form at `/app/events/sponsors` (nav group "Sponsors"), per-event filter.; create/edit sponsor; create-invoice action (soft); status toggle committed→paid.
- [ ] **Sponsor Revenue** ([[../../domains/events/sponsors/features/sponsor-revenue|spec]]) — hand-check: open revenue summary widget on the `SponsorResource` list / event dashboard.; event selector filters the widget; click a tier → filter the sponsor list.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Registrations — `events.registrations`

Build: `/flowflex:start events.registrations` · Done: `/flowflex:done events.registrations` · Spec: [[../../domains/events/registrations/_module|hub]] · Hard deps: events.events, core.billing, core.rbac, foundation.email, foundation.queues

- [ ] **Check-In** ([[../../domains/events/registrations/features/check-in|spec]]) — hand-check: open "Check-In" (`/app/events/registrations/check-in`) — `CheckInPage` (Livewire), ui-strategy row #7.; scan QR → instant validate → green "checked in" flash or red reject reason; manual search → confirm identity → check in. Sub-3-sec
- [ ] **Public Registration** ([[../../domains/events/registrations/features/public-registration|spec]]) — hand-check: open registration form embedded in the event landing (`/e/{company}/{slug}`) — Vue + Inertia, ui-strategy row #16.; submit → Inertia POST → optimistic pending → confirmation or waitlist notice; sold-out disables CTA.
- [ ] **Registration Admin** ([[../../domains/events/registrations/features/registration-admin|spec]]) — hand-check: open `RegistrationResource` list at `/app/events/registrations` + `RegistrationStatsWidget`.; filter by event → status filter → cancel (confirm, promotes waitlist) → export (throttled).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Tickets — `events.tickets`

Build: `/flowflex:start events.tickets` · Done: `/flowflex:done events.tickets` · Spec: [[../../domains/events/tickets/_module|hub]] · Hard deps: events.events, events.registrations, core.billing, core.rbac, foundation.queues

- [ ] **Discount Codes** ([[../../domains/events/tickets/features/discount-codes|spec]]) — hand-check: open discount-codes relation/resource under the event's ticket settings.; create code → set type + value + max_uses; used_count shown read-only.
- [ ] **Refunds** ([[../../domains/events/tickets/features/refunds|spec]]) — hand-check: open "Refund" row action on the read-only Purchases list.; refund → confirm modal (reason) → Stripe refund → status flips to `refunded`, registration cancelled.
- [ ] **Ticket Purchase** ([[../../domains/events/tickets/features/ticket-purchase|spec]]) — hand-check: open purchase panel embedded in the event landing (`/e/{company}/{slug}`) — Vue + Inertia + Stripe Elements, ui-str; apply discount (live recalculated total, brick/money) → pay → PaymentIntent → confirmation on webhook; sold-out disables the CTA.
- [ ] **Ticket Types** ([[../../domains/events/tickets/features/ticket-types|spec]]) — hand-check: open Ticket types relation manager on `EventResource` edit.; add type → set price + quantity + window; sold-out badge auto-computed.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## it

### IT Reporting — `it.reporting`

Build: `/flowflex:start it.reporting` · Done: `/flowflex:done it.reporting` · Spec: [[../../domains/it/it-reporting/_module|hub]] · Hard deps: it.assets, core.billing, core.rbac

- [ ] **Asset Valuation Widget** ([[../../domains/it/it-reporting/features/asset-valuation-widget|spec]]) — hand-check: open hosted on the "IT Reporting" dashboard (`/it/reporting`) — ships apex-chart widgets, not a page of its own.; change the header period to re-scope; hover a series for the point tooltip.
- [ ] **Compliance Widget** ([[../../domains/it/it-reporting/features/compliance-widget|spec]]) — hand-check: open hosted on the "IT Reporting" dashboard (`/it/reporting`) — apex-chart widgets, not a page of its own.; change the header period to re-scope; hover for the exact percentage tooltip.
- [ ] **Helpdesk Metrics Widget** ([[../../domains/it/it-reporting/features/helpdesk-metrics-widget|spec]]) — hand-check: open hosted on the "IT Reporting" dashboard (`/it/reporting`) — apex-chart widgets, not a page of its own.; change the header period to re-scope; hover a series for tooltip.
- [ ] **IT Dashboard** ([[../../domains/it/it-reporting/features/it-dashboard|spec]]) — hand-check: open `ItDashboardPage` at `/it/reporting` (custom Filament page + apex-chart widgets).; change the header period → all widgets re-scope; hover a series for tooltip; export the current report from a header action (named
- [ ] **Licence Spend Widget** ([[../../domains/it/it-reporting/features/licence-spend-widget|spec]]) — hand-check: open hosted on the "IT Reporting" dashboard (`/it/reporting`) — apex-chart widgets, not a page of its own.; change the header period to re-scope; hover a series for tooltip.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Access Provisioning — `it.access-provisioning`

Build: `/flowflex:start it.access-provisioning` · Done: `/flowflex:done it.access-provisioning` · Spec: [[../../domains/it/access-provisioning/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Access Grants** ([[../../domains/it/access-provisioning/features/access-grants|spec]]) — hand-check: open `AccessGrantResource` at `/it/access-grants`.; Grant → `AccessService::grant` (stamps `granted_at`/`granted_by`); Revoke → `AccessService::revoke` (stamps `revoked_at`/`revoked_
- [ ] **Access Review Matrix** ([[../../domains/it/access-provisioning/features/access-review-matrix|spec]]) — hand-check: open `AccessReviewPage` at `/it/access-review`.; scan the matrix for over/under-provisioning; Export → throttled snapshot download (`RateLimiter` keyed on `company_id:user_id`).
- [ ] **Access Templates** ([[../../domains/it/access-provisioning/features/access-templates|spec]]) — hand-check: open `AccessTemplateResource` at `/it/access-templates`.; create / edit / delete a template; each `system_id` must be an existing `it_systems` id in the company.
- [ ] **De-provisioning on Offboard** ([[../../domains/it/access-provisioning/features/deprovisioning-on-offboard|spec]]) — hand-check: background — trigger it (: `EmployeeOffboarded` → `DeprovisionOnOffboardListener` → all active grants set to `revok), then check the visible result named in the spec
- [ ] **Provisioning on Hire** ([[../../domains/it/access-provisioning/features/provisioning-on-hire|spec]]) — hand-check: background — trigger it (: `EmployeeHired` → `ProvisionOnHireListener` → pending grants from matching template + IT), then check the visible result named in the spec
- [ ] **System Catalogue** ([[../../domains/it/access-provisioning/features/system-catalogue|spec]]) — hand-check: open `SystemResource` at `/it/systems`.; create / edit / delete a system; deleting a system in use prompts to reassign or block *(assumed)*.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Asset Inventory — `it.asset-inventory`

Build: `/flowflex:start it.asset-inventory` · Done: `/flowflex:done it.asset-inventory` · Spec: [[../../domains/it/asset-inventory/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Asset Record** ([[../../domains/it/asset-inventory/features/asset-record|spec]]) — hand-check: open `AssetResource` at `/it/assets` (list + create/edit).; filters by type / status / assignee; create + edit form; assignment-history relation manager on the record; assign/return/retire r
- [ ] **Assignment & Return** ([[../../domains/it/asset-inventory/features/assignment-return|spec]]) — hand-check: open `AssetResource` at `/it/assets` — Assign / Return / Retire actions per row + "Assignment history" relation man; Assign disabled unless `in_stock`; Return disabled unless `assigned`; Retire disabled while `assigned`.
- [ ] **Offboarding Return Flags** ([[../../domains/it/asset-inventory/features/offboarding-return-flags|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Warranty Alerts** ([[../../domains/it/asset-inventory/features/warranty-alerts|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### IT Helpdesk — `it.helpdesk`

Build: `/flowflex:start it.helpdesk` · Done: `/flowflex:done it.helpdesk` · Spec: [[../../domains/it/helpdesk/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Replies Thread** ([[../../domains/it/helpdesk/features/replies-thread|spec]]) — hand-check: open rendered inside the ticket detail (`/it/helpdesk/tickets/{ticket}` and the queue slide-over).; post public reply (notifies requester) · post internal note (staff only, silent) · requester public reply (may reopen).
- [ ] **Self-Service Requests** ([[../../domains/it/helpdesk/features/self-service-requests|spec]]) — hand-check: open "Report an IT issue" / "My tickets" (`/it/helpdesk/tickets` → My tickets tab), nav group Helpdesk.; submit ticket · view own ticket + public replies · reply to own ticket. No assign / no internal-note controls.
- [ ] **Staff Queue** ([[../../domains/it/helpdesk/features/staff-queue|spec]]) — hand-check: open "Helpdesk queue" (`/it/helpdesk/queue`), nav group Helpdesk.; assign to me / to a teammate · quick reply · resolve · filter by category/assignee. Polling refresh every 30s.
- [ ] **Ticket Management** ([[../../domains/it/helpdesk/features/ticket-management|spec]]) — hand-check: open "IT tickets" (`/it/helpdesk/tickets`), nav group Helpdesk.; create ticket · edit/assign (staff) · open infolist with reply thread (replies-thread) · filter by category/priority/status/assign
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### MDM Integration — `it.mdm-integration`

Build: `/flowflex:start it.mdm-integration` · Done: `/flowflex:done it.mdm-integration` · Spec: [[../../domains/it/mdm-integration/_module|hub]] · Hard deps: it.assets, core.billing, core.rbac, foundation.queues

- [ ] **Compliance Alerts** ([[../../domains/it/mdm-integration/features/compliance-alerts|spec]]) — hand-check: background — trigger it (`compliance_status` transition during `SyncMdmDevicesJob`.), then check the visible result named in the spec
- [ ] **Device Actions (Lock / Wipe)** ([[../../domains/it/mdm-integration/features/device-actions|spec]]) — hand-check: open `MdmDeviceResource` list at `/app/it/mdm/devices` — table with compliance filter; Lock and Wipe row actions.; click Lock → confirm → proxy + audit; click Wipe → permission check + confirm modal → proxy + audit; toast on dispatch.
- [ ] **Device Sync** ([[../../domains/it/mdm-integration/features/device-sync|spec]]) — hand-check: background — trigger it (hourly scheduler → `SyncMdmDevicesJob`.), then check the visible result named in the spec
- [ ] **Provider Connection** ([[../../domains/it/mdm-integration/features/provider-connection|spec]]) — hand-check: open `MdmConfigPage` at `/app/it/mdm/config` (custom Filament page, form schema).; submit → verify against provider → on success store encrypted + set `last_synced_at` baseline; on failure show validation error, s
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Software Licences — `it.software-licences`

Build: `/flowflex:start it.software-licences` · Done: `/flowflex:done it.software-licences` · Spec: [[../../domains/it/software-licences/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Licence Record** ([[../../domains/it/software-licences/features/licence-record|spec]]) — hand-check: open `LicenceResource` at `/it/licences` (nav group Licences).; create/edit/delete licence; filter by vendor / billing cycle; row action to open seat assignments.
- [ ] **Offboarding Seat Reclaim** ([[../../domains/it/software-licences/features/offboarding-seat-reclaim|spec]]) — hand-check: background — trigger it (: `EmployeeOffboarded` (hr.employee-profiles) → `FlagSeatsForReclaimListener` flags that e), then check the visible result named in the spec
- [ ] **Renewal Alerts** ([[../../domains/it/software-licences/features/renewal-alerts|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Seat Assignment** ([[../../domains/it/software-licences/features/seat-assignment|spec]]) — hand-check: open seat-assignment relation on `LicenceResource` at `/it/licences/{licence}` (nav group Licences).; assign employee → capacity + duplicate checks → row added; revoke row → `revoked_at` set; over-capacity/duplicate → inline validat
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## legal

### DSAR Processing (Legal layer) — `legal.dsar-processing`

Build: `/flowflex:start legal.dsar-processing` · Done: `/flowflex:done legal.dsar-processing` · Spec: [[../../domains/legal/dsar-processing/_module|hub]] · Hard deps: core.privacy, core.billing, core.rbac

- [ ] **Action Log & Rejection** ([[../../domains/legal/dsar-processing/features/action-log-rejection|spec]]) — hand-check: open action trail + reject action on `DsarRequestResource` (extended) (`/legal/dsar`).; view trail; reject → required notes → `rejected` action logged; record `rectified` with notes.
- [ ] **Data Discovery** ([[../../domains/legal/dsar-processing/features/data-discovery|spec]]) — hand-check: open discovery section on `DsarFulfilmentPage` (`/legal/dsar/{id}`).; run discovery → registry query → populate table → log `discovery-run`.
- [ ] **Fulfilment Delegation** ([[../../domains/legal/dsar-processing/features/fulfilment-delegation|spec]]) — hand-check: open fulfilment section on `DsarFulfilmentPage` (`/legal/dsar/{id}`).; trigger export/erasure → dispatch core.privacy job → poll status → log action on completion.
- [ ] **Identity Verification** ([[../../domains/legal/dsar-processing/features/identity-verification|spec]]) — hand-check: open verification step on `DsarFulfilmentPage` (`/legal/dsar/{id}`).; pick method → complete checklist → confirm → gate lifts + `verified` action logged.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Legal Spend — `legal.legal-spend`

Build: `/flowflex:start legal.legal-spend` · Done: `/flowflex:done legal.legal-spend` · Spec: [[../../domains/legal/legal-spend/_module|hub]] · Hard deps: legal.matters, core.billing, core.rbac

- [ ] **Budget vs Actual** ([[../../domains/legal/legal-spend/features/budget-vs-actual|spec]]) — hand-check: open `LegalSpendDashboardPage` (`/legal/spend/dashboard`).; switch period; drill matter → matter spend; set/edit budget; export report.
- [ ] **Expense Records** ([[../../domains/legal/legal-spend/features/expense-records|spec]]) — hand-check: open `LegalExpenseResource` — list + create/edit at `/legal/spend/expenses`.; create expense (matter picker limited to accessible matters); duplicate-invoice inline error; approve action (see approval feature
- [ ] **Invoice Approval** ([[../../domains/legal/legal-spend/features/invoice-approval|spec]]) — hand-check: open "Approval queue" (`/legal/spend/approvals`).; approve (blocked if you are the submitter → inline message); reject with reason; bulk approve selected; optional "create AP bill" 
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Matter Management — `legal.matter-management`

Build: `/flowflex:start legal.matter-management` · Done: `/flowflex:done legal.matter-management` · Spec: [[../../domains/legal/matter-management/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [ ] **Confidential Access** ([[../../domains/legal/matter-management/features/confidential-access|spec]]) — hand-check: open confidentiality panel on the matter form (`/legal/matters/{id}/edit`).; toggle confidential; add/remove users from access list; non-listed users never see the row.
- [ ] **Matter Records** ([[../../domains/legal/matter-management/features/matter-records|spec]]) — hand-check: open `MatterResource` — list + create/edit at `/legal/matters`.; filter type/status/priority; status transition actions (respect machine); close action; toggle confidential + edit access list.
- [ ] **Matter Timeline** ([[../../domains/legal/matter-management/features/matter-timeline|spec]]) — hand-check: open "Timeline" tab on the matter view (`/legal/matters/{id}`).; add event (mark as deadline); edit/delete; deadlines highlighted as they approach.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Compliance Registers — `legal.compliance-registers`

Build: `/flowflex:start legal.compliance-registers` · Done: `/flowflex:done legal.compliance-registers` · Spec: [[../../domains/legal/compliance-registers/_module|hub]] · Hard deps: core.billing, core.rbac, core.files, core.notifications

- [ ] **Audit Readiness Dashboard** ([[../../domains/legal/compliance-registers/features/audit-readiness-dashboard|spec]]) — hand-check: open `ComplianceDashboardPage` (`/legal/compliance/dashboard`).; switch framework; click gap → control; assign owner; drill to control.
- [ ] **Compliance Tasks** ([[../../domains/legal/compliance-registers/features/compliance-tasks|spec]]) — hand-check: open `ControlResource` tasks tab + a "My compliance tasks" filtered view (`/legal/compliance/controls/{id}` → Tasks; add task with frequency; assign; complete (auto-spawns next if recurring); filter overdue / mine.
- [ ] **Control Management** ([[../../domains/legal/compliance-registers/features/control-management|spec]]) — hand-check: open `ControlResource` — list + create/edit at `/legal/compliance/controls`.; set status (evidence required for green); upload evidence; link policy; filter to gaps.
- [ ] **Framework Registers** ([[../../domains/legal/compliance-registers/features/framework-registers|spec]]) — hand-check: open `FrameworkResource` — list + create/edit at `/legal/compliance/frameworks`.; create framework; open controls tab; readiness badge per row.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Legal Contracts — `legal.legal-contracts`

Build: `/flowflex:start legal.legal-contracts` · Done: `/flowflex:done legal.legal-contracts` · Spec: [[../../domains/legal/legal-contracts/_module|hub]] · Hard deps: core.billing, core.rbac, core.files, core.notifications

- [ ] **Contract Lifecycle** ([[../../domains/legal/legal-contracts/features/contract-lifecycle|spec]]) — hand-check: open "Renewals & Lifecycle" (`/legal/contracts/lifecycle`) — plus a `ContractRenewalWidget` on the panel dashboard.; click card → slide-over with sign / renew / terminate; renew opens a date form; bulk "acknowledge" to snooze noise.
- [ ] **Contract Repository** ([[../../domains/legal/legal-contracts/features/contract-repository|spec]]) — hand-check: open `LegalContractResource` — list + create/edit at `/legal/contracts`.; filter by type / status / renewal window; row actions sign / renew / terminate (delegate to lifecycle); upload signed PDF; open ob
- [ ] **E-signature** ([[../../domains/legal/legal-contracts/features/e-signature|spec]]) — hand-check: open internal upload step on `LegalContractResource` (custom action modal); roadmap external signer surface = Vue/I; internal — drop PDF → validate PDF-only → confirm → transition to `signed`. Public — review → sign → POST returns signed status.
- [ ] **Obligation Tracking** ([[../../domains/legal/legal-contracts/features/obligation-tracking|spec]]) — hand-check: open obligations tab on the contract view (`/legal/contracts/{id}`).; add obligation; mark done; filter overdue; assign responsible user.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Policy Library — `legal.policy-library`

Build: `/flowflex:start legal.policy-library` · Done: `/flowflex:done legal.policy-library` · Spec: [[../../domains/legal/policy-library/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Acknowledgement Tracking** ([[../../domains/legal/policy-library/features/acknowledgement-tracking|spec]]) — hand-check: open `PolicyAcknowledgementPage` — matrix (`/legal/policies/acknowledgements`); `MyPoliciesPage` — self-service (`/; matrix — filter by policy/department, export CSV; self-service — open policy body, click acknowledge → cell flips.
- [ ] **Policy Authoring** ([[../../domains/legal/policy-library/features/policy-authoring|spec]]) — hand-check: open `PolicyResource` — list + create/edit at `/legal/policies`.; edit body (Tiptap); set audience; save draft; trigger publish (delegates to publication feature); review-due badge.
- [ ] **Publication & Versioning** ([[../../domains/legal/policy-library/features/publication-versioning|spec]]) — hand-check: open publish action/modal launched from `PolicyResource` (`/legal/policies/{id}` → Publish).; pick audience (all / departments) → preview recipient count → confirm publish → version bump + notify.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## lms

### Certifications — `lms.certifications`

Build: `/flowflex:start lms.certifications` · Done: `/flowflex:done lms.certifications` · Spec: [[../../domains/lms/certifications/_module|hub]] · Hard deps: none

- [ ] **Certificate Issuance** ([[../../domains/lms/certifications/features/certificate-issuance|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Expiry & Renewal** ([[../../domains/lms/certifications/features/expiry-renewal|spec]]) — hand-check: background — trigger it (: `CertificateExpiryCommand` (daily, notifications queue). Admin-facing view is the `Certi), then check the visible result named in the spec
- [ ] **Public Verification** ([[../../domains/lms/certifications/features/public-verification|spec]]) — hand-check: open "Verify Certificate" (`/verify/{number}`, `Verify.vue`).; land on `/verify/{number}` → status resolves; or type a number → submit (throttled).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Course Builder — `lms.courses`

Build: `/flowflex:start lms.courses` · Done: `/flowflex:done lms.courses` · Spec: [[../../domains/lms/courses/_module|hub]] · Hard deps: none

- [ ] **Course Builder** ([[../../domains/lms/courses/features/course-builder|spec]]) — hand-check: open "Course Builder" (`CourseBuilderPage`, `/lms/courses/{course}/build`); drag module → reorder → optimistic move + persist `order`; drag lesson between modules → reassign `module_id` + `order`; inline ad
- [ ] **Course Management** ([[../../domains/lms/courses/features/course-management|spec]]) — hand-check: open "Courses" (`CourseResource`, `/lms/courses`); create/edit form; "Publish" row action (guarded, disabled if no lessons); status + category table filters; archive action.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Enrolments — `lms.enrolments`

Build: `/flowflex:start lms.enrolments` · Done: `/flowflex:done lms.enrolments` · Spec: [[../../domains/lms/enrolments/_module|hub]] · Hard deps: none

- [ ] **Auto-Enrol on Hire** ([[../../domains/lms/enrolments/features/auto-enrol-on-hire|spec]]) — hand-check: background — trigger it (: `EmployeeHired` event → queued `AutoEnrolOnHireListener`. Its results surface in the Enr), then check the visible result named in the spec
- [ ] **Enrolment Management** ([[../../domains/lms/enrolments/features/enrolment-management|spec]]) — hand-check: open "Enrolments" (`EnrolmentResource`, `/lms/enrolments`); enrol form (prerequisite check inline); bulk-enrol modal; drop action; filter to overdue; deep-link learner.
- [ ] **Learner Portal** ([[../../domains/lms/enrolments/features/learner-portal|spec]]) — hand-check: open "My Learning" (`/learn`, `/learn/courses/{course}`, `/learn/lessons/{lesson}`) — ui-strategy row #15.; open lesson → auto/explicit mark complete → progress ring updates; quiz submit → server grade → pass/fail feedback (no answer keys
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Learning Paths — `lms.learning-paths`

Build: `/flowflex:start lms.learning-paths` · Done: `/flowflex:done lms.learning-paths` · Spec: [[../../domains/lms/learning-paths/_module|hub]] · Hard deps: none

- [ ] **Path Builder** ([[../../domains/lms/learning-paths/features/path-builder|spec]]) — hand-check: open "Learning Paths" (`LearningPathResource`, `/lms/paths`); add/reorder courses in the repeater; toggle sequential; bulk-assign learners; view per-path progress.
- [ ] **Path Progression** ([[../../domains/lms/learning-paths/features/path-progression|spec]]) — hand-check: background — trigger it (: `PathService::onCourseCompleted` (called by enrolments on course completion). Learner-fa), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Lessons & Content — `lms.lessons`

Build: `/flowflex:start lms.lessons` · Done: `/flowflex:done lms.lessons` · Spec: [[../../domains/lms/lessons/_module|hub]] · Hard deps: none

- [ ] **Lesson Content** ([[../../domains/lms/lessons/features/lesson-content|spec]]) — hand-check: open Lesson relation manager on `CourseResource` / modules (`/lms/courses/{course}` → module → lessons).; add lesson → pick type → type-specific form; reorder within module; set completion criterion; upload validated client + server.
- [ ] **Quizzes** ([[../../domains/lms/lessons/features/quizzes|spec]]) — hand-check: open "Quiz Builder" (`QuizBuilderPage` / repeater within the lesson form, `/lms/courses/{course}/quiz/{lesson}`).; add/reorder questions; mark correct option(s); set passing score; preview (admin-only, shows keys). Learner submission happens on 
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### LMS Analytics — `lms.lms-analytics`

Build: `/flowflex:start lms.lms-analytics` · Done: `/flowflex:done lms.lms-analytics` · Spec: [[../../domains/lms/lms-analytics/_module|hub]] · Hard deps: none

- [ ] **Compliance Report** ([[../../domains/lms/lms-analytics/features/compliance-report|spec]]) — hand-check: open Compliance tab of `LmsDashboardPage` (`ComplianceWidget`, `/lms/analytics` → Compliance).; filter to overdue; export report (rate-limited); drill into a course's overdue list.
- [ ] **LMS Dashboard** ([[../../domains/lms/lms-analytics/features/lms-dashboard|spec]]) — hand-check: open "LMS Dashboard" (`LmsDashboardPage` + `CompletionRateWidget` / `EngagementWidget`, `/lms/analytics`); change date range → recompute (cached); drill into drop-off lesson; toggle course/path scope.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Mentoring — `lms.mentoring`

Build: `/flowflex:start lms.mentoring` · Done: `/flowflex:done lms.mentoring` · Spec: [[../../domains/lms/mentoring/_module|hub]] · Hard deps: none

- [ ] **Mentor Directory** ([[../../domains/lms/mentoring/features/mentor-directory|spec]]) — hand-check: open "Mentor Directory" (`MentorDirectoryPage`, `/lms/mentoring/directory`); filter by expertise; open a mentor card; "Request mentorship" → focus-area modal → creates a pending mentorship + notifies the men
- [ ] **Mentorship Management** ([[../../domains/lms/mentoring/features/mentorship-management|spec]]) — hand-check: open "Mentorships" (`MentorshipResource`, `/lms/mentoring`); accept/pause/complete actions; add/toggle goals; open sessions relation.
- [ ] **Session Logging** ([[../../domains/lms/mentoring/features/session-logging|spec]]) — hand-check: open Sessions relation on `MentorshipResource` (`/lms/mentoring/{mentorship}` → sessions).; add session; check off action items; edit own logs. Non-participants get no rows (query-scoped).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Skills Matrix — `lms.skills-matrix`

Build: `/flowflex:start lms.skills-matrix` · Done: `/flowflex:done lms.skills-matrix` · Spec: [[../../domains/lms/skills-matrix/_module|hub]] · Hard deps: none

- [ ] **Gap Analysis & Recommendations** ([[../../domains/lms/skills-matrix/features/gap-analysis|spec]]) — hand-check: open "Gap Analysis" (`SkillsMatrixPage` tab / `GapAnalysisPage`, `/lms/skills/gaps`); optionally a widget.; pick employee (own/report scope); view gaps; enrol from a recommendation.
- [ ] **Skill Catalogue** ([[../../domains/lms/skills-matrix/features/skill-catalogue|spec]]) — hand-check: open "Skills" (`SkillResource`, `/lms/skills`); create skill; set role requirement; link course + taught level; assess an employee (own or report).
- [ ] **Skills Heat-map** ([[../../domains/lms/skills-matrix/features/skills-heatmap|spec]]) — hand-check: open "Skills Matrix" (`SkillsMatrixPage`, `/lms/skills/matrix`); filter by category/department; click cell → detail; toggle "gaps only".
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## marketing

### Content CMS — `marketing.content-cms`

Build: `/flowflex:start marketing.content-cms` · Done: `/flowflex:done marketing.content-cms` · Spec: [[../../domains/marketing/content-cms/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [ ] **Authoring** ([[../../domains/marketing/content-cms/features/authoring|spec]]) — hand-check: open `PostResource` (`/marketing/content`) — Content nav group; `PostCategoryResource` alongside.; write body; pick category/tags; set featured + OG image; save draft.
- [ ] **Public Blog** ([[../../domains/marketing/content-cms/features/public-blog|spec]]) — hand-check: open `/blog/{company-slug}` (Index) + `/blog/{company-slug}/{slug}` (Show) — Vue + Inertia (ui-strategy rows #12/#1; search; filter by category; open a post; navigate related.
- [ ] **Scheduling & Publish** ([[../../domains/marketing/content-cms/features/scheduling-publish|spec]]) — hand-check: open publish/schedule actions on `PostResource` rows + edit form; status badge tracks state.; click Publish (immediate) or set date → Schedule; unpublish returns to draft.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Forms — `marketing.forms`

Build: `/flowflex:start marketing.forms` · Done: `/flowflex:done marketing.forms` · Spec: [[../../domains/marketing/forms/_module|hub]] · Hard deps: core.billing, core.rbac, foundation.queues

- [ ] **Embed & Hosted Page** ([[../../domains/marketing/forms/features/embed-hosted|spec]]) — hand-check: open hosted form `/f/{slug}` (Vue + Inertia, ui-strategy row #16); embed = JS renderer injected into the customer's; fill → submit → POST `/f/{slug}` → thank-you or redirect; client-side validation mirrors server rules.
- [ ] **Form Builder** ([[../../domains/marketing/forms/features/form-builder|spec]]) — hand-check: open `FormResource` (`/marketing/forms`) — Forms nav group.; add/reorder fields in the repeater; set submit action; copy embed snippet; submissions relation tab.
- [ ] **Public Submit** ([[../../domains/marketing/forms/features/public-submit|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Landing Pages — `marketing.landing-pages`

Build: `/flowflex:start marketing.landing-pages` · Done: `/flowflex:done marketing.landing-pages` · Spec: [[../../domains/marketing/landing-pages/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [ ] **Page Analytics** ([[../../domains/marketing/landing-pages/features/page-analytics|spec]]) — hand-check: open visit/conversion columns on the `LandingPageResource` table + a small funnel on the page view; the cross-page ; sort/filter by conversion rate; click through to the page.
- [ ] **Page Builder** ([[../../domains/marketing/landing-pages/features/page-builder|spec]]) — hand-check: open block-builder inside `LandingPageResource` (`/marketing/landing-pages/{id}/edit`) — Landing Pages nav group. A; drag to reorder blocks; edit block config in a panel; toggle preview device; publish/unpublish.
- [ ] **Publish & Public Render** ([[../../domains/marketing/landing-pages/features/publish-render|spec]]) — hand-check: open `/p/{company-slug}/{page-slug}` (Vue + Inertia block renderer, ui-strategy row #16).; scroll / CTA clicks / form submit; visit recorded via `RecordVisitAction`.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Marketing Analytics — `marketing.analytics`

Build: `/flowflex:start marketing.analytics` · Done: `/flowflex:done marketing.analytics` · Spec: [[../../domains/marketing/marketing-analytics/_module|hub]] · Hard deps: marketing.campaigns, core.billing, core.rbac

- [ ] **Marketing Dashboard** ([[../../domains/marketing/marketing-analytics/features/marketing-dashboard|spec]]) — hand-check: open `MarketingDashboardPage` (`/marketing/analytics`) — Analytics nav group; apex-chart widgets (ui-strategy row #; change date range → all widgets refresh; toggle first/last attribution; export CSV.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### UTM Tracking — `marketing.utm`

Build: `/flowflex:start marketing.utm` · Done: `/flowflex:done marketing.utm` · Spec: [[../../domains/marketing/utm-tracking/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac

- [ ] **Attribution** ([[../../domains/marketing/utm-tracking/features/attribution|spec]]) — hand-check: open attribution tables rendered inside the Marketing Analytics dashboard (not a standalone page) — first/last togg; switch model; change date range (inherited from dashboard); drill by dimension.
- [ ] **Touch Capture** ([[../../domains/marketing/utm-tracking/features/touch-capture|spec]]) — hand-check: background — trigger it (: `RecordUtmFromFormListener` on `FormSubmissionReceived` (+ soft `RecordVisitAction`). No), then check the visible result named in the spec
- [ ] **UTM Builder** ([[../../domains/marketing/utm-tracking/features/utm-builder|spec]]) — hand-check: open `UtmBuilderPage` (`/marketing/utm/builder`) — Analytics nav group (ui-strategy row #7, form-style custom page); fill fields → live-generated URL → copy; clear/reset.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Email Sequences — `marketing.email-sequences`

Build: `/flowflex:start marketing.email-sequences` · Done: `/flowflex:done marketing.email-sequences` · Spec: [[../../domains/marketing/email-sequences/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac, foundation.queues, foundation.email

- [ ] **Advancement Engine** ([[../../domains/marketing/email-sequences/features/advancement-engine|spec]]) — hand-check: background — trigger it (: scheduled command on the `notifications` queue. No page; progress visible per-enrolment ), then check the visible result named in the spec
- [ ] **Build Sequence** ([[../../domains/marketing/email-sequences/features/build-sequence|spec]]) — hand-check: open `SequenceResource` (`/marketing/sequences`) — Sequences nav group.; add/reorder steps in the repeater; set trigger config; toggle active; view page shows per-step open/click.
- [ ] **Enrolment Triggers** ([[../../domains/marketing/email-sequences/features/enrolment-triggers|spec]]) — hand-check: background — trigger it (: event listener + scheduled diff — no dedicated page. Configured in the build-sequence fo), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Campaigns — `marketing.campaigns`

Build: `/flowflex:start marketing.campaigns` · Done: `/flowflex:done marketing.campaigns` · Spec: [[../../domains/marketing/campaigns/_module|hub]] · Hard deps: crm.contacts, crm.segments, core.billing, core.rbac, foundation.queues, foundation.email

- [ ] **A/B Subject Testing** ([[../../domains/marketing/campaigns/features/ab-testing|spec]]) — hand-check: open within `CampaignResource` form (A/B toggle reveals `subject_b` + split slider) + `CampaignStatsWidget` per-var; enable A/B → enter subject_b + split → schedule; view page compares variant open/click rates.
- [ ] **Audience Materialisation** ([[../../domains/marketing/campaigns/features/audience-materialisation|spec]]) — hand-check: background — trigger it (: `CampaignService::schedule` (invoked from compose-schedule's "Send now" / "Schedule"). N), then check the visible result named in the spec
- [ ] **Compose & Schedule** ([[../../domains/marketing/campaigns/features/compose-schedule|spec]]) — hand-check: open `CampaignResource` (`/marketing/campaigns`) — Campaigns nav group.; pick audience → compose → test-send → "Send now" / "Schedule"; status badge tracks lifecycle.
- [ ] **Tracking & Suppression** ([[../../domains/marketing/campaigns/features/tracking-suppression|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## operations

### Warehouses — `operations.warehouses`

Build: `/flowflex:start operations.warehouses` · Done: `/flowflex:done operations.warehouses` · Spec: [[../../domains/operations/warehouses/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Stock Transfer** ([[../../domains/operations/warehouses/features/stock-transfer|spec]]) — hand-check: open `WarehouseTransferResource` at `/operations/warehouse-transfers`.; select item → panel shows available at chosen source; submit → atomic transfer → row appears in history.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Inventory — `operations.inventory`

Build: `/flowflex:start operations.inventory` · Done: `/flowflex:done operations.inventory` · Spec: [[../../domains/operations/inventory/_module|hub]] · Hard deps: operations.warehouses, core.billing, core.rbac

- [ ] **Item Catalogue** ([[../../domains/operations/inventory/features/item-catalogue|spec]]) — hand-check: open `ItemResource` at `/operations/items`.; create/edit item; SKU search + category filter; low-stock filter toggle; row link to movement history.
- [ ] **Low-Stock Alerts** ([[../../domains/operations/inventory/features/low-stock-alerts|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Reserve & Release** ([[../../domains/operations/inventory/features/reserve-release|spec]]) — hand-check: background — trigger it (: `StockService::reserve` / `release` calls from sales-order / cart flows (soft dependents), then check the visible result named in the spec
- [ ] **Stock Movements Ledger & Stock Board** ([[../../domains/operations/inventory/features/stock-movements|spec]]) — hand-check: open `StockBoardPage` at `/operations/stock-board`; `StockMovementResource` at `/operations/stock-movements`.; filter/search the ledger; on the board, click a cell → `move` modal (type, qty, cost, reason) → optimistic level update; over-avai
- [ ] **Valuation** ([[../../domains/operations/inventory/features/valuation|spec]]) — hand-check: open `ValuationWidget` (Filament widget) — total value + by-warehouse/category breakdown.; warehouse/category filter (mirrors reporting dashboard); no writes.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Operations Reporting — `operations.operations-reporting`

Build: `/flowflex:start operations.operations-reporting` · Done: `/flowflex:done operations.operations-reporting` · Spec: [[../../domains/operations/operations-reporting/_module|hub]] · Hard deps: operations.inventory, core.billing, core.rbac

- [ ] **Dead-Stock & Turnover** ([[../../domains/operations/operations-reporting/features/dead-stock-report|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Spend Analytics & Supplier Performance** ([[../../domains/operations/operations-reporting/features/spend-analytics|spec]]) — hand-check: open widget on `OperationsDashboardPage` at `/operations/dashboard`.; filter by supplier/category/period; drill to a supplier; Excel export; no writes.
- [ ] **Valuation Report** ([[../../domains/operations/operations-reporting/features/valuation-report|spec]]) — hand-check: open widgets on `OperationsDashboardPage` at `/operations/dashboard`.; date-range filter (recomputes/reads cache); Excel export; no writes.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Stock Adjustments — `operations.stock-adjustments`

Build: `/flowflex:start operations.stock-adjustments` · Done: `/flowflex:done operations.stock-adjustments` · Spec: [[../../domains/operations/stock-adjustments/_module|hub]] · Hard deps: operations.inventory, core.billing, core.rbac

- [ ] **Adjustment & Approval** ([[../../domains/operations/stock-adjustments/features/adjustment-approval|spec]]) — hand-check: open `StockAdjustmentResource` at `/operations/adjustments`.; create adjustment (applies or queues by threshold); approve pending (blocked for the adjuster); filter by reason/period.
- [ ] **Stocktake** ([[../../domains/operations/stock-adjustments/features/stocktake|spec]]) — hand-check: open `StocktakePage` at `/operations/stocktake`.; enter counts → preview computed deltas → confirm → adjustments created/applied; large runs throttled.
- [ ] **Write-Off Report** ([[../../domains/operations/stock-adjustments/features/write-off-report|spec]]) — hand-check: open report filters on `StockAdjustmentResource` at `/operations/adjustments`.; filter by reason/period → totals recompute; export to Excel for finance.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Suppliers — `operations.suppliers`

Build: `/flowflex:start operations.suppliers` · Done: `/flowflex:done operations.suppliers` · Spec: [[../../domains/operations/suppliers/_module|hub]] · Hard deps: operations.inventory, core.billing, core.rbac

- [ ] **Supplied-Items Catalogue** ([[../../domains/operations/suppliers/features/supplier-catalogue|spec]]) — hand-check: open supplied-items relation manager under `OpsSupplierResource` at `/operations/suppliers/{id}`.; add item link; toggle preferred (unsets previous, confirm); edit cost/lead time.
- [ ] **Supplier Performance** ([[../../domains/operations/suppliers/features/supplier-performance|spec]]) — hand-check: open performance panel on `OpsSupplierResource` view at `/operations/suppliers/{id}`.; date-range filter *(assumed)*; click PO → PO view; no writes.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Goods Receipt — `operations.goods-receipt`

Build: `/flowflex:start operations.goods-receipt` · Done: `/flowflex:done operations.goods-receipt` · Spec: [[../../domains/operations/goods-receipt/_module|hub]] · Hard deps: operations.purchase-orders, operations.inventory, core.billing, core.rbac

- [ ] **Quality Check (Accept / Reject)** ([[../../domains/operations/goods-receipt/features/quality-check|spec]]) — hand-check: open accept/reject columns within `ReceiveGoodsPage` at `/operations/goods-receipts/receive`.; adjust accepted/rejected split; reason enforced; totals + discrepancy recompute live.
- [ ] **Receiving** ([[../../domains/operations/goods-receipt/features/receiving|spec]]) — hand-check: open `ReceiveGoodsPage` at `/operations/goods-receipts/receive` (and read-only `GoodsReceiptResource` for history).; pick PO → grid prefills open qty; edit accepted/rejected (validation live); reason required on reject; confirm → atomic post → GRN
- [ ] **GoodsReceived Event (3-Way Match)** ([[../../domains/operations/goods-receipt/features/three-way-match-event|spec]]) — hand-check: background — trigger it (: `GoodsReceived` fired by `GrnService::receive`.), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Purchase Orders — `operations.purchase-orders`

Build: `/flowflex:start operations.purchase-orders` · Done: `/flowflex:done operations.purchase-orders` · Spec: [[../../domains/operations/purchase-orders/_module|hub]] · Hard deps: operations.inventory, operations.suppliers, core.billing, core.rbac, foundation.queues

- [ ] **PO PDF & Supplier Email** ([[../../domains/operations/purchase-orders/features/pdf-and-email|spec]]) — hand-check: background — trigger it (: `send` action dispatches `GeneratePoPdfJob` then `PurchaseOrderMail`; preview link opens), then check the visible result named in the spec
- [ ] **PO Lifecycle** ([[../../domains/operations/purchase-orders/features/po-lifecycle|spec]]) — hand-check: open `PurchaseOrderResource` at `/operations/purchase-orders`.; add lines (cost auto-fills from catalogue); `send` action (confirm → PDF/mail queued); `cancel` action (blocked after receipt); re
- [ ] **Requisition Conversion** ([[../../domains/operations/purchase-orders/features/requisition-conversion|spec]]) — hand-check: open action on `PurchaseOrderResource` / requisition view at `/operations/purchase-orders`.; pick an approved requisition → PO form prefilled → adjust → save draft → send.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## procurement

### Supplier Catalogue — `procurement.catalogue`

Build: `/flowflex:start procurement.catalogue` · Done: `/flowflex:done procurement.catalogue` · Spec: [[../../domains/procurement/supplier-catalogue/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Catalogue Items** ([[../../domains/procurement/supplier-catalogue/features/catalogue-items|spec]]) — hand-check: open "Catalogue Items" (`/operations/procurement/catalogue`); create/edit form (supplier picker, category, price, date range, lead time); category filter; validity badges.
- [ ] **Preferred Supplier per Category** ([[../../domains/procurement/supplier-catalogue/features/preferred-supplier|spec]]) — hand-check: open none — "Set preferred" action on catalogue items / supplier-status rows, grouped by category.; toggle preferred → confirm swap if another exists.
- [ ] **Supplier Self-Onboarding Portal** ([[../../domains/procurement/supplier-catalogue/features/supplier-portal|spec]]) — hand-check: open "Supplier onboarding" (`/portal/suppliers/onboard/{token}`) — Vue + Inertia.; stepper next/back (pinia wizard state); file uploads with client validation; submit → confirmation screen.
- [ ] **Supplier Status (Approval & Blacklist)** ([[../../domains/procurement/supplier-catalogue/features/supplier-status|spec]]) — hand-check: open "Supplier Status" (`/operations/procurement/supplier-status`); approve / set-pending / blacklist row actions; blacklist requires a notes field before save.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Procurement Approvals — `procurement.approvals`

Build: `/flowflex:start procurement.approvals` · Done: `/flowflex:done procurement.approvals` · Spec: [[../../domains/procurement/approvals/_module|hub]] · Hard deps: core.billing, core.rbac, core.notifications

- [ ] **Approval Matrix & Routing** ([[../../domains/procurement/approvals/features/approval-matrix|spec]]) — hand-check: open "Approval Rules" (`/operations` → Procurement → Settings → Approval Rules); create/edit rule form (amount range, category select, role select, level, escalation days); overlap validation inline.
- [ ] **Approver Delegation** ([[../../domains/procurement/approvals/features/delegation|spec]]) — hand-check: open "My Delegations" (`/operations` → Procurement → Settings → Delegations); create delegation (delegate picker, date range); overlap validation inline; revoke (delete).
- [ ] **SLA Escalation** ([[../../domains/procurement/approvals/features/escalation|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Unified Pending Approvals Queue** ([[../../domains/procurement/approvals/features/pending-approvals-queue|spec]]) — hand-check: open "Pending Approvals" (`/operations/procurement/approvals/pending`); approve/reject with comment (comment required on reject) → optimistic row removal + toast; escalated rows badged.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Goods Receipt Notes (3-Way Match layer) — `procurement.goods-receipt`

Build: `/flowflex:start procurement.goods-receipt` · Done: `/flowflex:done procurement.goods-receipt` · Spec: [[../../domains/procurement/goods-receipt/_module|hub]] · Hard deps: operations.goods-receipt, finance.ap, core.billing, core.rbac

- [ ] **Discrepancy Resolution** ([[../../domains/procurement/goods-receipt/features/discrepancy-resolution|spec]]) — hand-check: open resolution modal on the compare pane.; choose action → notes required → confirm → optimistic status change + toast.
- [ ] **3-Way Match Evaluation** ([[../../domains/procurement/goods-receipt/features/match-evaluation|spec]]) — hand-check: open "3-Way Match" (`/operations/procurement/matches`); open a match → three-column compare; auto-approved rows badged green; discrepancies badged with variance; filter by status.
- [ ] **Payment Gate** ([[../../domains/procurement/goods-receipt/features/payment-gate|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Purchase Requisitions — `procurement.requisitions`

Build: `/flowflex:start procurement.requisitions` · Done: `/flowflex:done procurement.requisitions` · Spec: [[../../domains/procurement/requisitions/_module|hub]] · Hard deps: procurement.approvals, core.billing, core.rbac, core.notifications

- [ ] **Approval Flow** ([[../../domains/procurement/requisitions/features/approval-flow|spec]]) — hand-check: open approval acts happen from the Pending Approvals queue or the requisition infolist's approval timeline.; approve → advance + notify next level (optimistic); reject → require comment, notify requester.
- [ ] **Budget Check** ([[../../domains/procurement/requisitions/features/budget-check|spec]]) — hand-check: open none — renders as a callout on `RequisitionResource` create/edit + a badge on over-budget rows.; none beyond acknowledging; submit still allowed.
- [ ] **Catalogue Picker** ([[../../domains/procurement/requisitions/features/catalogue-picker|spec]]) — hand-check: open none — modal/slide-over within `RequisitionResource`.; type-ahead search; add item → new line appears with agreed price; free-text line still allowed alongside.
- [ ] **Convert to Purchase Order** ([[../../domains/procurement/requisitions/features/convert-to-po|spec]]) — hand-check: open none — "Convert to PO" action on approved rows.; click convert → confirm → optimistic status change → link to PO.
- [ ] **Create Requisition** ([[../../domains/procurement/requisitions/features/create-requisition|spec]]) — hand-check: open "Requisitions" (`/operations/procurement/requisitions`) with My requisitions / Approval queue tabs.; add/remove lines; live total; "Save draft" vs "Submit"; convert action on approved rows (convert-to-po).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Spend Analytics — `procurement.spend`

Build: `/flowflex:start procurement.spend` · Done: `/flowflex:done procurement.spend` · Spec: [[../../domains/procurement/spend-analytics/_module|hub]] · Hard deps: procurement.requisitions, operations.purchase-orders, core.billing, core.rbac

- [ ] **Committed vs Actual (+ Budget)** ([[../../domains/procurement/spend-analytics/features/committed-vs-actual|spec]]) — hand-check: open none — a stat/chart block on `SpendAnalyticsDashboard`.; period follows dashboard; hover for figures; drill to POs.
- [ ] **Spend Report Export** ([[../../domains/procurement/spend-analytics/features/export|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Maverick Spend Detection** ([[../../domains/procurement/spend-analytics/features/maverick-spend|spec]]) — hand-check: open none of its own — `MaverickSpendWidget` on `SpendAnalyticsDashboard`.; click stat → filtered line list; period follows the dashboard filter.
- [ ] **Savings Tracking** ([[../../domains/procurement/spend-analytics/features/savings-tracking|spec]]) — hand-check: open none — `SavingsWidget` on `SpendAnalyticsDashboard`.; period follows dashboard filter; drill to line detail.
- [ ] **Spend Breakdown** ([[../../domains/procurement/spend-analytics/features/spend-breakdown|spec]]) — hand-check: open "Spend Analytics" (`/operations/procurement/spend`); change filters → charts recompute (from cache); drill into a supplier/category; export (export).
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Purchase Orders (Procurement layer) — `procurement.purchase-orders`

Build: `/flowflex:start procurement.purchase-orders` · Done: `/flowflex:done procurement.purchase-orders` · Spec: [[../../domains/procurement/purchase-orders/_module|hub]] · Hard deps: operations.purchase-orders, procurement.requisitions, procurement.approvals, core.billing, core.rbac

- [ ] **Create PO from Requisition** ([[../../domains/procurement/purchase-orders/features/create-from-requisition|spec]]) — hand-check: open "Purchase Orders" (`/operations/procurement/purchase-orders`).; open PO → detail with sourcing tab, approval actions, commitment figures; link back to the source requisition.
- [ ] **PO Approval (Final Sign-off)** ([[../../domains/procurement/purchase-orders/features/po-approval|spec]]) — hand-check: open approval timeline on the PO detail; acts from the pending queue.; approve → advance (optimistic); reject → comment required; on final approve the PO becomes sendable.
- [ ] **Sourcing / Quote Comparison** ([[../../domains/procurement/purchase-orders/features/sourcing|spec]]) — hand-check: open "Sourcing board" (`/operations/procurement/purchase-orders/{po}/sourcing`); add quote → card appears; select → confirm supplier swap → optimistic highlight + PO supplier updates; blacklisted suppliers not s
- [ ] **Spend Commitment Tracking** ([[../../domains/procurement/purchase-orders/features/spend-commitment|spec]]) — hand-check: open none of its own — badges on the PO table + a "committed vs actual" stat widget in the Procurement nav.; change period → figures recompute; drill to the spend dashboard.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## workplace

### Desk Booking — `workplace.desk-booking`

Build: `/flowflex:start workplace.desk-booking` · Done: `/flowflex:done workplace.desk-booking` · Spec: [[../../domains/workplace/desk-booking/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac

- [ ] **Book a Desk** ([[../../domains/workplace/desk-booking/features/book-a-desk|spec]]) — hand-check: open book modal on `DeskBookingPage`.; click free desk → confirm modal → optimistic marker flip to "mine"; polling reconciles.
- [ ] **Check-in & Auto-release** ([[../../domains/workplace/desk-booking/features/check-in-release|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Floor Map** ([[../../domains/workplace/desk-booking/features/floor-map|spec]]) — hand-check: open `DeskBookingPage` — "Desk Booking" (`/workplace/desks`); desk CRUD on `DeskResource` (`/workplace/desks/manage; pick date → map recolours; click a free desk → book modal; polling refresh 60s.
- [ ] **Team View** ([[../../domains/workplace/desk-booking/features/team-view|spec]]) — hand-check: open "Team" + "My bookings" tabs on `DeskBookingPage`.; switch date → list + markers update; click colleague → map focus.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Workplace Analytics — `workplace.workplace-analytics`

Build: `/flowflex:start workplace.workplace-analytics` · Done: `/flowflex:done workplace.workplace-analytics` · Spec: [[../../domains/workplace/workplace-analytics/_module|hub]] · Hard deps: workplace.rooms, core.billing, core.rbac

- [ ] **Export Report** ([[../../domains/workplace/workplace-analytics/features/export-report|spec]]) — hand-check: open "Export" action on `WorkplaceDashboardPage`.; click "Export" → generate → download; repeated clicks throttled.
- [ ] **Space Optimisation** ([[../../domains/workplace/workplace-analytics/features/space-optimisation|spec]]) — hand-check: open "Underused space" widget on `WorkplaceDashboardPage`.; click item → open the room/desk record; adjust range → list recomputes.
- [ ] **Utilisation Dashboard** ([[../../domains/workplace/workplace-analytics/features/utilisation-dashboard|spec]]) — hand-check: open `WorkplaceDashboardPage` — "Workplace Analytics" (`/workplace/analytics`), apex charts.; change range → widgets refresh from cache; export → throttled download.
- [ ] **Utilisation Widgets** ([[../../domains/workplace/workplace-analytics/features/utilisation-widgets|spec]]) — hand-check: open mounted on `WorkplaceDashboardPage` (and reusable on the `/workplace` panel dashboard).; hover chart → tooltip; soft widgets simply absent when their module is off.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Facility Maintenance — `workplace.maintenance`

Build: `/flowflex:start workplace.maintenance` · Done: `/flowflex:done workplace.maintenance` · Spec: [[../../domains/workplace/maintenance/_module|hub]] · Hard deps: core.billing, core.rbac, core.files, core.notifications

- [ ] **Assignment & Workflow** ([[../../domains/workplace/maintenance/features/assignment-workflow|spec]]) — hand-check: open assign/resolve/close as row + detail actions on `MaintenanceRequestResource`.; "Assign" (pick staff/contractor) → "Start" → "Resolve" (attach after-photo) → "Close"; illegal transitions hidden.
- [ ] **Preventive Schedules** ([[../../domains/workplace/maintenance/features/preventive-schedules|spec]]) — hand-check: open `MaintenanceScheduleResource` list/form at `/workplace/maintenance/schedules`.; create/edit schedule; toggle active; next-due shown; generated requests link back via `schedule_id`.
- [ ] **Report a Request** ([[../../domains/workplace/maintenance/features/report-request|spec]]) — hand-check: open `MaintenanceRequestResource` create/list at `/workplace/maintenance`.; "Log an issue" -> form -> photo upload -> submit; row -> detail infolist.
- [ ] **SLA Tracking** ([[../../domains/workplace/maintenance/features/sla-tracking|spec]]) — hand-check: open "Overdue" tab + SLA/overdue column on `MaintenanceRequestResource`.; switch to "Overdue" tab → breached requests only; overdue chip on each row.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Room Booking — `workplace.room-booking`

Build: `/flowflex:start workplace.room-booking` · Done: `/flowflex:done workplace.room-booking` · Spec: [[../../domains/workplace/room-booking/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Book a Room** ([[../../domains/workplace/room-booking/features/book-a-room|spec]]) — hand-check: open `RoomBookingPage` — "Room Booking" (`/workplace/rooms/calendar`), `saade/filament-fullcalendar`.; click/drag a slot → booking modal → confirm → optimistic calendar block; polling refresh 30s.
- [ ] **Check-in & No-show Release** ([[../../domains/workplace/room-booking/features/check-in-release|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Recurring Bookings** ([[../../domains/workplace/room-booking/features/recurring-bookings|spec]]) — hand-check: open recurrence controls inside the Book a Room modal on `RoomBookingPage`.; enable recurrence → pick freq + until → submit → summary of created/skipped.
- [ ] **Room Catalogue** ([[../../domains/workplace/room-booking/features/room-catalogue|spec]]) — hand-check: open `RoomResource` list/form at `/workplace/rooms`.; create/edit room; toggle bookable inline.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Visitor Management — `workplace.visitor-management`

Build: `/flowflex:start workplace.visitor-management` · Done: `/flowflex:done workplace.visitor-management` · Spec: [[../../domains/workplace/visitor-management/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications, foundation.email

- [ ] **Check-in & Kiosk** ([[../../domains/workplace/visitor-management/features/check-in|spec]]) — hand-check: open `VisitorKioskPage` — "Visitor Kiosk" (`/workplace/kiosk`), kiosk-role device session; optional Vue+Inertia rec; type name → match expected → confirm → badge assigned + host pinged; walk-in path fills fields inline. Lookup + check-in are rate-
- [ ] **GDPR Purge** ([[../../domains/workplace/visitor-management/features/gdpr-purge|spec]]) — hand-check: background — trigger it (: scheduled console command (`PurgeVisitorsCommand`), daily. No page.), then check the visible result named in the spec
- [ ] **Pre-registration** ([[../../domains/workplace/visitor-management/features/pre-registration|spec]]) — hand-check: open `VisitorResource` create/edit at `/workplace/visitors`.; create expected visitor → confirmation mail dispatched; re-register from a past record.
- [ ] **Visitor Log** ([[../../domains/workplace/visitor-management/features/visitor-log|spec]]) — hand-check: open `VisitorResource` list with log filters at `/workplace/visitors`.; filter → export; click row → visit detail infolist.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean
