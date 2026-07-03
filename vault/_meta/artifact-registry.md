---
type: meta
color: "#A78BFA"
generated: 2026-07-03
---

# Artifact Registry

> **Generated 2026-07-03** by scraping every module's `## Filament Artifacts` section (wave 3b). Regenerate rather than hand-edit; per-module truth lives in each `architecture.md`. Permission column = the first `can('...')` verb in the module's access contract.

352 artifacts across 168 modules; 10 backend-only modules with explicit **None** markers.

## Artifacts

| Module | Artifact | Kind (ui-strategy) | Blueprint / Tweaks | Permission |
|---|---|---|---|---|
| ai.copilot | CopilotPage | #8 shared inbox / chat / conversation | - | ai.copilot.use |
| ai.document-intelligence | DocumentExtractionResource | #1 CRUD resource | tweaks: state-badge-column (processing/extracted/reviewed/applied/failed), custom-header-actions (apply, retry) | ai.document-intelligence.view-any |
| ai.model-config | AiConfigPage | #7 custom page (settings form) | *(closest — single settings form, not a multi-step wizard; flagged)* | ai.config.manage |
| ai.model-config | AiUsageDashboardPage | #6 dashboard page | - | ai.config.manage |
| ai.workflow-builder | WorkflowResource | #1 CRUD resource | tweaks: custom-header-actions (enable/disable, dry-run), state-badge-column (`is_active`) | ai.workflows.view-any |
| ai.workflow-builder | WorkflowBuilderPage | #9 Report builder / query UI | — builder rail = trigger picker + condition rows + action rows *(assumed: v1 is the list-based builder; a drag-canvas node editor is a new UI kind requiring an ADR + ui-strategy row first)* | ai.workflows.view-any |
| ai.workflow-builder | WorkflowRunResource | #2 detail with tabs | tweaks: read-only-flow-owned (rows written by `RunWorkflowJob`), view-page-tabs (trigger payload, node trace) | ai.workflows.view-any |
| analytics.dashboards | DashboardBuilderPage | #6 Dashboard custom page | (+ drag-drop grid builder extension *(assumed)*) | analytics.dashboards.view-any |
| analytics.dashboards | DashboardResource | #1 CRUD resource | tweaks: `custom-header-actions` (share toggle) *(assumed)* | analytics.dashboards.view-any |
| analytics.data-views | DataViewsPage | #17 Gallery / directory | - | analytics.data-views.view-any |
| analytics.data-views | Per-view render | #9 Report builder / query UI | — result pane + drill-down; builder rail reduced to date-range (views are shipped, not user-built) | analytics.data-views.view-any |
| analytics.kpi-tracking | KpiResource | #1 CRUD resource | tweaks: `custom-header-actions` (record manual value) *(assumed)* | analytics.kpis.view-any |
| analytics.kpi-tracking | KpiDashboardPage | #6 Dashboard custom page | - | analytics.kpis.view-any |
| analytics.report-builder | ReportBuilderPage | #9 Report builder custom page | - | analytics.reports.view-any |
| analytics.report-builder | ReportResource | #1 CRUD resource | tweaks: `custom-header-actions` (run, export) | analytics.reports.view-any |
| analytics.scheduled-exports | ScheduledExportResource | #1 CRUD resource | tweaks: custom-header-actions (pause/resume), relation-manager-timeline (delivery log, read-only) | analytics.exports.view-any |
| communications.automations | CommsAutomationRuleResource | #1 CRUD resource | tweaks: inline-relation-repeater (condition/action repeaters), custom-header-actions (activate/deactivate) *(assumed)* | comms.automations.view-any |
| communications.automations | ChatbotFlowResource | #1 CRUD resource | tweaks: inline-relation-repeater (node/option builder) | comms.automations.view-any |
| communications.broadcast | BroadcastResource | #1 CRUD resource | tweaks: view-page-tabs (compose / preview / funnel), state-badge-column (broadcast state), custom-header-actions (send / schedule) | comms.broadcast.view-any |
| communications.comms-analytics | CommsAnalyticsDashboard | #6 dashboard page | - | comms.analytics.view |
| communications.email-channel | EmailChannelResource | #1 CRUD resource | tweaks: custom-header-actions (test-connection) | comms.email.view-any |
| communications.internal-messaging | InternalMessagingPage | #8 chat custom page | - | comms.internal.use |
| communications.shared-inbox | SharedInboxPage | #8 inbox custom page | - | comms.inbox.view-any |
| communications.shared-inbox | ChannelResource | #1 CRUD resource | tweaks: state-badge-column (active/inactive), custom-header-actions (activate/deactivate) *(assumed)* | comms.inbox.view-any |
| communications.sms-channel | SmsChannelResource | #1 CRUD resource | tweaks: custom-header-actions (test-send) | comms.sms.view-any |
| communications.sms-channel | Opt-out list | #1 CRUD resource | tweaks: read-only-flow-owned (writes owned by `OptOutService` / STOP webhook) | comms.sms.view-any |
| communications.whatsapp | WhatsAppTemplateResource | #1 CRUD resource | tweaks: state-badge-column (approval status), custom-header-actions (submit-for-approval) | comms.whatsapp.view-any |
| communications.whatsapp | WhatsAppConfigPage | #7 wizard custom page | - | comms.whatsapp.view-any |
| core.api-clients | ApiClientResource | #1 CRUD resource | tweaks: custom-header-actions (create-token copy-once modal, rotate, revoke, revoke-all) | core.api.view-any |
| core.audit-log | AuditLogResource (/app) | #1 CRUD resource | tweaks: read-only-flow-owned (`AuditLogger` owns all writes → `canCreate(): false`, no edit/delete) | core.audit.view-any |
| core.audit-log | Cross-company log view (/admin) | #1 CRUD resource | tweaks: read-only-flow-owned | core.audit.view-any |
| core.billing-engine | BillingResource (/app) | #1 CRUD resource | tweaks: read-only-flow-owned (`BillingService` + Stripe own all invoice writes → `canCreate(): false`), state-badge-column (invoice status), pdf-preview-panel (invoice PDF), custom-header-actions (manage payment method) | core.billing.view-any |
| core.company-settings | CompanySettingsPage (/app) | #7 Multi-step wizard custom page | - | core.settings.view-any |
| core.data-import | DataImportResource (/app) | #1 CRUD resource | tweaks: state-badge-column (import status), custom-header-actions (download error report) | core.import.view-any |
| core.data-import | DataImportResource Create page (import wizard) | #7 Multi-step wizard custom page | - | core.import.view-any |
| core.data-privacy | DsarRequestResource (/app) | #1 CRUD resource | tweaks: state-badge-column (DSAR status), custom-header-actions (Process, Reject; access rows expose the result-ZIP download) | core.privacy.view-any |
| core.data-privacy | ConsentLog resource (/app) | #1 CRUD resource | tweaks: read-only-flow-owned (records appended by consent-capture flows, not hand-edited) | core.privacy.view-any |
| core.data-privacy | DataExportPage (/app) | #9 Report builder / query UI custom page *(assumed — closest blueprint for a single-action export + result pane)* | - | core.privacy.view-any |
| core.health-monitoring | SystemStatusPage (/app) | #6 Dashboard custom page *(assumed — status tiles + poll; closest blueprint)* | - | core.health.view-any |
| core.health-monitoring | Pulse dashboard (/admin /pulse) | external package dashboard (Laravel Pulse's own UI — not a FlowFlex-built Filament artifact) | n/a — linked from `/admin` Monitoring nav | core.health.view-any |
| core.health-monitoring | Horizon dashboard (/admin /horizon) | external package dashboard (Laravel Horizon's own UI — not a FlowFlex-built Filament artifact) | n/a — linked from `/admin` Monitoring nav | core.health.view-any |
| core.invitation-system | InvitationResource (/app) | #1 CRUD resource | tweaks: state-badge-column (pending / accepted / revoked / expired), custom-header-actions (resend, revoke) | core.invitations.view-any |
| core.module-marketplace | ModuleMarketplacePage (/app) | #17 Gallery / directory grid custom page | - | core.marketplace.view-any |
| core.notifications | Notification bell (all panels) | #10 Notification bell (render hook) | - | - |
| core.notifications | NotificationPreferencesPage | Custom Filament Page (settings form) *(assumed — no board-kind blueprint applies)* | Filament Page hosting a schema form () | - |
| core.rbac | RoleResource | #1 CRUD resource | tweaks: custom-header-actions (none non-CRUD) | core.rbac.view-any |
| core.rbac | UserResource | #1 CRUD resource | tweaks: custom-header-actions (assign-roles, deactivate, transfer-ownership, invite [soft-dep on invitations]) | core.rbac.view-any |
| core.setup-wizard | SetupWizardPage | #7 Multi-step wizard custom page | - | core.setup.complete |
| core.spotlight | Spotlight (Livewire overlay) | Render-hook chrome component *(assumed — no dedicated ui-strategy row; closest analogue is #10 notification bell, render hook)* | - | - |
| core.staff-console | CompanyResource (+ List/Create/Edit) | #1 CRUD resource | tweaks: view-page-tabs (Modules / Invoices / Users relation managers), custom-header-actions (suspend-with-reason), inline-relation-repeater (n/a) | - |
| core.staff-console | ModulesRelationManager | #1 CRUD resource (relation table) | tweaks: custom-header-actions (activate / deactivate — free-core deactivation refused) | - |
| core.staff-console | InvoicesRelationManager | #1 CRUD resource (relation table) | tweaks: read-only-flow-owned (billing owns writes) | - |
| core.staff-console | UsersRelationManager | #1 CRUD resource (relation table) | tweaks: read-only-flow-owned | - |
| core.staff-console | BillingInvoiceResource (+ ListBillingInvoices) | #1 CRUD resource | tweaks: read-only-flow-owned (`canCreate(): false`; billing-engine owns writes) | - |
| core.staff-console | AdminResource (+ List/Create/Edit) | #1 CRUD resource | tweaks: custom-header-actions (self / last-admin delete guard) | - |
| core.staff-console | UserResource (+ ListUsers) | #1 CRUD resource | tweaks: read-only-flow-owned | - |
| core.staff-console | ActivityResource (+ ListActivities) | #1 CRUD resource | tweaks: read-only-flow-owned | - |
| core.staff-console | AdminLogin page | - | — | - |
| core.staff-console | Horizon + Pulse nav links (Monitoring group) | - | — | - |
| core.webhooks | WebhookEndpointResource (+ List/Create/Edit) | #1 CRUD resource | tweaks: state-badge-column (active/auto-disabled), custom-header-actions (send-test, rotate-secret), inline-relation-repeater (domain-grouped event checkboxes) | core.webhooks.view-any |
| core.webhooks | Deliveries relation manager | #1 CRUD resource (relation table) | tweaks: read-only-flow-owned (`DeliverWebhookJob` owns writes) | core.webhooks.view-any |
| core.workspace-hub | WorkspaceHubPage | #17 Gallery / directory grid custom page | - | core.hub.view |
| crm.activities | ActivityResource | #1 CRUD resource | tweaks: state-badge-column (task done/overdue), custom-header-actions (complete task) | crm.activities.view-any |
| crm.activities | ActivityTimeline (Livewire) | #2 record detail timeline | tweaks: relation-manager-timeline (host Contact/Deal/Account view pages render it as a timeline tab; bubble styling cues ) | crm.activities.view-any |
| crm.appointment-scheduling | MeetingTypeResource | #1 CRUD resource | tweaks: custom-header-actions (copy booking link) | crm.scheduling.view-any |
| crm.appointment-scheduling | BookingResource | #1 CRUD resource | tweaks: state-badge-column (confirmed/cancelled/completed/no-show), custom-header-actions (cancel, mark no-show) | crm.scheduling.view-any |
| crm.appointment-scheduling | AvailabilityPage | #7 custom page | — single-step settings form for the rep's own working hours *(assumed: single-step; not a true multi-step wizard — see )* | crm.scheduling.view-any |
| crm.appointment-scheduling | Public booking page | #16 Vue + Inertia (public-vue) | guest-facing, no Filament — | crm.scheduling.view-any |
| crm.contacts | ContactResource | #1 CRUD resource | tweaks: state-badge-column (lifecycle stage), custom-header-actions (change-lifecycle quick-move, export), view-page-tabs | crm.contacts.view-any |
| crm.contacts | Contact view page | #2 record detail with tabs | tweaks: view-page-tabs, relation-manager-timeline (Activities tab, soft-dep —  bubble cues) | crm.contacts.view-any |
| crm.contacts | AccountResource | #1 CRUD resource | tweaks: view-page-tabs | crm.contacts.view-any |
| crm.contracts | ContractResource | #1 CRUD resource | tweaks: state-badge-column (contract status), custom-header-actions (create-from-deal, send, sign-off, renew, terminate), pdf-preview-panel (signed PDF) | crm.contracts.view-any |
| crm.contracts | ContractRenewalsPage (assumed) | #3 custom page | — read-only queue grouped by urgency (90 / 30 / overdue), renew/terminate per row | crm.contracts.view-any |
| crm.customer-segments | SegmentResource | #1 CRUD resource | tweaks: inline-relation-repeater (condition rule rows), inline-relation-repeater (static-list members) | crm.segments.view-any |
| crm.deal-rooms | DealRoomResource | #1 CRUD resource | tweaks: custom-header-actions (revoke), relation-manager-timeline (engagement panel) | crm.deal-rooms.view-any |
| crm.deal-rooms | Public room /room/{token} | #16 public/portal (Vue + Inertia) | scoped-portal guard + single-use signed, expiring, revocable token | crm.deal-rooms.view-any |
| crm.deals | DealResource | #1 CRUD resource | tweaks: view-page-tabs, state-badge-column, custom-header-actions (close / reopen / create-invoice) | crm.deals.view-any |
| crm.deals | Deal view page | #2 record detail with tabs | tweaks: view-page-tabs, relation-manager-timeline (Activities, if crm.activities) | crm.deals.view-any |
| crm.deals | CreateInvoiceAction | #2 view-page header action | tweak: custom-header-actions | crm.deals.view-any |
| crm.deals | CloseDealAction | #2 view-page header action | tweak: custom-header-actions | crm.deals.view-any |
| crm.email-integration | EmailConnectionResource | #1 CRUD resource (own only) | tweaks: custom-header-actions (connect via OAuth redirect / disconnect) | crm.email.view-any |
| crm.email-integration | EmailThread (Livewire) | #2 embedded conversation component | tweak: relation-manager-timeline ( bubble cues) | crm.email.view-any |
| crm.email-integration | Compose action | #2 view-page header action | tweak: custom-header-actions (send — `panel-action` limiter, comms) | crm.email.view-any |
| crm.forecasting | QuotaResource | #1 CRUD resource | standard resource (no tweaks) | crm.forecasting.view-any |
| crm.forecasting | ForecastPage | #6 Dashboard custom page | - | crm.forecasting.view-any |
| crm.leads | LeadResource | #1 CRUD resource | tweaks: custom-header-actions (convert-to-deal — own permission `crm.leads.convert`, hidden once `status = converted`) | crm.leads.view-any |
| crm.leads | ListLeads page | #1 CRUD resource (list page) | standard list page | crm.leads.view-any |
| crm.pipeline | PipelineBoardPage | #3 Kanban custom page | - | crm.pipeline.view |
| crm.pipeline | PipelineStageResource | #1 CRUD resource | standard resource; `ReorderStagesAction` for column order | crm.pipeline.view |
| crm.price-management | ProductResource | #1 CRUD resource | tweaks: inline-relation-repeater (volume-discount tiers relation manager) | crm.pricing.view-any |
| crm.price-management | PriceBookResource | #1 CRUD resource | tweaks: inline-relation-repeater (price-book entries with promo `valid_from`/`valid_until` windows) | crm.pricing.view-any |
| crm.price-management | VolumeDiscountResource | #1 CRUD resource | standard resource (also usable as a relation manager on `ProductResource`) | crm.pricing.view-any |
| crm.quotes | QuoteResource | #1 CRUD resource | tweaks: inline-relation-repeater (quote lines), custom-header-actions (send / new-version), state-badge-column | crm.quotes.view-any |
| crm.quotes | Quote view page | #2 detail | tweaks: view-page-tabs, pdf-preview-panel | crm.quotes.view-any |
| crm.quotes | Quotes/Accept.vue (public) | #16 public Vue + Inertia | external tokenised accept/decline surface — not a Filament artifact | crm.quotes.view-any |
| crm.referral-program | ReferralProgramResource | #1 CRUD resource | tweaks: inline-relation-repeater (reward config) | crm.referrals.view-any |
| crm.referral-program | ReferralResource | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (qualify / reward / reject) | crm.referrals.view-any |
| crm.referral-program | ReferralLeaderboardPage | #9 Report custom page | - | crm.referrals.view-any |
| crm.revenue-intelligence | DealHealthResource | #1 CRUD resource | tweaks: read-only-flow-owned (scores written by the `DealHealthService` recalc job — `canCreate(): false`) | crm.revenue-intelligence.view-any |
| crm.revenue-intelligence | WinLossPage | #9 Report custom page | - | crm.revenue-intelligence.view-any |
| crm.revenue-intelligence | RevenueIntelligenceDashboard | #6 Dashboard page | - | crm.revenue-intelligence.view-any |
| crm.sales-sequences | SequenceResource | #1 CRUD resource | tweaks: inline-relation-repeater (step builder), view-page-tabs (performance tab), custom-header-actions (activate / A/B config) | crm.sequences.view-any |
| crm.sales-sequences | SequenceEnrolmentResource | #1 CRUD resource | tweaks: state-badge-column (`active`/`paused`/`completed`/`unenrolled`), custom-header-actions (pause / resume / unenrol) | crm.sequences.view-any |
| crm.sales-sequences | Enrol action (Contact / Deal) | #1 CRUD resource | tweaks: custom-header-actions (enrol in sequence) | crm.sequences.view-any |
| customer-success.churn-risk | ChurnRiskResource | simple-resource (read-only + actions) | - | cs.churn.view-any |
| customer-success.health-scores | HealthScoreResource | #1 CRUD resource (read-only) | - | cs.health.view-any |
| customer-success.health-scores | HealthDashboardPage | #4 custom page | - | cs.health.view-any |
| customer-success.nps | NpsSurveyResource | simple-resource | - | cs.nps.view-any |
| customer-success.nps | NpsResponseResource | simple-resource (read-only) | - | cs.nps.view-any |
| customer-success.nps | NpsDashboardPage | custom-page | - | cs.nps.view-any |
| customer-success.playbooks | PlaybookResource | simple-resource | - | cs.playbooks.view-any |
| customer-success.playbooks | PlaybookRunResource | simple-resource | - | cs.playbooks.view-any |
| customer-success.qbr | QbrResource | simple-resource | - | cs.qbr.view-any |
| customer-success.success-analytics | CsDashboardPage | custom-page (+ apex charts) | - | cs.analytics.view |
| dms.approval-workflows | ApprovalWorkflowResource | #1 CRUD resource | tweaks: inline-relation-repeater (steps) | dms.approvals.view-any |
| dms.approval-workflows | ApprovalRequestResource | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (approve / reject / request-changes), relation-manager-timeline (audit trail, read-only) | dms.approvals.view-any |
| dms.document-library | DocumentLibraryPage | #11 tree custom page | — folder-tree sidebar + document grid | dms.library.view-any |
| dms.document-library | DocumentViewerPage | #2-style custom page | no dedicated viewer blueprint — record-detail preview rendered as a custom page *(assumed)*; passes | dms.library.view-any |
| dms.document-library | FolderResource | #1 CRUD resource | tweaks: inline-relation-repeater (folder-access rows) | dms.library.view-any |
| dms.retention-policies | RetentionPolicyResource | #1 CRUD resource | - | dms.retention.manage-policies |
| dms.retention-policies | LegalHoldResource | #1 CRUD resource | - | dms.retention.manage-policies |
| dms.retention-policies | Retention log | #1 (read-only) | - | dms.retention.manage-policies |
| dms.templates | DocumentTemplateResource | #1 CRUD resource | tweaks: custom-header-actions (duplicate/copy-on-edit for system templates) | dms.templates.view-any |
| dms.templates | GenerateFromTemplatePage | #7 wizard custom page | - | dms.templates.view-any |
| dms.version-control | Version-history relation manager | on `DocumentViewerPage` (#2-style library custom page) | tweak: relation-manager-timeline (read-only versions list) | dms.versions.view-any |
| dms.version-control | Upload-new-version action | custom-header-action on `DocumentViewerPage` | tweak: custom-header-actions (needs `dms.versions.upload`) | dms.versions.view-any |
| dms.version-control | Lock / unlock action + lock badge | custom-header-action + badge on `DocumentViewerPage` | tweak: custom-header-actions (force-unlock needs `dms.versions.force-unlock`) | dms.versions.view-any |
| dms.wiki | WikiPageResource | #1 CRUD resource | - | dms.wiki.view-any |
| dms.wiki | WikiViewerPage | #2-style custom page | - | dms.wiki.view-any |
| ecommerce.abandoned-cart | AbandonedCartResource | simple-resource (read-only) | - | ecommerce.abandoned-cart.view |
| ecommerce.orders | EcOrderResource | #1 CRUD resource | tweaks: state-badge-column (order status), custom-header-actions (mark-paid / fulfil / refund / cancel), relation-manager-timeline (`ec_order_events`) | ecommerce.orders.view-any |
| ecommerce.orders | OrderFulfilmentPage | #3 Kanban custom page | — read-only queue (unfulfilled · partial), no free drag reorder; expand-to-ship | ecommerce.orders.view-any |
| ecommerce.payments | EcPaymentResource | #1 CRUD resource | tweaks: read-only-flow-owned (writes owned by `EcPaymentService` / Stripe webhook — `canCreate(): false`), custom-header-actions (refund) | ecommerce.payments.view-any |
| ecommerce.products | EcProductResource | #1 CRUD resource | tweaks: state-badge-column (draft/active/archived), custom-header-actions (publish / archive) | ecommerce.products.view-any |
| ecommerce.products | EcCategoryResource | #1 CRUD resource | (no tweaks — standard CRUD) | ecommerce.products.view-any |
| ecommerce.promotions | CouponResource | #1 CRUD resource | tweaks: relation-manager-timeline (redemptions relation) | ecommerce.promotions.view-any |
| ecommerce.promotions | EcPromotionResource | #1 CRUD resource | tweaks: inline-relation-repeater (rule-builder) | ecommerce.promotions.view-any |
| ecommerce.reviews | ReviewResource | simple-resource | - | ecommerce.reviews.view-any |
| ecommerce.storefront | StorefrontSettingsPage | custom-page (tabbed) | - | - |
| ecommerce.storefront | StorefrontPageResource | simple-resource | - | - |
| ecommerce.variants | VariantsRelationManager | #1 CRUD resource (relation manager on `EcProductResource`) | tweaks: inline-relation-repeater, custom-header-actions (generate variants) | ecommerce.variants.manage |
| events.event-analytics | EventAnalyticsDashboard | #6 dashboard page + apex charts | - | events.analytics.view |
| events.events | EventResource | #1 CRUD resource | - | events.events.view-any |
| events.events | EventCalendarPage | #4 calendar custom page | - | events.events.view-any |
| events.events | Public landing | #16 public Vue+Inertia | - | events.events.view-any |
| events.registrations | RegistrationResource | #1 CRUD resource | - | events.registrations.view-any |
| events.registrations | CheckInPage | #7 custom page | - | events.registrations.view-any |
| events.speakers | SpeakerResource | #1 CRUD resource | - | events.speakers.view-any |
| events.speakers | Session assignment | relation manager | - | events.speakers.view-any |
| events.sponsors | SponsorResource | #1 CRUD resource | - | events.sponsors.view-any |
| events.tickets | Ticket types relation manager | relation manager | - | events.tickets.view-any |
| events.tickets | Purchases list | - | - | events.tickets.view-any |
| events.venues | VenueResource | #1 CRUD resource | - | events.venues.view-any |
| finance.accounts-payable | SupplierResource | #1 CRUD resource | tweaks: custom-header-actions (manage-suppliers) | finance.ap.view-any |
| finance.accounts-payable | BillResource | #1 CRUD resource | tweaks: state-badge-column (`BillState` badge + transition actions), custom-header-actions (approve — own permission + rate limiter), inline-relation-repeater (bill-line grid) | finance.ap.view-any |
| finance.accounts-payable | ApAgingPage | #9 custom page | — per supplier/bill aging buckets (current/30/60/90+), drill into a bill; realtime none | finance.ap.view-any |
| finance.accounts-payable | PaymentRunPage | #9 custom page (closest — batch worklist) | — select scheduled bills → batch preview with line-sum check → execute (SEPA/CSV export); realtime none | finance.ap.view-any |
| finance.accounts-receivable | DunningRuleResource | #1 CRUD resource | tweaks: — (plain resource; `is_active` toggle, unique `escalation_level` per company) | finance.ar.view-any |
| finance.accounts-receivable | ArAgingPage | #9 custom page | — aging buckets (current/1–30/31–60/61–90/90+) + DSO / turnover KPIs, drill to invoices; realtime none | finance.ar.view-any |
| finance.accounts-receivable | CustomerStatementPage | #9 custom page | — per-customer invoices / payments / running balance over a period; realtime none | finance.ar.view-any |
| finance.accounts-receivable | AllocatePaymentAction | action (modal) hosted on `ArAgingPage` / `CustomerStatementPage` | money mutation — names a `panel-action` limiter; live sum-check `sum(allocations) === amount_cents` | finance.ar.view-any |
| finance.accounts-receivable | WriteOffAction | action (modal) hosted on the AR invoice list | money mutation — names a `panel-action` limiter; amount = invoice open balance | finance.ar.view-any |
| finance.bank-accounts | BankAccountResource | #1 CRUD resource | tweaks: custom-header-actions (manage-accounts) | finance.bank.view-any |
| finance.bank-accounts | BankTransactionResource | #1 CRUD resource | tweaks: read-only-flow-owned (rows created by `ImportBankStatementJob`, mutated by reconcile) | finance.bank.view-any |
| finance.bank-accounts | ImportStatementPage | #7 wizard custom page | — upload CSV → map columns (date/description/amount) + date format → confirm → queued `ImportBankStatementJob`; realtime none | finance.bank.view-any |
| finance.bank-accounts | ReconciliationPage | #9 custom page (closest — two-panel matcher, no exact ui-strategy row) | — unreconciled txns (left) vs suggested journal lines (right) + bank-vs-GL balance strip, click to link; realtime none | finance.bank.view-any |
| finance.budgets | BudgetResource | #1 CRUD resource | tweaks: custom-header-actions (approve / revise / copy-from-year), inline-relation-repeater (per-account/period line grid) | finance.budgets.view-any |
| finance.budgets | BudgetVariancePage | #9 custom page | — per account/period budgeted/actual/variance grid + journal-entry drilldown; realtime none | finance.budgets.view-any |
| finance.cash-flow | CashFlowPage | #9 report custom page | — 13-week grid (opening/inflow/outflow/closing) + apex chart, scenario toggle, inline manual-item add; realtime none | finance.cashflow.view-any |
| finance.cash-flow | AddManualItemAction | action on `CashFlowPage` | inline modal action — adds/edits a manual inflow/outflow item; own permission `finance.cashflow.manage-items` + `panel-action` rate limiter (mutates money) | finance.cashflow.view-any |
| finance.expenses | ExpenseResource | #1 CRUD resource | tweaks: state-badge-column (expense state + submit/approve/reject/reimburse actions), custom-header-actions (submit / approve / reject / reimburse / CSV export) | finance.expenses.view-any |
| finance.expenses | ExpenseReportResource | #1 CRUD resource | tweaks: state-badge-column (report status), custom-header-actions (bulk-submit / CSV export) | finance.expenses.view-any |
| finance.expenses | ExpenseCategoryResource | #1 CRUD resource | — | finance.expenses.view-any |
| finance.financial-reporting | ProfitLossPage | #9 report custom page | — revenue/COGS/expenses/net-profit rows, comparison columns (prior period, budget when active), drill-down to journal entries; realtime none | finance.reporting.view-any |
| finance.financial-reporting | BalanceSheetPage | #9 report custom page | — assets/liabilities/equity as-of snapshot, asserts `assets = liabilities + equity`; realtime none | finance.reporting.view-any |
| finance.financial-reporting | CashFlowStatementPage | #9 report custom page | — operating/investing/financing sections (indirect method *(assumed)*); realtime none | finance.reporting.view-any |
| finance.fixed-assets | FixedAssetResource | #1 CRUD resource | tweaks: state-badge-column (status: active / fully-depreciated / disposed), custom-header-actions (dispose) | finance.assets.view-any |
| finance.fixed-assets | DepreciationRunPage | #7 wizard custom page | — pick run month → preview per-asset charge → post → result summary; realtime none | finance.assets.view-any |
| finance.forecasting | ForecastResource | #1 CRUD resource | tweaks: custom-header-actions (seed-from-actuals), inline-relation-repeater (projected-line grid per account/period) | finance.forecasting.view-any |
| finance.forecasting | ForecastComparisonPage | #9 custom page | — scenarios side by side + three-way projected/actual/budget columns per account/period (apex charts); budget columns hidden when budgets inactive; realtime none | finance.forecasting.view-any |
| finance.general-ledger | ChartOfAccountsResource | #1 CRUD resource | tweaks: custom-header-actions (seed default CoA) | finance.ledger.view-any |
| finance.general-ledger | JournalEntryResource | #1 CRUD resource | tweaks: read-only-flow-owned (all writes via `LedgerService::post` — no edit/delete), custom-header-actions (post manual entry / reverse), state-badge-column (posted / reversed *(assumed)*) | finance.ledger.view-any |
| finance.general-ledger | FiscalPeriodResource | #1 CRUD resource | tweaks: state-badge-column (open / closed), custom-header-actions (close / reopen) | finance.ledger.view-any |
| finance.general-ledger | TrialBalancePage | #9 report custom page | — date-range selector + per-account debit/credit grid, drill-down to journal lines; realtime none | finance.ledger.view-any |
| finance.invoicing | InvoiceResource | #1 CRUD resource | tweaks: state-badge-column (lifecycle badge + transition actions), custom-header-actions (send / record-payment / void), inline-relation-repeater (invoice line items), pdf-preview-panel (rendered invoice PDF) | finance.invoicing.view-any |
| finance.invoicing | CustomerResource | #1 CRUD resource | — | finance.invoicing.view-any |
| finance.multi-currency | CurrencyResource | #1 CRUD resource | tweaks: state-badge-column (active/inactive) | finance.currency.view-any |
| finance.multi-currency | ExchangeRateResource | #1 CRUD resource | — | finance.currency.view-any |
| finance.multi-currency | FxGainLossPage | #9 custom page | — per-period realised + unrealised FX gain/loss by currency; realtime none | finance.currency.view-any |
| finance.tax-management | TaxRateResource | #1 CRUD resource | tweaks: state-badge-column (active/reverse-charge flags) | finance.tax.view-any |
| finance.tax-management | TaxReturnPage | #9 custom page | — per-period output/input/net VAT return prep, file action, export; realtime none | finance.tax.view-any |
| foundation.filament-panels | AppPanelProvider / AdminPanelProvider | Panel shell — hosts all module artifacts (not a decision-table row) | (Switchboard+ chrome, full-height sidebar, spotlight) | {permission} |
| foundation.filament-panels | PanelLogin, EditProfile (shared App\Filament\Auth) | Filament framework auth pages (Livewire) | — | {permission} |
| hr.compensation-benefits | CompensationBandResource | #1 CRUD resource | tweaks: none | hr.compensation.view-any |
| hr.compensation-benefits | BenefitResource | #1 CRUD resource | tweaks: none | hr.compensation.view-any |
| hr.compensation-benefits | BenefitEnrollmentResource | #1 CRUD resource | tweaks: custom-header-actions (unenroll) | hr.compensation.view-any |
| hr.compensation-benefits | AdjustSalaryAction | #1 resource action (custom-header-action) | tweaks: custom-header-actions (adjust-salary) | hr.compensation.view-any |
| hr.compensation-benefits | CompReviewPage (assumed) | #7 wizard custom page | - | hr.compensation.view-any |
| hr.compensation-benefits | SalaryHistoryRelationManager | #2 relation manager | tweaks: read-only-flow-owned (owned by `CompensationService`), relation-manager-timeline | hr.compensation.view-any |
| hr.dei-metrics | DeiDashboardPage | #6 Dashboard custom page | - | hr.dei.view-dashboard |
| hr.employee-feedback | FeedbackResource | #1 CRUD resource | tweaks: custom-header-actions (give-feedback — `hr.feedback.give`; request-feedback — comms, rate-limited) | hr.feedback.view-any |
| hr.employee-feedback | OneOnOneResource | #1 CRUD resource | tweaks: *(none — participant-scoped list + action-item checklist)* | hr.feedback.view-any |
| hr.employee-feedback | RecognitionFeedPage | #17 Gallery / directory grid custom page | - | hr.feedback.view-any |
| hr.employee-profiles | EmployeeResource | #1 CRUD resource | tweaks: view-page-tabs (Personal / Employment / Documents / History), state-badge-column (employment status + transition action group), custom-header-actions (offboard — own permission), relation-manager-timeline (History via activitylog) | hr.employees.view-any |
| hr.employee-profiles | DepartmentResource | #1 CRUD resource | tweaks: *(none v1 — simple list; tree via `parent_department_id` assumed later)* *(assumed)* | hr.employees.view-any |
| hr.employee-profiles | OffboardAction | header action on `EmployeeResource` view page — tweak `custom-header-actions` | - | hr.employees.view-any |
| hr.employee-self-service | SelfServiceDashboardPage | #6 Dashboard custom page | - | hr.self-service.view |
| hr.employee-self-service | MyProfilePage | #7 custom page (single-step form) | — single-step profile form *(assumed — closest blueprint; no multi-step)* | hr.self-service.view |
| hr.employee-self-service | MyDocumentsPage | #17 Gallery / directory grid custom page *(assumed — own-doc list)* | - | hr.self-service.view |
| hr.hr-analytics | HrAnalyticsDashboard | #6 Dashboard custom page | - | hr.analytics.view |
| hr.leave-management | LeaveRequestResource | #1 CRUD resource | tweaks: state-badge-column (request status + transition group), custom-header-actions (approve / reject — each own permission, reject opens reason modal) | hr.leave.view-any |
| hr.leave-management | LeaveBalanceResource | #1 CRUD resource | tweaks: read-only-flow-owned (`canCreate(): false` — balances written by `LeaveService` + accrual commands) | hr.leave.view-any |
| hr.leave-management | LeaveTypeResource | #1 CRUD resource | tweaks: *(none — plain admin config)* | hr.leave.view-any |
| hr.leave-management | LeaveCalendarPage | #4 Calendar custom page | - | hr.leave.view-any |
| hr.onboarding | OnboardingResource | #1 CRUD resource | tweaks: view-page-tabs (plan view — checklist grouped by `assigned_role`: HR/IT/manager/employee), custom-header-actions (complete-task / skip-task — each own permission) | hr.onboarding.view-any |
| hr.onboarding | OnboardingTemplateResource | #1 CRUD resource | tweaks: inline-relation-repeater (ordered tasks with `assigned_role`) | hr.onboarding.view-any |
| hr.org-chart | OrgChartPage | #11 Org chart / tree custom page | - | hr.org.view |
| hr.payroll | PayrollRunResource | #1 CRUD resource | tweaks: state-badge-column (run status machine), custom-header-actions (process / approve / archive), view-page-tabs (run detail: payslip list + employer-cost summary) | hr.payroll.view-any |
| hr.payroll | PayslipResource | #1 CRUD resource | tweaks: read-only-flow-owned (`GeneratePayslipsJob` owns writes), pdf-preview-panel | hr.payroll.view-any |
| hr.payroll | PayrollEmployeeResource | #1 CRUD resource | tweaks: state-badge-column (`incomplete` → `ready`) *(assumed)* | hr.payroll.view-any |
| hr.payroll | DeductionTypeResource | #1 CRUD resource | — | hr.payroll.view-any |
| hr.performance-reviews | ReviewCycleResource | #1 CRUD resource | tweaks: state-badge-column (cycle state machine), custom-header-actions (activate / move-to-calibration / finalise) | hr.performance.view-any |
| hr.performance-reviews | ReviewResource | #1 CRUD resource | tweaks: state-badge-column (cycle-driven mode: editable `active` / locked `calibration` / frozen `finalised`) *(assumed)*, custom-header-actions (submit), view-page-tabs (side-by-side self vs manager for calibration) *(assumed)* | hr.performance.view-any |
| hr.performance-reviews | MyGoalsPage | #17 Gallery / Directory grid *(assumed)* | - | hr.performance.view-any |
| hr.recruitment | JobRequisitionResource | #1 CRUD resource | tweaks: state-badge-column (draft/open/closed), custom-header-actions (publish toggle) | hr.recruitment.view-any |
| hr.recruitment | ApplicantPipelinePage | #3 Kanban custom page | - | hr.recruitment.view-any |
| hr.recruitment | ApplicantResource | #1 CRUD resource | tweaks: state-badge-column, pdf-preview-panel (CV preview) | hr.recruitment.view-any |
| hr.recruitment | InterviewResource | #1 CRUD resource | — | hr.recruitment.view-any |
| hr.recruitment | OfferResource | #1 CRUD resource | tweaks: state-badge-column (draft/sent/accepted/declined), custom-header-actions (send offer) | hr.recruitment.view-any |
| hr.shift-scheduling | ShiftSchedulePage | #4 Calendar custom page | - | hr.shifts.view-any |
| hr.shift-scheduling | ShiftSwapRequestResource | #1 CRUD resource | tweaks: state-badge-column (pending/accepted/approved/declined), custom-header-actions (accept / approve / decline) | hr.shifts.view-any |
| hr.time-attendance | TimesheetResource | #1 CRUD resource | tweaks: state-badge-column (draft/submitted/approved/rejected), custom-header-actions (submit / approve / reject) | hr.time.view-any |
| hr.time-attendance | TimeEntryResource | #1 CRUD resource | — | hr.time.view-any |
| hr.workforce-planning | HeadcountPlanResource | #1 CRUD resource | — | hr.workforce.view-any |
| hr.workforce-planning | PlannedRoleResource | #1 CRUD resource | tweaks: state-badge-column (draft/approved/filled), custom-header-actions (approve-role / mark-filled) | hr.workforce.view-any |
| hr.workforce-planning | WorkforcePlanningDashboard | #6 Dashboard custom page | - | hr.workforce.view-any |
| it.access-provisioning | SystemResource | #1 CRUD resource | tweaks: — | it.access.view-any |
| it.access-provisioning | AccessGrantResource | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (grant / revoke) | it.access.view-any |
| it.access-provisioning | AccessTemplateResource | #1 CRUD resource | tweaks: inline-relation-repeater (systems) | it.access.view-any |
| it.access-provisioning | AccessReviewPage | #18 heat-map / matrix custom page | - | it.access.view-any |
| it.asset-inventory | AssetResource | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (assign / return / retire), relation-manager-timeline (assignment history) | it.assets.view-any |
| it.helpdesk | ItTicketResource | #1 CRUD resource | tweaks: state-badge-column, relation-manager-timeline (replies thread), custom-header-actions (assign / resolve) | it.helpdesk.view-any |
| it.helpdesk | ItHelpdeskQueuePage | #8 shared-inbox / work-queue custom page | - | it.helpdesk.view-any |
| it.it-reporting | ItDashboardPage | #6 dashboard custom page + apex charts | - | - |
| it.mdm-integration | MdmDeviceResource | #1 read-only synced (simple-resource) | - | it.mdm.view-any |
| it.mdm-integration | MdmConfigPage | #7 custom page (form) | - | it.mdm.view-any |
| it.software-licences | LicenceResource | #1 CRUD resource | tweaks: custom-header-actions (assign / revoke seat) | it.licences.view-any |
| legal.compliance-registers | FrameworkResource | #1 CRUD resource | tweaks: view-page-tabs (Controls, Tasks, Readiness) | legal.compliance.view-any |
| legal.compliance-registers | ControlResource | #1 CRUD resource | tweaks: state-badge-column (status enum), custom-header-actions (set status + evidence) | legal.compliance.view-any |
| legal.compliance-registers | ComplianceDashboardPage | #6 Dashboard | - | legal.compliance.view-any |
| legal.dsar-processing | DsarFulfilmentPage | #7 Wizard | — steps: Verify → Discover → Fulfil → Close *(assumed)* | legal.dsar.view-any |
| legal.dsar-processing | DsarRequestResource (extends core.privacy's) | #2 detail with tabs | tweaks: relation-manager-timeline (action trail), custom-header-actions (reject w/ required reason, record rectified) | legal.dsar.view-any |
| legal.legal-contracts | LegalContractResource | #1 CRUD resource | tweaks: view-page-tabs, state-badge-column, custom-header-actions (sign-off / renew / terminate) | legal.contracts.view-any |
| legal.legal-contracts | ContractLifecyclePage (assumed) | #3 custom page | — read-only queue grouped by urgency (overdue · ≤30d · ≤90d), no drag reorder | legal.contracts.view-any |
| legal.legal-spend | LegalExpenseResource | #1 CRUD resource | tweaks: state-badge-column (pending/approved/rejected), custom-header-actions (approve / reject) | legal.spend.view-any |
| legal.legal-spend | ApprovalQueuePage (assumed — not yet in build manifest) | #17 custom page | — pending expenses grouped by matter/vendor, bulk approve | legal.spend.view-any |
| legal.legal-spend | LegalSpendDashboardPage | #6 dashboard page | - | legal.spend.view-any |
| legal.matter-management | MatterResource | #1 CRUD resource | tweaks: view-page-tabs, state-badge-column, custom-header-actions (close / change-status), relation-manager-timeline (matter events tab) | legal.matters.view-any |
| legal.policy-library | PolicyResource | #1 CRUD resource | tweaks: state-badge-column (draft/published/archived), custom-header-actions (publish modal w/ audience preview + "resets acknowledgements" warning) | {permission} |
| legal.policy-library | PolicyAcknowledgementPage | #18 Heat-map / matrix | - | {permission} |
| legal.policy-library | MyPoliciesPage | #17 Gallery / directory | — list of policies to read + acknowledge *(assumed)* | {permission} |
| lms.certifications | CertificateTemplateResource | #1 CRUD resource | - | lms.certifications.view-any |
| lms.certifications | CertificateResource | #1 (read-only) | - | lms.certifications.view-any |
| lms.courses | CourseResource | #1 CRUD resource | - | lms.courses.view-any |
| lms.courses | CourseBuilderPage | #3-style custom page | - | lms.courses.view-any |
| lms.enrolments | EnrolmentResource | #1 CRUD resource | - | lms.enrolments.view-any |
| lms.learning-paths | LearningPathResource | #1 CRUD resource | - | lms.paths.view-any |
| lms.lessons | Lesson relation manager | #1-adjacent | - | lms.lessons.view-any |
| lms.lessons | Quiz authoring page | #3-style custom page | - | lms.lessons.view-any |
| lms.lms-analytics | LmsDashboardPage | #6 dashboard page + apex charts | - | lms.analytics.view |
| lms.mentoring | MentorshipResource | #1 CRUD resource | - | lms.mentoring.participate |
| lms.mentoring | MentorDirectoryPage | #17 gallery/directory custom page | - | lms.mentoring.participate |
| lms.skills-matrix | SkillResource | #1 CRUD resource | - | lms.skills.view-any |
| lms.skills-matrix | SkillsMatrixPage | #18 heat-map/matrix custom page | - | lms.skills.view-any |
| marketing.campaigns | CampaignResource | #1 CRUD resource | tweaks: state-badge-column, view-page-tabs, custom-header-actions (send / test-send) | marketing.campaigns.view-any |
| marketing.content-cms | PostResource | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (publish / schedule / unpublish) | marketing.cms.view-any |
| marketing.content-cms | PostCategoryResource | #1 CRUD resource | — | marketing.cms.view-any |
| marketing.email-sequences | SequenceResource | #1 CRUD resource | tweaks: inline-relation-repeater (steps), state-badge-column (active/paused), custom-header-actions (pause / resume) | marketing.sequences.view-any |
| marketing.email-sequences | SequenceEnrolmentResource | #1 CRUD resource | tweaks: read-only-flow-owned (rows created by triggers/listener), custom-header-actions (unenrol) | marketing.sequences.view-any |
| marketing.forms | FormResource | #1 CRUD resource | tweaks: inline-relation-repeater (fields), relation-manager-timeline (submissions tab) *(assumed)* | marketing.forms.view-any |
| marketing.forms | FormSubmissionResource | #1 CRUD resource | tweaks: read-only-flow-owned (rows written by the public submit path), custom-header-actions (export) | marketing.forms.view-any |
| marketing.landing-pages | LandingPageResource | #1 CRUD resource | - | marketing.landing-pages.view-any |
| marketing.marketing-analytics | MarketingDashboardPage | #6 dashboard page + apex charts | - | marketing.analytics.view |
| marketing.utm-tracking | UtmBuilderPage | #7 custom page (form) | - | marketing.utm.view |
| marketing.utm-tracking | Attribution tables | rendered inside [[../marketing-analytics/_module\ | - | marketing.utm.view |
| operations.goods-receipt | GoodsReceiptResource | #1 CRUD resource | tweaks: read-only-flow-owned (writes owned by `GrnService` via `ReceiveGoodsPage`), state-badge-column | operations.goods-receipt.view-any |
| operations.goods-receipt | ReceiveGoodsPage | #7 wizard custom page | *(assumed — a create-from-PO receiving grid framed as a single-flow entry page; not a clean multi-step wizard, see QUESTIONS)* | operations.goods-receipt.view-any |
| operations.inventory | ItemResource | #1 CRUD resource | tweaks: view-page-tabs, inline-relation-repeater (per-warehouse levels, read-only display) | operations.inventory.view-any |
| operations.inventory | StockMovementResource | #1 CRUD resource | tweak: read-only-flow-owned (writes owned by `StockService::move`) | operations.inventory.view-any |
| operations.inventory | StockBoardPage | #18 heat-map / matrix custom page | *(assumed — items × warehouses availability matrix; described in  but absent from Build Manifest, see QUESTIONS)* | operations.inventory.view-any |
| operations.operations-reporting | OperationsDashboardPage | #6 dashboard page + apex charts | - | operations.reporting.view |
| operations.purchase-orders | PurchaseOrderResource | #1 CRUD resource | tweaks: inline-relation-repeater (PO lines), state-badge-column, custom-header-actions (send / cancel / create-from-requisition), pdf-preview-panel | operations.purchase-orders.view-any |
| operations.stock-adjustments | StockAdjustmentResource | #1 CRUD resource | tweaks: state-badge-column (pending-approval / applied), custom-header-actions (approve / export) | operations.adjustments.view-any |
| operations.stock-adjustments | StocktakePage | #7 wizard custom page | - | operations.adjustments.view-any |
| operations.suppliers | OpsSupplierResource | #1 CRUD resource | - | operations.suppliers.view-any |
| operations.warehouses | WarehouseResource | #1 CRUD resource | - | operations.warehouses.view-any |
| operations.warehouses | WarehouseTransferResource | #1 CRUD resource | - | operations.warehouses.view-any |
| procurement.approvals | ApprovalRuleResource | #1 CRUD resource | badge-status, guarded-delete | procurement.approvals.view-any |
| procurement.approvals | ApprovalDelegationResource | #1 CRUD resource | date-range filter | procurement.approvals.view-any |
| procurement.approvals | PendingApprovalsPage | #8 inbox custom page () | cross-module queue | procurement.approvals.view-any |
| procurement.goods-receipt | ThreeWayMatchResource | #1 CRUD resource (read-heavy) | badge-status, filter-tabs (matched / flagged / overridden / rejected) | procurement.goods-receipt.view-any |
| procurement.goods-receipt | ThreeWayMatchBoard | #9-style review custom page *(assumed -- two-panel matcher family, see [[../../../../vault/build/gaps/gap-two-panel-matcher-ui-row-missing | gap]])* | procurement.goods-receipt.view-any |
| procurement.purchase-orders | ProcurementPoResource | #1 CRUD resource (layer view) | badge-status, relation-panels | procurement.purchase-orders.view-any |
| procurement.purchase-orders | SourcingBoard | #9-style comparison custom page *(assumed -- quote comparison, two-panel matcher family)* | side-by-side quotes | procurement.purchase-orders.view-any |
| procurement.requisitions | RequisitionResource | #1 CRUD resource | badge-status, wizard-ish create (items repeater), guarded actions (submit / approve / reject / convert) | procurement.requisitions.view-any |
| procurement.spend-analytics | SpendAnalyticsDashboard | #6 dashboard custom page + apex charts | date filter, soft-dep sections | procurement.spend.view |
| procurement.supplier-catalogue | CatalogueItemResource | #1 CRUD resource | badge-status, validity-window filter, guarded-delete | procurement.catalogue.view-any |
| procurement.supplier-catalogue | SupplierStatusResource | #1 CRUD resource | badge-status (approved / pending / blacklisted), audited status actions | procurement.catalogue.view-any |
| projects.gantt | GanttChartPage | #5 Gantt custom page | - | projects.gantt.view |
| projects.kanban | KanbanBoardPage | #3 Kanban custom page | - | projects.kanban.view |
| projects.milestones | MilestoneResource | #1 CRUD resource | tweaks: custom-header-actions (achieve) | projects.milestones.view-any |
| projects.okrs | ObjectiveResource | #1 CRUD resource | tweaks: custom-header-actions (check-in) | projects.okrs.view-any |
| projects.okrs | OkrDashboardPage | #6 Dashboard custom page | - | projects.okrs.view-any |
| projects.projects | ProjectResource | #1 CRUD resource | tweaks: state-badge-column (status machine), view-page-tabs | projects.projects.view-any |
| projects.projects | Project view page | #2 detail with tabs | tweaks: view-page-tabs | projects.projects.view-any |
| projects.resource-allocation | ResourceAllocationResource | #1 CRUD resource | list filters: team, project, date | projects.resources.view-any |
| projects.resource-allocation | AllocationTimelinePage | #5 Gantt / Timeline custom page | - | projects.resources.view-any |
| projects.sprints | SprintResource | #1 CRUD resource | tweaks: state-badge-column (status machine), custom-header-actions (start / complete) | projects.sprints.view-any |
| projects.sprints | SprintBoardPage | #3 Kanban custom page | - | projects.sprints.view-any |
| projects.tasks | TaskResource | #1 CRUD resource | tweaks: state-badge-column, view-page-tabs, relation-manager-timeline | projects.tasks.view-any |
| projects.tasks | MyTasksPage | #17 gallery/directory custom page *(assumed — no dedicated grouped-list kind)* | - | projects.tasks.view-any |
| projects.templates | ProjectTemplateResource | #1 CRUD resource | tweaks: inline-relation-repeater (section/task/milestone repeaters), custom-header-actions (duplicate-to-edit, save-as-template) | projects.templates.view-any |
| projects.templates | CreateProjectFromTemplatePage | #7 Wizard custom page | - | projects.templates.view-any |
| projects.time-tracking | TimeEntryResource | #1 CRUD resource | tweaks: custom-header-actions (approve week) | projects.time.view-any |
| projects.time-tracking | TimesheetPage | #9 report custom page | - | projects.time.view-any |
| projects.time-tracking | ProjectTimeReportPage | #9 report custom page | - | projects.time.view-any |
| projects.time-tracking | Timer control | embedded Livewire component *(not a standalone page — hosted on the projects.tasks task view + projects.kanban card)* | — | projects.time.view-any |
| projects.workload | WorkloadPage | #18 Heat-map / matrix custom page | - | projects.workload.view |
| support.automations | AutomationRuleResource | #1 CRUD resource | tweaks: inline-relation-repeater (conditions + actions), view-page-tabs (logs tab), custom-header-actions (test-run preview *(assumed)*) | support.automations.view-any |
| support.automations | Logs relation manager | #1 relation manager (on rule view) | read-only append-only history | support.automations.view-any |
| support.canned-responses | CannedResponseResource | #1 CRUD resource | (base resource — list tabs Personal/Shared, usage column) | support.canned.view-any |
| support.canned-responses | Composer insert action | embedded action, host  (#8) | (host-owned surface — not a standalone page) | support.canned.view-any |
| support.knowledge-base | KbArticleResource | #1 CRUD resource | tweaks: state-badge-column (draft/published), custom-header-actions (publish / unpublish) | support.kb.view-any |
| support.knowledge-base | KbCategoryResource | #1 CRUD resource | (base resource — tree order, sub-categories) | support.kb.view-any |
| support.live-chat | ChatQueuePage | #8 chat custom page | - | support.chat.view-any |
| support.live-chat | ChatTranscriptResource | #1 CRUD resource | tweaks: read-only-flow-owned (`ChatService` owns writes) | support.chat.view-any |
| support.live-chat | Availability toggle | #10 render hook (panel header) | - | support.chat.view-any |
| support.sla | SlaPolicyResource | #1 CRUD resource | tweaks: inline-relation-repeater (per-priority targets) | support.sla.view |
| support.sla | SlaMonitorPage | #3 custom page | — read-only near-breach queue grouped by urgency (green / amber / red), no drag reorder | support.sla.view |
| support.support-analytics | SupportDashboardPage | #6 dashboard page | - | support.analytics.view |
| support.tickets | TicketResource | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (resolve / assign / merge) | support.tickets.view-any |
| support.tickets | TicketInboxPage | #8 inbox custom page | - | support.tickets.view-any |
| support.tickets | TicketCategoryResource | - | (base resource — no non-CRUD tweaks) | support.tickets.view-any |
| workplace.desk-booking | DeskResource | #1 CRUD resource | — | workplace.desks.view-any |
| workplace.desk-booking | DeskBookingPage | #19 Spatial / floor-map custom page | - | workplace.desks.view-any |
| workplace.maintenance | MaintenanceRequestResource | #1 CRUD resource | tweaks: state-badge-column (status), custom-header-actions (assign / resolve / reopen) | workplace.maintenance.view-any |
| workplace.maintenance | MaintenanceScheduleResource | #1 CRUD resource | tweaks: custom-header-actions (pause/resume *(assumed)*) | workplace.maintenance.view-any |
| workplace.room-booking | RoomResource | #1 CRUD resource | — | workplace.rooms.view-any |
| workplace.room-booking | RoomBookingPage | #4 Calendar custom page | - | workplace.rooms.view-any |
| workplace.visitor-management | VisitorResource | #1 CRUD resource | tweaks: custom-header-actions (pre-register, check-in / check-out) | workplace.visitors.view-any |
| workplace.visitor-management | VisitorKioskPage | custom page (kiosk) *(no exact ui-strategy row — bespoke full-screen check-in page; blueprint gap, see QUESTIONS)* | - | workplace.visitors.view-any |
| workplace.workplace-analytics | WorkplaceDashboardPage | #6 Dashboard | - | workplace.analytics.view-any |

## Backend-only modules (no Filament artifacts)

| Module | Reason |
|---|---|
| core.file-storage | backend module — no standalone resource or page; Media Library file fields live inside every other module's own Filament forms, gated by that owning record's permissions |
| core.i18n | backend module — no standalone resource or page; the locale-selection controls are a tab on `CompanySettingsPage`, owned by [[../company-settings/_module]] |
| core.two-factor-auth | backend / auth module — 2FA is Filament's **built-in** multi-factor feature registered on `AppPanelProvider` and `AdminPanelProvider` via `->multiFactorAuthentication(... |
| foundation.docker-environment | backend module — local-dev Compose stack; the only "UI" is the internal Mailpit inbox and `localhost:8080` serving panels built by other modules |
| foundation.email-setup | backend module — outbound mail + inbound webhook; no panel UI. Suppression state — `users.email_deliverable` — may surface read-only inside user screens owned by other modules |
| foundation.laravel-scaffold | backend module — provides the project skeleton, ULID/soft-delete conventions, and base `companies`/`users`/`admins` migrations; owns no panel UI. Editing those records happens in their owning modules — panel auth, `core.company-settings`, `core.staff-console` |
| foundation.multi-tenancy-layer | backend module — provides the scoping/context substrate every panel and job runs under; owns no panel UI. Its middleware is wired into the panels by [[../filament-panels/_module/filament-panels]] |
| foundation.permissions-seed | backend module — artisan seeders run at install/deploy; no panel UI. The permissions it creates become the assignable set in `core.rbac`'s role builder, but that UI is owned by RBAC, not here |
| foundation.queue-workers | backend module — background processing. Its only dashboard is Laravel Horizon's own `/horizon` UI, gated to the `admin` guard via `HorizonServiceProvider` — an external Laravel surface, not a Filament panel artifact |
| foundation.test-suite | backend module — Pest test suite + CI pipeline; no application UI |

## Related

- [[../architecture/ui-strategy|ui-strategy]] · [[../architecture/domain-panels|domain-panels]] · [[module-graph]]
