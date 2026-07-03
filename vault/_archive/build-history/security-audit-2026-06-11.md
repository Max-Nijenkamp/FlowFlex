---
type: audit
date: 2026-06-11
scope: vault security + UI spec-conformance
status: open
color: "#F97316"
---

# Vault Security & UI Spec-Conformance Audit — 2026-06-11

**173 specs / 31 domains audited.** Findings: **184 HIGH · 85 MEDIUM · 29 LOW**.

> Note: audit is **spec-level only** — no application code exists (wiped on reset to origin/main). These are gaps in the specifications, not verified runtime vulnerabilities. Auth model = Sanctum (not JWT).

# FlowFlex Security & UI Spec-Conformance Audit — Executive Summary

## Overall Posture

The specs are functionally rich but systematically under-specify the security and authorization contract: out of 173 specs audited, nearly every module-gated Filament surface fails to document `canAccess()`, making this the single dominant gap rather than a scattering of one-offs. The failures are concentrated in a few repeatable categories — authorization gating, public/external surface guards, encrypted-field omissions, and webhook verification — meaning a handful of spec-template fixes would close the majority of HIGH findings at once. No fundamentally novel risk classes were found; the issue is consistency and explicitness, not architecture.

## Top Systemic Patterns (fix at the template/standard level, not per-spec)

1. **Missing `canAccess()` on Filament artifacts (SEC-CANACCESS) — pervasive, ~100+ HIGH findings across all 31 domains.** Almost no spec states the required `canAccess() = permission check + BillingService::hasModule()` contract on its resources/pages/widgets. Custom pages (Kanban, dashboards, builders, kiosks) are especially dangerous since Filament does not auto-gate them. This is the #1 systemic failure.

2. **Unauthenticated public/external surfaces with no guard boundary (SEC-EXTERNAL) — recurring HIGH across CRM, ecommerce, marketing, HR, support, LMS, CS.** Public booking, quote-accept, deal-room, referral, NPS/CSAT, form-submit, careers-apply, chat-widget, cart-recovery and email-tracking endpoints describe a token but never declare the guest/non-session guard isolating them from the app/Sanctum session. Signed-URL and single-use token semantics are also frequently unstated.

3. **Sensitive data not in `encrypted-fields` (SEC-ENCRYPT) — HIGH across AI, comms, events, workplace, legal, HR.** External-person PII and secrets are stored plaintext: extracted invoice IBANs/CV data, attendee emails, visitor emails, WhatsApp/SMS `webhook_secret`, offer salary. `encrypted-fields` frontmatter is empty even where the spec body describes regulated PII.

4. **No rate limiter on expensive/abuse-prone surfaces (SEC-RATELIMIT) — MEDIUM, very widespread.** Two sub-classes: (a) inbound webhooks (Stripe, SMS, WhatsApp, email, Resend) and (b) heavy exports / PDF / bulk-import / public token endpoints. Signature verification is often present but edge throttling is consistently absent.

5. **File uploads without whitelist + size + tenant path (SEC-UPLOAD) — MEDIUM, across nearly every domain handling attachments.** The `companies/{id}/` storage path, MIME whitelist, and max size are repeatedly delegated to "core.files" or "security rules" generically instead of being restated as an enforced contract.

6. **Webhook signature verification stated as assumption, not requirement (SEC-WEBHOOK) — HIGH in foundation/email-setup, events/tickets, crm/email.** Inbound surfaces that flip deliverability flags or confirm payments rely on `*(assumed)*` verification with no named middleware or secret source.

7. **UI kinds not in the decision table (UI-ROW) — HIGH in lms (skills-matrix, mentoring), workplace (desk-booking floor map).** Heat-maps, gallery directories and spatial floor-maps are mapped to wrong/non-existent ui-strategy rows and require an ADR before build.

## Most Urgent HIGH Items to Fix First

1. **Codify the `canAccess()` rule in `spec-template.md` + `filament-patterns.md` and backfill it everywhere** — single highest-leverage fix; closes the largest HIGH cluster. Prioritize high-sensitivity surfaces: `hr.payroll`, `hr.compensation`, `core.rbac`, `core.audit-log` (admin `withoutGlobalScope` cross-company view), `core.billing` admin resources, and `it.mdm` (lock/wipe device actions).
2. **Lock down webhook verification from assumption to requirement** — `foundation/email-setup` (Resend bounce flips `email_deliverable`), `events/tickets` and `ecommerce/payments` (Stripe payment confirmation), `crm/email-integration` (OAuth state/PKCE). These are spoofing / payment-confirmation vectors.
3. **Declare guest-guard boundaries + signed/single-use tokens on all public surfaces** — especially payment-triggering ones (`crm.scheduling`, `events.tickets`) and write-creating ones (`marketing.forms`, `hr.recruitment` apply, `ecommerce` cart-recovery/reviews).
4. **Encrypt the named PII/secret columns now** — `ai_extractions.extracted_data`, `ev_registrations.attendee_email`, `wp_visitors.email`, `comms_*_config.webhook_secret`, `hr_offers.salary_cents`.
5. **Resolve the 3 UI-ROW ADRs** (skills-matrix heat-map, mentor directory gallery, desk-booking floor map) before those modules are built.

## UI Theming Risk

Low and uniform: every UI-THEME finding is a LOW-severity missing branding note (artifacts would render stock Filament rather than panel-branded) — a polish/consistency item, not a security or correctness risk.

---

## Findings by Criterion

| Criterion | HIGH | MED | LOW | Total |
|---|---:|---:|---:|---:|
| SEC-CANACCESS | 159 | 2 | 0 | 161 |
| SEC-RATELIMIT | 0 | 50 | 0 | 50 |
| UI-THEME | 0 | 0 | 29 | 29 |
| SEC-UPLOAD | 0 | 24 | 0 | 24 |
| SEC-EXTERNAL | 14 | 0 | 0 | 14 |
| SEC-ENCRYPT | 5 | 2 | 0 | 7 |
| SEC-RICHTEXT | 0 | 4 | 0 | 4 |
| SEC-WEBHOOK | 3 | 0 | 0 | 3 |
| UI-ROW | 3 | 0 | 0 | 3 |
| SEC-AUTHZ | 0 | 1 | 0 | 1 |
| SEC-INPUT | 0 | 1 | 0 | 1 |
| SEC-TENANCY | 0 | 1 | 0 | 1 |

## HIGH Findings by Domain

| Domain | HIGH | All |
|---|---:|---:|
| crm | 21 | 26 |
| hr | 16 | 23 |
| finance | 13 | 19 |
| projects | 11 | 22 |
| communications | 10 | 19 |
| core | 10 | 18 |
| ecommerce | 10 | 13 |
| lms | 10 | 16 |
| events | 9 | 16 |
| marketing | 9 | 13 |
| support | 9 | 10 |
| customer-success | 7 | 14 |
| operations | 7 | 13 |
| workplace | 7 | 13 |
| dms | 6 | 10 |
| it | 6 | 9 |
| legal | 6 | 10 |
| procurement | 6 | 7 |
| ai | 5 | 11 |
| analytics | 5 | 13 |
| foundation | 1 | 3 |

---

## Full Findings

Grouped by domain, HIGH first. `spec` · `criterion` · issue → fix.

### crm  (26)

- **HIGH** `activities.md` · `SEC-CANACCESS`  
  ## Filament section lists ActivityResource, Timeline widget, OverdueTasksWidget but never states canAccess() = permission check + BillingService::hasModule('crm.activities'). Module gating only implied via core.billing dependency.  
  → *Add an explicit canAccess() note to the Filament section: each Resource/Page/Widget must gate on the crm.activities permission AND BillingService::hasModule('crm.activities').*
- **HIGH** `appointment-scheduling.md` · `SEC-CANACCESS`  
  ## Filament lists MeetingTypeResource, BookingResource, AvailabilityPage with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each Filament artifact gating on crm.scheduling permissions AND BillingService::hasModule('crm.scheduling').*
- **HIGH** `appointment-scheduling.md` · `SEC-EXTERNAL`  
  Public booking page (/book/{company-slug}/{meeting-slug}) and the BookSlotData public endpoint are unauthenticated external surfaces but the spec does not specify a Sanctum guard / explicit public (guest) guard handling. Honeypot + rate-limit noted, but the auth boundary is unstated.  
  → *Specify the guard for the public booking surface (guest/no-auth route group, isolated from app session guard) and confirm no app/Sanctum session leakage; document the route group's middleware stack.*
- **HIGH** `contacts.md` · `SEC-CANACCESS`  
  ## Filament lists ContactResource, Contact view page, AccountResource with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each Filament artifact gating on crm.contacts/crm.accounts permissions AND BillingService::hasModule('crm.contacts').*
- **HIGH** `contracts.md` · `SEC-CANACCESS`  
  ## Filament lists ContractResource and ContractRenewalWidget with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on ContractResource/ContractRenewalWidget gating on crm.contracts permissions AND BillingService::hasModule('crm.contracts').*
- **HIGH** `customer-segments.md` · `SEC-CANACCESS`  
  ## Filament lists SegmentResource with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on SegmentResource gating on crm.segments permissions AND BillingService::hasModule('crm.segments').*
- **HIGH** `deal-rooms.md` · `SEC-CANACCESS`  
  ## Filament lists DealRoomResource with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on DealRoomResource gating on crm.deal-rooms permissions AND BillingService::hasModule('crm.deal-rooms').*
- **HIGH** `deal-rooms.md` · `SEC-EXTERNAL`  
  Public deal room (/room/{token}) is an external, token-authed buyer-facing surface but the spec does not specify the Sanctum/guest guard boundary for the public route group — only token validity (unexpired/unrevoked) is described, not the auth guard isolating it from the app session.  
  → *Specify the public route guard (guest/no app-session) and confirm the token resolves the company context without exposing the authenticated app guard; document middleware for /room/{token}.*
- **HIGH** `deals.md` · `SEC-CANACCESS`  
  ## Filament lists DealResource, Deal view page, CreateInvoiceAction, CloseDealAction. CreateInvoiceAction notes hasModule('finance.invoicing') visibility, but the resource itself has no canAccess() statement gating on crm.deals permission + BillingService::hasModule('crm.deals').  
  → *State canAccess() on DealResource/view page gating on crm.deals permissions AND BillingService::hasModule('crm.deals').*
- **HIGH** `email-integration.md` · `SEC-CANACCESS`  
  ## Filament lists EmailConnectionResource, Email thread component, Compose action with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each Filament artifact gating on crm.email permissions AND BillingService::hasModule('crm.email').*
- **HIGH** `email-integration.md` · `SEC-WEBHOOK`  
  Spec describes 'provider webhooks v1.x' for inbound sync and OAuth callback (EmailOAuthController) but specifies no signature verification (Google/Microsoft webhook validation or OAuth state/PKCE) for these inbound external surfaces.  
  → *Require signature/state verification on the OAuth callback (state + PKCE) and on any provider push webhook (validate provider signature) before processing; document in the controllers section.*
- **HIGH** `email-integration.md` · `SEC-EXTERNAL`  
  TrackOpenController / TrackClickController are public unauthenticated endpoints (open pixel, click redirect) with a per-email token but no Sanctum/guest guard boundary specified.  
  → *Specify these run on a guest (no app-session) route group; validate the per-email token signature and isolate from authenticated guards.*
- **HIGH** `forecasting.md` · `SEC-CANACCESS`  
  ## Filament lists QuotaResource, ForecastPage, ForecastWidget with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each Filament artifact gating on crm.forecasting permissions AND BillingService::hasModule('crm.forecasting'); note view-own/view-team scoping in canAccess/query.*
- **HIGH** `pipeline.md` · `SEC-CANACCESS`  
  ## Filament lists PipelineBoardPage (custom Kanban page) and PipelineStageResource with no canAccess() statement (permission + BillingService::hasModule). Custom pages especially require explicit canAccess().  
  → *State canAccess() on PipelineBoardPage and PipelineStageResource gating on crm.pipeline permissions AND BillingService::hasModule('crm.pipeline').*
- **HIGH** `price-management.md` · `SEC-CANACCESS`  
  ## Filament lists ProductResource, PriceBookResource, VolumeDiscountResource with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each pricing Resource gating on crm.pricing permissions AND BillingService::hasModule('crm.pricing').*
- **HIGH** `quotes.md` · `SEC-CANACCESS`  
  ## Filament lists QuoteResource and Quote view page with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on QuoteResource/view page gating on crm.quotes permissions AND BillingService::hasModule('crm.quotes').*
- **HIGH** `quotes.md` · `SEC-EXTERNAL`  
  Public accept/decline page (/quotes/{token}) is an unauthenticated external surface (accept()/decline() via token) but no Sanctum/guest guard boundary is specified — only 'rate-limited' and token scoping.  
  → *Specify the public quote route runs on a guest (no app-session) guard, validate the signed accept_token, and isolate from authenticated guards.*
- **HIGH** `referral-program.md` · `SEC-CANACCESS`  
  ## Filament lists ReferralProgramResource, ReferralResource, ReferralLeaderboardPage with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each referral Filament artifact gating on crm.referrals permissions AND BillingService::hasModule('crm.referrals').*
- **HIGH** `referral-program.md` · `SEC-EXTERNAL`  
  Referral registration via referral link/code (RegisterReferralData captures referee_email from a public link click/signup) is an external unauthenticated entry point but no public route guard, Sanctum boundary, or rate limiter is specified.  
  → *Specify the public referral-capture route's guest guard and a named rate limiter; document the unauthenticated entry surface explicitly (currently absent from the spec).*
- **HIGH** `revenue-intelligence.md` · `SEC-CANACCESS`  
  ## Filament lists DealHealthResource, WinLossPage, RevenueIntelligenceDashboard (custom pages) with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each artifact gating on crm.revenue-intelligence.view AND BillingService::hasModule('crm.revenue-intelligence').*
- **HIGH** `sales-sequences.md` · `SEC-CANACCESS`  
  ## Filament lists SequenceResource, SequenceEnrolmentResource, Enrol action with no canAccess() statement (permission + BillingService::hasModule).  
  → *State canAccess() on each sequence Filament artifact gating on crm.sequences permissions AND BillingService::hasModule('crm.sequences').*
- **MEDIUM** `appointment-scheduling.md` · `SEC-RATELIMIT`  
  Stripe PaymentIntent creation on paid bookings is an expensive/sensitive external call triggered from the public book() path; only generic 'public endpoints rate-limited' is noted without citing a named limiter for the booking POST.  
  → *Cite a specific named rate limiter for the public booking POST (e.g. RateLimiter::for('public-booking')) covering slot lookup and PaymentIntent creation.*
- **MEDIUM** `contacts.md` · `SEC-RATELIMIT`  
  Contact CSV import and Excel export are bulk operations with no rate limiter cited; consumed events FormSubmissionReceived/EventRegistrationReceived create contacts via public-origin data without an abuse limiter noted.  
  → *Cite a named rate limiter for the import upload and export actions, and note throttling/dedupe on contact-creating event listeners.*
- **MEDIUM** `contracts.md` · `SEC-UPLOAD`  
  Signed-PDF upload (markSigned, ContractResource signed-PDF upload action) does not specify a MIME/type whitelist (application/pdf), max file size, or the companies/{id}/ storage path.  
  → *In the contract record / markSigned section note: accept application/pdf only, max size cap, stored under companies/{company_id}/contracts/ via Media Library.*
- **MEDIUM** `email-integration.md` · `SEC-RATELIMIT`  
  Public TrackOpen/TrackClick endpoints (high-volume, unauthenticated) cite no rate limiter; open-redirect/click endpoint can be abused.  
  → *Cite a named rate limiter for the tracking pixel and click-redirect endpoints, and constrain click redirect to validated stored URLs only.*
- **MEDIUM** `sales-sequences.md` · `SEC-RICHTEXT`  
  Email-step templates (SequenceStepMail, config holds template HTML/merge fields) send rich HTML to prospects but the spec does not note HTMLPurifier sanitization of template body before storage/send.  
  → *Note HTMLPurifier sanitization on sequence email-step template HTML on save (consistent with crm.email body purification).*

### hr  (23)

- **HIGH** `compensation-benefits.md` · `SEC-CANACCESS`  
  ## Filament lists CompensationBandResource, BenefitResource, BenefitEnrollmentResource and SalaryHistoryRelationManager with no canAccess() = permission + BillingService::hasModule('hr.compensation') statement.  
  → *Add explicit canAccess() (permission + hasModule) note for each resource; confirm SalaryHistoryRelationManager view gated behind hr.payroll.view-sensitive.*
- **HIGH** `dei-metrics.md` · `SEC-CANACCESS`  
  ## Filament lists DeiDashboardPage and the DEI section in MyProfilePage with no canAccess() = permission + BillingService::hasModule('hr.dei') statement (dashboard handles privacy-sensitive aggregates).  
  → *Add canAccess() = hr.dei.view-dashboard + hasModule('hr.dei') for DeiDashboardPage, and hr.dei.submit-own for the self-service section.*
- **HIGH** `employee-feedback.md` · `SEC-CANACCESS`  
  ## Filament lists FeedbackResource, OneOnOneResource, RecognitionFeedPage with no canAccess() = permission + BillingService::hasModule('hr.feedback') statement.  
  → *Add explicit canAccess() (permission + hasModule) note per artifact; visibility query scoping is already documented separately.*
- **HIGH** `employee-profiles.md` · `SEC-CANACCESS`  
  ## Filament section lists EmployeeResource, Employee view page, DepartmentResource, OffboardAction, EmployeeProfileWidget but never states canAccess() = permission + BillingService::hasModule('hr.profiles'). Module gating appears only as a test-checklist line, not as an authorization rule on the resources/page/widget.  
  → *Add to ## Filament: 'canAccess() on every resource/page/widget = hasPermission(hr.employees.*) && BillingService::hasModule(hr.profiles)'. State the gate explicitly per artifact.*
- **HIGH** `employee-self-service.md` · `SEC-CANACCESS`  
  ## Filament lists SelfServiceDashboardPage, MyProfilePage, MyDocumentsPage with no canAccess() = permission(hr.self-service.view) + BillingService::hasModule statement. Soft-dep tiles note hasModule, but the pages themselves never state the canAccess gate.  
  → *Add canAccess() = hr.self-service.view (+ hasModule('hr.profiles') for the parent module) note on each page; the own-data scoping rule is already documented.*
- **HIGH** `hr-analytics.md` · `SEC-CANACCESS`  
  ## Filament lists HrAnalyticsDashboard page + apex-chart widgets with no canAccess() = permission(hr.analytics.view) + BillingService::hasModule('hr.analytics') statement.  
  → *Add explicit canAccess() (hr.analytics.view + hasModule) note in the Filament section.*
- **HIGH** `leave-management.md` · `SEC-CANACCESS`  
  ## Filament lists LeaveRequestResource, LeaveBalanceResource, LeaveTypeResource, LeaveCalendarPage, PendingApprovalsWidget with no canAccess() statement tying access to permission + BillingService::hasModule('hr.leave'). Gating mentioned only in test checklist.  
  → *Add explicit canAccess() = permission + hasModule note in the Filament section for all five artifacts.*
- **HIGH** `onboarding.md` · `SEC-CANACCESS`  
  ## Filament lists OnboardingResource, OnboardingTemplateResource, ActiveOnboardingsWidget with no canAccess() = permission + BillingService::hasModule('hr.onboarding') statement (only a test-checklist line references gating).  
  → *Add explicit canAccess() (permission + hasModule) note per artifact in the Filament section.*
- **HIGH** `org-chart.md` · `SEC-CANACCESS`  
  ## Filament lists OrgChartPage (#11 tree-view) with no canAccess() = permission(hr.org.view) + BillingService::hasModule('hr.org') statement.  
  → *Add explicit canAccess() (hr.org.view + hasModule) note in the Filament section.*
- **HIGH** `payroll.md` · `SEC-CANACCESS`  
  ## Filament lists PayrollRunResource, PayslipResource, PayrollEmployeeResource, DeductionTypeResource, PayrollRunWidget (highly sensitive financial/PII data) with no canAccess() = permission + BillingService::hasModule('hr.payroll') statement.  
  → *State canAccess() per artifact combining hr.payroll.* permission + hasModule, and note view-sensitive gating for salary/IBAN display columns.*
- **HIGH** `performance-reviews.md` · `SEC-CANACCESS`  
  ## Filament lists ReviewCycleResource, ReviewResource, MyGoalsPage with no canAccess() = permission + BillingService::hasModule('hr.performance') statement.  
  → *Add explicit canAccess() (permission + hasModule) note per artifact.*
- **HIGH** `recruitment.md` · `SEC-CANACCESS`  
  ## Filament lists JobRequisitionResource, ApplicantPipelinePage, ApplicantResource, InterviewResource, OfferResource with no canAccess() = permission + BillingService::hasModule('hr.recruitment') statement.  
  → *Add explicit canAccess() (permission + hasModule) note per Filament artifact (the public Vue careers pages are separate and correctly excluded).*
- **HIGH** `recruitment.md` · `SEC-EXTERNAL`  
  Public application surface (apply() service path + /careers, /careers/{slug} Vue pages + public ApplyData form) accepts unauthenticated writes that create applicants/CV uploads, but the spec does not specify the auth/guard model for the public endpoint (no Sanctum/guest guard, company resolved only from slug). Public unauthenticated write surface with no guard specification.  
  → *Specify the public submission goes through a guest/unauthenticated controller (no Sanctum session), with company resolved+validated from requisition slug, plus explicit input validation and abuse controls; document the guard boundary in the spec.*
- **HIGH** `shift-scheduling.md` · `SEC-CANACCESS`  
  ## Filament lists ShiftSchedulePage and ShiftSwapRequestResource with no canAccess() = permission + BillingService::hasModule('hr.shifts') statement.  
  → *Add explicit canAccess() (permission + hasModule) note for the page and resource.*
- **HIGH** `time-attendance.md` · `SEC-CANACCESS`  
  ## Filament lists TimesheetResource, TimeEntryResource and the self-service Clock widget with no canAccess() = permission + BillingService::hasModule('hr.time') statement.  
  → *Add explicit canAccess() (permission + hasModule) note per artifact.*
- **HIGH** `workforce-planning.md` · `SEC-CANACCESS`  
  ## Filament lists HeadcountPlanResource, PlannedRoleResource, WorkforcePlanningDashboard with no canAccess() = permission + BillingService::hasModule('hr.workforce') statement.  
  → *Add explicit canAccess() (permission + hasModule) note per artifact.*
- **MEDIUM** `employee-profiles.md` · `SEC-RATELIMIT`  
  EmployeeResource offers 'export via pxlrbt/filament-excel' (expensive/bulk export of PII incl. potentially large datasets) but no rate limiter is cited.  
  → *Cite a throttle (e.g. RateLimiter 'hr-export' per-user/company) on the export action per architecture/security.md.*
- **MEDIUM** `hr-analytics.md` · `SEC-RATELIMIT`  
  Core feature 'Export all charts as PNG or data as CSV' is an expensive export with no rate limiter cited.  
  → *Cite a named throttle on the CSV/PNG export action per architecture/security.md.*
- **MEDIUM** `payroll.md` · `SEC-RATELIMIT`  
  Payslip PDF download (PayslipResource 'PDF download') and self-service payslip access stream sensitive financial PDFs but no rate limiter is cited on the download/export path.  
  → *Cite a named throttle on payslip PDF download / export action per architecture/security.md.*
- **MEDIUM** `recruitment.md` · `SEC-RATELIMIT`  
  Public apply route (ApplyData) only says 'rate-limited + honeypot *(assumed)*' — no named rate limiter is cited for an unauthenticated public POST endpoint that uploads files. PurgeStaleApplicantsCommand fine; the public POST is the risk.  
  → *Cite a concrete named RateLimiter (e.g. 'public-apply' per IP + per requisition) per architecture/security.md and remove the *(assumed)* marker.*
- **MEDIUM** `recruitment.md` · `SEC-UPLOAD`  
  CV upload specifies type whitelist (pdf/docx) and max size (10MB) but does not specify the tenant-scoped storage path companies/{id}/ for cv_path (cv_path only noted 'via core.files').  
  → *State CV files store under companies/{company_id}/recruitment/ (private disk) with the type+size rules, matching the file-storage tenant path convention.*
- **MEDIUM** `recruitment.md` · `SEC-ENCRYPT`  
  hr_offers stores salary_cents (compensation/pay data) in plaintext bigint and frontmatter encrypted-fields is empty. Across HR, salary is treated as sensitive and encrypted (payroll/compensation use salary_raw encrypted). Offer salary is the same sensitivity class but unencrypted here.  
  → *Either encrypt hr_offers.salary_cents (add to encrypted-fields as a *_raw text column) consistent with payroll/compensation, or document an explicit ADR justifying plaintext offer salary.*
- **MEDIUM** `workforce-planning.md` · `SEC-AUTHZ`  
  ## Permissions lists view-any, create, update, approve-role but omits a view (single-record) and delete permission present in sibling HR modules; MarkRoleFilledAction has no clearly mapped permission (only approve-role is listed).  
  → *Add the missing granular permissions (e.g. hr.workforce.view, hr.workforce.delete) and map MarkRoleFilledAction to an explicit permission.*

### finance  (19)

- **HIGH** `accounts-payable.md` · `SEC-CANACCESS`  
  ## Filament lists SupplierResource, BillResource, ApAgingPage, PaymentRunPage with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.ap.* permission AND BillingService::hasModule('finance.ap')) to each Filament artifact.*
- **HIGH** `accounts-receivable.md` · `SEC-CANACCESS`  
  ## Filament lists ArAgingPage, CustomerStatementPage, DunningRuleResource with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.ar.* permission AND BillingService::hasModule('finance.ar')) to each Filament artifact.*
- **HIGH** `bank-accounts.md` · `SEC-CANACCESS`  
  ## Filament lists BankAccountResource, BankTransactionResource with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.bank.* permission AND BillingService::hasModule('finance.bank')) to each resource.*
- **HIGH** `budgets.md` · `SEC-CANACCESS`  
  ## Filament lists BudgetResource, BudgetVariancePage, BudgetVarianceWidget with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.budgets.* permission AND BillingService::hasModule('finance.budgets')) to each artifact.*
- **HIGH** `cash-flow.md` · `SEC-CANACCESS`  
  ## Filament lists CashFlowPage and low-cash alert widget with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.cashflow.* permission AND BillingService::hasModule('finance.cashflow')) to the page and widget.*
- **HIGH** `expenses.md` · `SEC-CANACCESS`  
  ## Filament lists ExpenseResource, ExpenseReportResource, ExpenseCategoryResource with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.expenses.* permission AND BillingService::hasModule('finance.expenses')) to each resource.*
- **HIGH** `financial-reporting.md` · `SEC-CANACCESS`  
  ## Filament lists ProfitLossPage, BalanceSheetPage, CashFlowStatementPage with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.reporting.* permission AND BillingService::hasModule('finance.reporting')) to each report page.*
- **HIGH** `fixed-assets.md` · `SEC-CANACCESS`  
  ## Filament lists FixedAssetResource, DepreciationRunPage with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.assets.* permission AND BillingService::hasModule('finance.assets')) to each artifact.*
- **HIGH** `forecasting.md` · `SEC-CANACCESS`  
  ## Filament lists ForecastResource, ForecastComparisonPage with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.forecasting.* permission AND BillingService::hasModule('finance.forecasting')) to each artifact.*
- **HIGH** `general-ledger.md` · `SEC-CANACCESS`  
  ## Filament section lists 4 artifacts (ChartOfAccountsResource, JournalEntryResource, FiscalPeriodResource, TrialBalancePage) but does not state canAccess() = permission + BillingService::hasModule('finance.ledger'). No module-gating/authorization gate documented on the panel surface.  
  → *Add a Filament authorization note: every resource/page implements canAccess() returning auth user has the finance.ledger permission AND BillingService::hasModule('finance.ledger'). Reference architecture/filament-patterns.md.*
- **HIGH** `invoicing.md` · `SEC-CANACCESS`  
  ## Filament lists InvoiceResource, invoice view page, CustomerResource, InvoiceStatsWidget with no canAccess() statement combining permission check + BillingService::hasModule().  
  → *State canAccess() = finance.invoicing.* permission AND BillingService::hasModule('finance.invoicing') on each resource/page/widget.*
- **HIGH** `multi-currency.md` · `SEC-CANACCESS`  
  ## Filament lists CurrencyResource, ExchangeRateResource, FX gain/loss report page with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.currency.* permission AND BillingService::hasModule('finance.currency')) to each artifact.*
- **HIGH** `tax-management.md` · `SEC-CANACCESS`  
  ## Filament lists TaxRateResource, TaxReturnPage, VAT-validate action with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() gate (finance.tax.* permission AND BillingService::hasModule('finance.tax')) to each artifact.*
- **MEDIUM** `accounts-payable.md` · `SEC-UPLOAD`  
  CreateBillData accepts an attachment (pdf) for bill documents but the spec does not state a max file size or a companies/{id}/ tenant-scoped storage path.  
  → *Specify pdf MIME whitelist, max size, and companies/{company_id}/ap-bills/ storage path for bill attachments.*
- **MEDIUM** `bank-accounts.md` · `SEC-UPLOAD`  
  Bank statement CSV upload (ImportStatementData: file csv, max 10MB) specifies a max size but no explicit MIME/type whitelist enforcement note beyond 'csv' and no companies/{id}/ tenant-scoped storage path for the uploaded file.  
  → *Document MIME whitelist (text/csv) + the companies/{company_id}/bank-imports/ storage path so uploaded statements are tenant-isolated; reference Security upload rules in architecture/security.md.*
- **MEDIUM** `bank-accounts.md` · `SEC-RATELIMIT`  
  Bulk bank-statement import (chunked job over a 10MB upload) is an expensive operation with no rate limiter cited on the upload/import action.  
  → *Cite a rate limiter on the import action (e.g. N imports per company per minute) in addition to the queued chunked job.*
- **MEDIUM** `expenses.md` · `SEC-UPLOAD`  
  Expense receipt upload defines MIME whitelist (pdf,jpg,png,webp) and 'max per settings' but does not document a companies/{id}/ tenant-scoped storage path for receipts.  
  → *State that receipts store under companies/{company_id}/expense-receipts/ (Media Library tenant-scoped collection) and pin a concrete max size default.*
- **MEDIUM** `financial-reporting.md` · `SEC-RATELIMIT`  
  P&L / balance sheet / cash flow Excel + PDF exports (Chromium-rendered, ledger-wide) are expensive with no rate limiter cited.  
  → *Add a rate limiter on the report export actions to prevent export abuse / resource exhaustion.*
- **MEDIUM** `invoicing.md` · `SEC-RATELIMIT`  
  Invoice list export via pxlrbt/filament-excel and on-demand PDF generation are expensive operations with no rate limiter cited.  
  → *Cite a per-user/per-company rate limiter (RateLimiter::for) on the export action and PDF-generation endpoint per architecture/api-design.md / security.md.*

### projects  (22)

- **HIGH** `gantt.md` · `SEC-CANACCESS`  
  ## Filament defines a GanttChartPage custom page but the section never states canAccess() = permission (projects.gantt.view) + BillingService::hasModule().  
  → *Add a canAccess() note to the Filament section: gate the page on projects.gantt.view AND BillingService::hasModule('projects.gantt').*
- **HIGH** `kanban.md` · `SEC-CANACCESS`  
  ## Filament defines KanbanBoardPage but does not state canAccess() = permission (projects.kanban.view) + BillingService::hasModule().  
  → *Add canAccess() note gating the page on projects.kanban.view AND BillingService::hasModule('projects.kanban').*
- **HIGH** `milestones.md` · `SEC-CANACCESS`  
  ## Filament lists MilestoneResource + timeline widget but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note: gate resource/widget on projects.milestones.* AND BillingService::hasModule('projects.milestones').*
- **HIGH** `okrs.md` · `SEC-CANACCESS`  
  ## Filament lists ObjectiveResource + OkrDashboardPage but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note gating both artifacts on projects.okrs.* AND BillingService::hasModule('projects.okrs').*
- **HIGH** `projects.md` · `SEC-CANACCESS`  
  ## Filament lists ProjectResource, project view page, and ProjectStatsWidget but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note gating artifacts on projects.projects.* AND BillingService::hasModule('projects.projects').*
- **HIGH** `resource-allocation.md` · `SEC-CANACCESS`  
  ## Filament lists ResourceAllocationResource + AllocationTimelinePage but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note gating on projects.resources.* AND BillingService::hasModule('projects.resources').*
- **HIGH** `sprints.md` · `SEC-CANACCESS`  
  ## Filament lists SprintResource, SprintBoardPage, BurndownChartWidget but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note gating artifacts on projects.sprints.* AND BillingService::hasModule('projects.sprints').*
- **HIGH** `tasks.md` · `SEC-CANACCESS`  
  ## Filament lists TaskResource, task view, MyTasksPage but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note gating artifacts on projects.tasks.* AND BillingService::hasModule('projects.tasks').*
- **HIGH** `templates.md` · `SEC-CANACCESS`  
  ## Filament lists ProjectTemplateResource + CreateProjectFromTemplatePage but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note gating artifacts on projects.templates.* AND BillingService::hasModule('projects.templates').*
- **HIGH** `time-tracking.md` · `SEC-CANACCESS`  
  ## Filament lists TimeEntryResource, TimesheetPage, ProjectTimeReportPage, timer widget but no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add canAccess() note gating artifacts on projects.time.* AND BillingService::hasModule('projects.time').*
- **HIGH** `workload.md` · `SEC-CANACCESS`  
  ## Filament defines WorkloadPage custom page but does not state canAccess() = permission (projects.workload.view) + BillingService::hasModule().  
  → *Add canAccess() note gating the page on projects.workload.view AND BillingService::hasModule('projects.workload').*
- **MEDIUM** `tasks.md` · `SEC-RICHTEXT`  
  Task comments store purified rich text ('plain text + purified rich text') but the spec does not explicitly cite HTMLPurifier (ezyang/htmlpurifier) as the sanitizer for the comment body, leaving sanitization unspecified if rich text is enabled.  
  → *In Core Features/CommentData, state that rich-text comment bodies are sanitized with HTMLPurifier before persistence.*
- **MEDIUM** `tasks.md` · `SEC-UPLOAD`  
  Tasks support attachments via Media Library but the spec gives no file type whitelist, max size, or companies/{id}/ storage path constraints.  
  → *Add an upload constraints note: allowed MIME/type whitelist, max file size, and tenant-scoped companies/{id}/ storage path.*
- **MEDIUM** `templates.md` · `SEC-TENANCY`  
  System templates are stored with company_id null and described as a 'global read scope exception' readable by all companies; spec does not specify how the global-read bypass is constrained (read-only, never writable cross-tenant) at the query/scope level.  
  → *Document the exact scope override: a read-only global scope exception that surfaces is_system/company_id-null rows to all tenants while blocking any cross-tenant write/edit; reference multi-tenancy.md.*
- **MEDIUM** `time-tracking.md` · `SEC-RATELIMIT`  
  CSV export of time entries (ProjectTimeReportPage export, projects.time.export) is an expensive/sensitive operation with no rate limiter cited.  
  → *Add a rate-limit note for the CSV export endpoint (per-user/company throttle).*
- **LOW** `gantt.md` · `UI-THEME`  
  GanttChartPage is a user-facing custom page (frappe-gantt in Blade) with no branding/theme note; would render as stock Filament.  
  → *Add a note confirming the Projects panel theme/branding is applied to the Gantt page.*
- **LOW** `kanban.md` · `UI-THEME`  
  KanbanBoardPage user-facing custom page (Livewire + Alpine SortableJS) has no branding/theme note.  
  → *Add a note confirming Projects panel theme/branding applied to the Kanban board.*
- **LOW** `okrs.md` · `UI-THEME`  
  OkrDashboardPage user-facing custom page has no branding/theme note.  
  → *Add a note confirming Projects panel theme/branding applied to the OKR dashboard.*
- **LOW** `resource-allocation.md` · `UI-THEME`  
  AllocationTimelinePage custom timeline page has no branding/theme note.  
  → *Add a note confirming Projects panel theme/branding applied to the allocation timeline page.*
- **LOW** `sprints.md` · `UI-THEME`  
  SprintBoardPage user-facing Kanban custom page has no branding/theme note.  
  → *Add a note confirming Projects panel theme/branding applied to the sprint board.*
- **LOW** `time-tracking.md` · `UI-THEME`  
  TimesheetPage and ProjectTimeReportPage are user-facing report custom pages with no branding/theme note.  
  → *Add a note confirming Projects panel theme/branding applied to the timesheet/report pages.*
- **LOW** `workload.md` · `UI-THEME`  
  WorkloadPage user-facing heat-map custom page (Livewire + Alpine) has no branding/theme note.  
  → *Add a note confirming Projects panel theme/branding applied to the workload page.*

### communications  (19)

- **HIGH** `automations.md` · `SEC-CANACCESS`  
  ## Filament defines CommsAutomationRuleResource (#1) and ChatbotFlowResource (#1) but no canAccess() = permission + BillingService::hasModule() is stated.  
  → *Add canAccess() gating on comms.automations.manage permission AND BillingService::hasModule('comms.automations').*
- **HIGH** `broadcast.md` · `SEC-CANACCESS`  
  ## Filament defines BroadcastResource (#1) and BroadcastStatsWidget (#6) but no canAccess() = permission + BillingService::hasModule() is stated.  
  → *Add canAccess() gating on comms.broadcast.* permission AND BillingService::hasModule('comms.broadcast').*
- **HIGH** `comms-analytics.md` · `SEC-CANACCESS`  
  ## Filament defines CommsAnalyticsDashboard (#6 page + charts) but no canAccess() = permission + BillingService::hasModule() is stated.  
  → *Add canAccess() gating on comms.analytics.view permission AND BillingService::hasModule('comms.analytics').*
- **HIGH** `email-channel.md` · `SEC-CANACCESS`  
  ## Filament defines EmailChannelResource (#1) but no canAccess() = permission + BillingService::hasModule() is stated.  
  → *Add canAccess() gating on comms.email.manage permission AND BillingService::hasModule('comms.email').*
- **HIGH** `internal-messaging.md` · `SEC-CANACCESS`  
  ## Filament defines InternalMessagingPage (#8) but no canAccess() = permission + BillingService::hasModule() is stated.  
  → *Add canAccess() gating on comms.internal.use permission AND BillingService::hasModule('comms.internal').*
- **HIGH** `shared-inbox.md` · `SEC-CANACCESS`  
  ## Filament defines SharedInboxPage (#8) and ChannelResource (#1) but the spec never states canAccess() must gate on permission + BillingService::hasModule(). No canAccess() mention anywhere.  
  → *Add a canAccess() note to the Filament section: each resource/page returns auth()->user()->can('comms.inbox.<action>') && BillingService::hasModule('comms.inbox').*
- **HIGH** `sms-channel.md` · `SEC-CANACCESS`  
  ## Filament defines SmsChannelResource (#1) and an opt-out list page but no canAccess() = permission + BillingService::hasModule() is stated.  
  → *Add canAccess() gating on comms.sms.manage permission AND BillingService::hasModule('comms.sms').*
- **HIGH** `sms-channel.md` · `SEC-ENCRYPT`  
  comms_sms_config.webhook_secret is a callback-verification secret but is not listed in encrypted-fields (only api_key and api_secret are).  
  → *Add comms_sms_config.webhook_secret to frontmatter encrypted-fields and mark the column 🔐 encrypted.*
- **HIGH** `whatsapp.md` · `SEC-CANACCESS`  
  ## Filament defines WhatsAppTemplateResource (#1) and WhatsAppConfigPage (#7) but no canAccess() = permission + BillingService::hasModule() is stated.  
  → *Add canAccess() to both artifacts gating on comms.whatsapp.* permission AND BillingService::hasModule('comms.whatsapp').*
- **HIGH** `whatsapp.md` · `SEC-ENCRYPT`  
  comms_whatsapp_config.webhook_secret holds a provider verify token / secret but is not listed in encrypted-fields (only api_key is).  
  → *Add comms_whatsapp_config.webhook_secret to frontmatter encrypted-fields and mark the column 🔐 encrypted text.*
- **MEDIUM** `broadcast.md` · `SEC-RATELIMIT`  
  Delivery/open callbacks update recipient status via channel webhooks but no rate limiter is cited on those inbound callback surfaces. (Outbound send rate-limiting per channel is noted.)  
  → *Cite a rate limiter on the delivery/open webhook callbacks; outbound batch send throttling is already noted.*
- **MEDIUM** `email-channel.md` · `SEC-UPLOAD`  
  Inbound and outbound email attachments are handled (stored via core.files) but no file type whitelist, max size, or companies/{id}/ path is specified.  
  → *Specify MIME/extension whitelist, max size, and tenant-scoped companies/{id}/ path for email attachments.*
- **MEDIUM** `email-channel.md` · `SEC-RATELIMIT`  
  InboundEmailWebhookController parses inbound provider webhooks with no rate limiter cited.  
  → *Cite a throttle / rate limiter on the inbound email webhook route.*
- **MEDIUM** `internal-messaging.md` · `SEC-UPLOAD`  
  File attachments via Media Library are supported but no type whitelist, max size, or companies/{id}/ path is specified.  
  → *Specify MIME/extension whitelist, max size, and tenant-scoped storage path for chat attachments.*
- **MEDIUM** `shared-inbox.md` · `SEC-UPLOAD`  
  SendMessageData carries attachments[] and inbound messages store attachments, but no file type whitelist, max size, or companies/{id}/ storage path is specified.  
  → *Specify allowed MIME/extension whitelist, max upload size, and tenant-scoped path companies/{company_id}/comms/... for message attachments.*
- **MEDIUM** `shared-inbox.md` · `SEC-RATELIMIT`  
  ProcessInboundMessageJob is driven per webhook but no rate limiter / throttling is cited on the inbound webhook entry points feeding the inbox.  
  → *Cite a rate limiter (e.g. throttle middleware on the channel webhook controllers) to protect the inbound pipeline from flooding.*
- **MEDIUM** `sms-channel.md` · `SEC-RATELIMIT`  
  SmsWebhookController handles inbound + status callbacks with no rate limiter cited.  
  → *Cite a throttle / rate limiter on the SMS webhook route.*
- **MEDIUM** `whatsapp.md` · `SEC-UPLOAD`  
  Media messages (images, documents) are sent/received via core.files but no type whitelist, max size, or companies/{id}/ path is specified.  
  → *Specify MIME/extension whitelist, max size, and tenant-scoped storage path for WhatsApp media attachments.*
- **MEDIUM** `whatsapp.md` · `SEC-RATELIMIT`  
  WhatsAppWebhookController processes inbound provider callbacks with no rate limiter cited.  
  → *Cite a throttle / rate limiter on the WhatsApp webhook route.*

### core  (18)

- **HIGH** `api-clients.md` · `SEC-CANACCESS`  
  The ## Filament section defines ApiClientResource but never states canAccess(). The module is module-gated (depends-on core.billing) so canAccess() must combine the core.api permission with BillingService::hasModule('core.api'). No canAccess contract is documented.  
  → *Add a canAccess() note to the Filament section: ApiClientResource::canAccess() = auth user has core.api.view-any AND BillingService::hasModule('core.api').*
- **HIGH** `audit-log.md` · `SEC-CANACCESS`  
  AuditLogResource (and the admin cross-company view) are declared in the ## Filament section with no canAccess() statement. The admin view explicitly uses withoutGlobalScope, making the access gate critical to document.  
  → *State canAccess() for both surfaces: tenant AuditLogResource gated by core.audit.view-any; admin cross-company view gated by the FlowFlex staff admin guard only (no tenant access).*
- **HIGH** `billing-engine.md` · `SEC-CANACCESS`  
  The ## Filament section lists BillingResource, BillingWidget (/app) and BillingOverviewResource, ModulePricingResource (/admin) but states no canAccess() for any of them. Admin resources especially need an explicit admin-guard gate.  
  → *Document canAccess() per artifact: /app BillingResource/Widget gated by core.billing.view; /admin BillingOverviewResource + ModulePricingResource gated by admin (FlowFlex staff) guard only.*
- **HIGH** `company-settings.md` · `SEC-CANACCESS`  
  CompanySettingsPage is declared in the ## Filament section with no canAccess() statement. The Permissions section says owner+admin only, but the canAccess() gate is not documented in the Filament section.  
  → *Add canAccess() to the Filament section: CompanySettingsPage::canAccess() = auth user has core.settings.view (always-free core module, so no hasModule gate needed).*
- **HIGH** `data-import.md` · `SEC-CANACCESS`  
  DataImportResource is declared in the ## Filament section with no canAccess() statement. The module is module-gated (depends-on core.billing), so the gate must combine permission + hasModule.  
  → *Add canAccess() to the Filament section: DataImportResource::canAccess() = core.import.view-any AND BillingService::hasModule('core.import').*
- **HIGH** `data-privacy.md` · `SEC-CANACCESS`  
  DsarRequestResource and DataExportPage are declared in the ## Filament section with no canAccess() statement. The module is module-gated (depends-on core.billing), so the gate must combine permission + hasModule.  
  → *Add canAccess() per artifact: DsarRequestResource gated by core.privacy.view-any AND BillingService::hasModule('core.privacy'); DataExportPage gated by core.privacy.export AND hasModule.*
- **HIGH** `invitation-system.md` · `SEC-CANACCESS`  
  InvitationResource is declared in the ## Filament section with no canAccess() statement.  
  → *Add canAccess() to the Filament section: InvitationResource::canAccess() = core.invitations.view-any (always-free core module, no hasModule gate needed).*
- **HIGH** `module-marketplace.md` · `SEC-CANACCESS`  
  ModuleMarketplacePage is declared in the ## Filament section with no canAccess() statement.  
  → *Add canAccess() to the Filament section: ModuleMarketplacePage::canAccess() = core.marketplace.view (always-free core; activation buttons additionally gated by core.billing.activate-module/deactivate-module).*
- **HIGH** `rbac.md` · `SEC-CANACCESS`  
  RoleResource and UserResource are declared in the ## Filament section with no canAccess() statement. These manage roles/permissions and are high-sensitivity.  
  → *Add canAccess() per artifact: RoleResource gated by core.rbac.view-any; UserResource gated by core.rbac.view-any/assign (always-free core, no hasModule gate).*
- **HIGH** `webhooks.md` · `SEC-CANACCESS`  
  WebhookEndpointResource is declared in the ## Filament section with no canAccess() statement. The module is module-gated (depends-on core.billing), so the gate must combine permission + hasModule.  
  → *Add canAccess() to the Filament section: WebhookEndpointResource::canAccess() = core.webhooks.view-any AND BillingService::hasModule('core.webhooks').*
- **MEDIUM** `billing-engine.md` · `SEC-RATELIMIT`  
  The Stripe webhook endpoint (StripeWebhookController) is an unauthenticated inbound surface. Signature verification is specified, but no rate limiter is cited on the webhook route.  
  → *Cite a throttle limiter on the Stripe webhook route (e.g. a dedicated 'webhook' limiter) in the routes/Filament section, in addition to the existing signature verification.*
- **MEDIUM** `data-import.md` · `SEC-UPLOAD`  
  The upload DTO whitelists mimes:csv,xlsx and 'max per settings', but the spec does not state the companies/{company_id}/ tenant-scoped storage path for the uploaded import file (it only says 'tenant-scoped file' for the error report). Path enforcement is delegated implicitly to core.files without an explicit note.  
  → *Note in Core Features / DTOs that the uploaded import file is stored via FileStorageService under companies/{company_id}/ (no raw Storage::put), matching the file-storage path contract.*
- **MEDIUM** `data-import.md` · `SEC-RATELIMIT`  
  Starting an import is an expensive bulk operation (file parse + chunked job dispatch). No rate limiter is cited on the StartImportAction / import create endpoint to prevent abuse or accidental flooding.  
  → *Cite a throttle limiter on the import-create surface (e.g. a low-rate 'import' limiter) in the Filament/Actions section.*
- **MEDIUM** `data-privacy.md` · `SEC-RATELIMIT`  
  Full company data export (ExportCompanyDataAction / DataExportPage trigger) is a heavy operation producing a ZIP of all CSVs. No rate limiter is cited to prevent repeated triggering.  
  → *Cite a throttle limiter on the export trigger (e.g. one export per company per N minutes) in the Filament/Actions section.*
- **MEDIUM** `health-monitoring.md` · `SEC-RATELIMIT`  
  GET /health is a public/unauthenticated JSON endpoint exposing DB/Redis/Meilisearch/Horizon/queue/disk status. No rate limiter and no access restriction are cited; this is an information-disclosure and abuse surface.  
  → *Cite a throttle limiter on GET /health and/or restrict detailed output to authenticated/monitoring callers (token-guarded), returning minimal status to anonymous callers.*
- **MEDIUM** `notifications.md` · `SEC-CANACCESS`  
  The bell render hook and NotificationPreferencesPage are declared in the ## Filament section with no canAccess() statement. These are per-user self-service surfaces (auth-only by design), but the spec should still state the canAccess gate explicitly.  
  → *Add a canAccess() note: NotificationPreferencesPage::canAccess() = authenticated user (own preferences only); bell renders for any authenticated panel user.*
- **MEDIUM** `setup-wizard.md` · `SEC-CANACCESS`  
  SetupWizardPage is declared in the ## Filament section. The Permissions section states owner-only via core.setup.complete and invisibility after completion, but the Filament section itself does not state an explicit canAccess() gate.  
  → *Add canAccess() to the Filament section: SetupWizardPage::canAccess() = owner role AND companies.setup_completed_at IS NULL.*
- **MEDIUM** `webhooks.md` · `SEC-RATELIMIT`  
  The 'Test button' (SendTestWebhookAction) lets a user trigger arbitrary outbound HTTPS POSTs to a user-supplied URL on demand — an abuse/SSRF-amplification surface. No rate limiter is cited on the test action.  
  → *Cite a throttle limiter on SendTestWebhookAction (e.g. a few test sends per endpoint per minute) in the Actions/Filament section.*

### ecommerce  (13)

- **HIGH** `abandoned-cart.md` · `SEC-CANACCESS`  
  The ## Filament section lists AbandonedCartResource (#1) and CartRecoveryWidget (#6) but never states canAccess() = permission + BillingService::hasModule(). No access-control note for the Filament artifacts.  
  → *Add a canAccess() note to the Filament section: gate on ecommerce.abandoned-cart.view permission AND BillingService::hasModule('ecommerce.abandoned-cart').*
- **HIGH** `abandoned-cart.md` · `SEC-EXTERNAL`  
  RestoreCartController is a public, unauthenticated token link (recovery_token) that restores a cart into a session and can later flag an order as 'recovered'. No Sanctum/guest guard or signed-URL middleware is specified for this public surface.  
  → *Specify the route uses Laravel signed URLs (signed middleware) validating recovery_token, on the public/guest guard, with the recovery_token treated as a single-use capability token.*
- **HIGH** `orders.md` · `SEC-CANACCESS`  
  The ## Filament section lists EcOrderResource (#1), OrderFulfilmentPage (#3) and OrderStatsWidget (#6) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note gating the resource/page/widget on the relevant ecommerce.orders.* permissions AND BillingService::hasModule('ecommerce.orders').*
- **HIGH** `payments.md` · `SEC-CANACCESS`  
  The ## Filament section lists EcPaymentResource (read-only #1) with a refund action but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note gating EcPaymentResource and the refund action on ecommerce.payments.view-any / ecommerce.payments.refund AND BillingService::hasModule('ecommerce.payments').*
- **HIGH** `products.md` · `SEC-CANACCESS`  
  The ## Filament section lists EcProductResource and EcCategoryResource (#1 CRUD) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note gating both resources on ecommerce.products.* permissions AND BillingService::hasModule('ecommerce.products').*
- **HIGH** `promotions.md` · `SEC-CANACCESS`  
  The ## Filament section lists CouponResource and EcPromotionResource (#1 CRUD) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note gating both resources on ecommerce.promotions.* permissions AND BillingService::hasModule('ecommerce.promotions').*
- **HIGH** `reviews.md` · `SEC-CANACCESS`  
  The ## Filament section lists ReviewResource (#1 CRUD, moderation/reply actions) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note gating ReviewResource and its moderate/reply actions on ecommerce.reviews.* permissions AND BillingService::hasModule('ecommerce.reviews').*
- **HIGH** `reviews.md` · `SEC-EXTERNAL`  
  Public review submission (SubmitReviewData via signed link or open storefront) and public helpful-votes are unauthenticated external surfaces, but the spec does not specify which guard they run on (guest/public Sanctum context).  
  → *Specify the public submission/helpful-vote routes run on the public/guest guard with signed-URL validation of review_token, distinct from the authenticated Filament panel guard.*
- **HIGH** `storefront.md` · `SEC-CANACCESS`  
  The ## Filament section lists StorefrontSettingsPage (#7 custom page) and StorefrontPageResource (#1 CRUD) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note gating the settings page and page resource on ecommerce.storefront.manage AND BillingService::hasModule('ecommerce.storefront').*
- **HIGH** `variants.md` · `SEC-CANACCESS`  
  The ## Filament section lists a Variant relation manager on EcProductResource but does not state canAccess() = permission + BillingService::hasModule() for the variant management surface.  
  → *Add canAccess()/relation-manager visibility note gating on ecommerce.variants.manage AND BillingService::hasModule('ecommerce.variants').*
- **MEDIUM** `abandoned-cart.md` · `SEC-RATELIMIT`  
  The public RestoreCartController endpoint (token-based, unauthenticated) does not cite a rate limiter; token-guessing/enumeration of recovery links is unthrottled.  
  → *Add a throttle/RateLimiter to the public restore-cart route (e.g. throttle:public).*
- **MEDIUM** `payments.md` · `SEC-RATELIMIT`  
  EcStripeWebhookController is an inbound public endpoint; signature verification is specified (good) but no rate limiter is cited to absorb webhook floods/replays at the edge.  
  → *Cite a rate limiter on the webhook route (e.g. throttle:webhooks) in addition to the existing Stripe signature verification and idempotency guards.*
- **MEDIUM** `products.md` · `SEC-UPLOAD`  
  Product image gallery uses Media Library (images[] in CreateProductData) but the spec gives no file-type whitelist, max size, or companies/{id}/ storage path constraint.  
  → *Add an upload note: restrict to image MIME types (jpg/png/webp), enforce a max file size, and store under companies/{company_id}/ per the security baseline.*

### lms  (16)

- **HIGH** `certifications.md` · `SEC-CANACCESS`  
  The ## Filament section defines CertificateTemplateResource, CertificateResource (read-only) and CertificationExpiryWidget but never states canAccess() = permission + BillingService::hasModule(). No module-gating/permission gate documented for the Filament artifacts.  
  → *Add a canAccess() note to the ## Filament section: each resource/page/widget guards on the lms.certifications.* permission AND BillingService::hasModule('lms.certifications').*
- **HIGH** `courses.md` · `SEC-CANACCESS`  
  The ## Filament section defines CourseResource and CourseBuilderPage but does not state canAccess() = permission + BillingService::hasModule(). No module-gating/permission gate documented.  
  → *Add a canAccess() note: CourseResource and CourseBuilderPage guard on lms.courses.* permission AND BillingService::hasModule('lms.courses').*
- **HIGH** `enrolments.md` · `SEC-EXTERNAL`  
  The learner portal /learn (Vue + Inertia, ui-strategy row #15 — external learners) and external-learner login via 'signed magic link' / 'portal_token' do not specify the Sanctum scoped portal guard. Row #15 mandates 'Sanctum, scoped portal guard'. The spec only says 'self-access via portal token/user link'.  
  → *Specify in the ## Filament / portal section that the /learn portal authenticates external learners via a Sanctum scoped portal guard (learner guard), and that lms_learners.portal_token issuance/rotation flows through that guard rather than ad-hoc token checks.*
- **HIGH** `enrolments.md` · `SEC-CANACCESS`  
  EnrolmentResource and EnrolmentProgressWidget in the ## Filament section have no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add a canAccess() note: EnrolmentResource/Widget guard on lms.enrolments.* permission AND BillingService::hasModule('lms.enrolments').*
- **HIGH** `lessons.md` · `SEC-CANACCESS`  
  The ## Filament section defines a Lesson relation manager on course modules but never states canAccess()/module gating (permission + BillingService::hasModule()).  
  → *Add a canAccess() note for the lesson relation manager guarding on lms.lessons.manage permission AND BillingService::hasModule('lms.lessons' or 'lms.courses').*
- **HIGH** `lms-analytics.md` · `SEC-CANACCESS`  
  LmsDashboardPage and the three widgets have no canAccess() = permission + BillingService::hasModule() statement in the ## Filament section.  
  → *Add a canAccess() note: LmsDashboardPage/widgets guard on lms.analytics.view permission AND BillingService::hasModule('lms.analytics').*
- **HIGH** `mentoring.md` · `UI-ROW`  
  MentorDirectoryPage is mapped to ui-strategy 'row #9 gallery custom page', but row #9 is 'Report builder / query UI' — a gallery directory page is not in the decision table. Mismatched/non-existent UI kind needs an ADR.  
  → *Either map the mentor directory to an existing table row (e.g. a custom Filament page modelled on an approved row) or raise an ADR to add a gallery/directory UI kind to ui-strategy.md, then cite the correct row.*
- **HIGH** `mentoring.md` · `SEC-CANACCESS`  
  MentorshipResource and MentorDirectoryPage have no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add a canAccess() note: guard on lms.mentoring.participate / lms.mentoring.view-pairings AND BillingService::hasModule('lms.mentoring').*
- **HIGH** `skills-matrix.md` · `UI-ROW`  
  SkillsMatrixPage is mapped to ui-strategy 'row #9 heat-map custom page', but row #9 is 'Report builder / query UI' — a skills heat-map matrix is not represented by that row. Mismatched/non-existent UI kind needs an ADR.  
  → *Map the heat-map to an approved table row or raise an ADR adding a heat-map/matrix UI kind to ui-strategy.md, then cite the correct row.*
- **HIGH** `skills-matrix.md` · `SEC-CANACCESS`  
  SkillResource and SkillsMatrixPage have no canAccess() = permission + BillingService::hasModule() statement.  
  → *Add a canAccess() note: guard on lms.skills.* permission AND BillingService::hasModule('lms.skills').*
- **MEDIUM** `certifications.md` · `SEC-INPUT`  
  Certificate template management (create/update templates with design jsonb) is a write operation but the ## DTOs section only defines VerifyCertificateData (the public read). No Data class for template create/update writes.  
  → *Add a CreateCertificateTemplateData (spatie/laravel-data) DTO covering name, design, course_id, validity_months for the template write path.*
- **MEDIUM** `enrolments.md` · `SEC-RATELIMIT`  
  Bulk enrolment (bulkEnrol / BulkEnrolData) is an expensive bulk operation with no rate limiter cited.  
  → *Cite a throttle/rate limiter on the bulk-enrol action (e.g. per-user throttle) in the Services or Filament section.*
- **MEDIUM** `lessons.md` · `SEC-UPLOAD`  
  Lessons accept video uploads and downloadable file resources. Spec notes 'tenant-scoped, streamed via signed URL' but does not specify a file-type whitelist, max size, or a companies/{id}/ storage path.  
  → *Add upload constraints to the lesson video/file content section: allowed MIME/type whitelist, max file size, and companies/{company_id}/ storage path.*
- **MEDIUM** `lms-analytics.md` · `SEC-RATELIMIT`  
  'Export reports' is an expensive export operation (compliance/engagement data) with no rate limiter cited.  
  → *Cite a rate limiter / throttle on the report export action in the Services or Filament section.*
- **LOW** `certifications.md` · `UI-THEME`  
  User-facing CertificateTemplateResource / CertificateResource render as stock Filament with no branding/theme note.  
  → *Add a note confirming the lms panel theme/branding is applied to these resources.*
- **LOW** `courses.md` · `UI-THEME`  
  CourseResource and the custom CourseBuilderPage render as stock Filament with no branding/theme note.  
  → *Add a note confirming lms panel theming applies to the CourseResource and CourseBuilderPage.*

### events  (16)

- **HIGH** `event-analytics.md` · `SEC-CANACCESS`  
  ## Filament section defines EventAnalyticsDashboard (#6) but never states canAccess() must combine the permission (events.analytics.view) with BillingService::hasModule(). 'module gating' only appears in the test checklist.  
  → *Add a canAccess() note in the Filament section: gate the dashboard on events.analytics.view AND BillingService::hasModule('events').*
- **HIGH** `events.md` · `SEC-CANACCESS`  
  ## Filament defines EventResource and EventCalendarPage but does not state canAccess() = permission + BillingService::hasModule(). Gating only referenced in test checklist.  
  → *Add canAccess() note: gate EventResource/EventCalendarPage on events.events.* permission AND BillingService::hasModule('events').*
- **HIGH** `registrations.md` · `SEC-CANACCESS`  
  ## Filament defines RegistrationResource, CheckInPage, RegistrationStatsWidget but no canAccess() = permission + BillingService::hasModule() note. Module gating only in test checklist.  
  → *Add canAccess() note gating each artifact on events.registrations.* AND BillingService::hasModule('events').*
- **HIGH** `registrations.md` · `SEC-ENCRYPT`  
  ev_registrations stores attendee_email (external attendee personal email) and attendee_name plus free-form custom_answers jsonb (likely PII), but encrypted-fields frontmatter is empty.  
  → *Add ev_registrations.attendee_email (and attendee_name / custom_answers as applicable) to encrypted-fields with encrypted cast on text columns; if attendee_email must stay queryable for the unique (event_id, attendee_email) constraint, add a hashed lookup column instead of leaving it plaintext.*
- **HIGH** `speakers.md` · `SEC-CANACCESS`  
  ## Filament defines SpeakerResource and session-assignment relation but no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating SpeakerResource on events.speakers.* AND BillingService::hasModule('events').*
- **HIGH** `sponsors.md` · `SEC-CANACCESS`  
  ## Filament defines SponsorResource and a revenue widget but no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating SponsorResource on events.sponsors.* AND BillingService::hasModule('events').*
- **HIGH** `tickets.md` · `SEC-WEBHOOK`  
  Tickets relies on an inbound Stripe webhook ('webhook success → purchase paid', 'Webhook handled by shared Stripe webhook routing') but the spec body does not state Stripe signature verification; it is only gestured at via a Related link to architecture/security.  
  → *State in the Services/webhook section that the inbound Stripe webhook verifies the Stripe-Signature header (signing secret) before processing payment-confirmation events.*
- **HIGH** `tickets.md` · `SEC-CANACCESS`  
  ## Filament defines ticket-types relation manager, TicketSalesWidget, and Purchases list but no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating ticket artifacts on events.tickets.* AND BillingService::hasModule('events').*
- **HIGH** `venues.md` · `SEC-CANACCESS`  
  ## Filament defines VenueResource but no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating VenueResource on events.venues.* AND BillingService::hasModule('events').*
- **MEDIUM** `event-analytics.md` · `SEC-RATELIMIT`  
  Core feature 'Export reports' is an expensive aggregation/export operation but no rate limiter is cited.  
  → *Cite a throttle (e.g. RateLimiter on the export action) for analytics report exports.*
- **MEDIUM** `events.md` · `SEC-RICHTEXT`  
  ev_events.description is a rich-text 'purified' field but the spec does not explicitly cite HTMLPurifier (ezyang/htmlpurifier) sanitization on write.  
  → *State that description is sanitized via HTMLPurifier before persistence.*
- **MEDIUM** `registrations.md` · `SEC-RATELIMIT`  
  RegistrationResource provides an 'attendee export' but no rate limiter is cited for that export operation (the public form rate-limit is covered, the admin export is not).  
  → *Cite a throttle for the attendee export action.*
- **MEDIUM** `speakers.md` · `SEC-UPLOAD`  
  Speakers store a photo (photo_media_id) uploadable via the public signed-token SpeakerSubmit flow, but the spec does not state an image type whitelist, max size, or companies/{id}/ storage path.  
  → *Note allowed image MIME whitelist, max file size, and companies/{id}/ media path for speaker photo uploads (especially the public submit endpoint).*
- **MEDIUM** `speakers.md` · `SEC-RICHTEXT`  
  ev_speakers.bio is rich-text 'purified' and is editable via the public token submit, but HTMLPurifier sanitization is not explicitly cited.  
  → *State bio is sanitized via HTMLPurifier on both admin and public-token writes.*
- **MEDIUM** `sponsors.md` · `SEC-UPLOAD`  
  Sponsors store a logo (logo_media_id) upload but the spec does not state an image type whitelist, max size, or companies/{id}/ storage path.  
  → *Note allowed image MIME whitelist, max file size, and companies/{id}/ media path for sponsor logo uploads.*
- **MEDIUM** `tickets.md` · `SEC-RATELIMIT`  
  Public ticket purchase flow (PaymentIntent creation, discount-code application) is a public, payment-triggering endpoint but no rate limiter is cited.  
  → *Cite a throttle on the public purchase endpoint (and discount-code validation) to prevent abuse/enumeration.*

### marketing  (13)

- **HIGH** `campaigns.md` · `SEC-CANACCESS`  
  Filament section lists CampaignResource and CampaignStatsWidget but does not state canAccess() = permission + BillingService::hasModule(). Module gating only implied in test checklist.  
  → *Add an explicit note to the ## Filament section that canAccess() on CampaignResource/CampaignStatsWidget checks marketing.campaigns.* permission AND BillingService::hasModule('marketing.campaigns').*
- **HIGH** `campaigns.md` · `SEC-EXTERNAL`  
  TrackOpenController, TrackClickController and UnsubscribeController are described as 'public token endpoints' (unauthenticated surfaces) with no guard/auth strategy specified.  
  → *Specify these endpoints use signed/tokenized URLs (e.g. Laravel signed routes or per-recipient opaque token validation) and run outside the Sanctum session guard; document the token scheme and that they resolve company by token, not session.*
- **HIGH** `content-cms.md` · `SEC-CANACCESS`  
  Filament section lists PostResource and PostCategoryResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add note that canAccess() on PostResource/PostCategoryResource checks marketing.cms.* permission AND BillingService::hasModule('marketing.cms').*
- **HIGH** `email-sequences.md` · `SEC-CANACCESS`  
  Filament section lists SequenceResource and SequenceEnrolmentResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add note that canAccess() on SequenceResource/SequenceEnrolmentResource checks marketing.sequences.* permission AND BillingService::hasModule('marketing.sequences').*
- **HIGH** `forms.md` · `SEC-CANACCESS`  
  Filament section lists FormResource and FormSubmissionResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add note that canAccess() on FormResource/FormSubmissionResource checks marketing.forms.* permission AND BillingService::hasModule('marketing.forms').*
- **HIGH** `forms.md` · `SEC-EXTERNAL`  
  Public submission endpoint (PublicFormController, /f/{slug}, embed/iframe) is unauthenticated and accepts writes from external customer websites, but no guard/CSRF/origin strategy is specified beyond spam controls.  
  → *Specify the public submit endpoint runs outside the Sanctum session guard with an explicit public route (no auth), resolves company by form slug, and document CSRF exemption + allowed-origin handling for cross-site embeds.*
- **HIGH** `landing-pages.md` · `SEC-CANACCESS`  
  Filament section lists LandingPageResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add note that canAccess() on LandingPageResource checks marketing.landing-pages.* permission AND BillingService::hasModule('marketing.landing-pages').*
- **HIGH** `marketing-analytics.md` · `SEC-CANACCESS`  
  Filament section lists MarketingDashboardPage and widgets but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add note that canAccess() on MarketingDashboardPage checks marketing.analytics.view permission AND BillingService::hasModule('marketing.analytics').*
- **HIGH** `utm-tracking.md` · `SEC-CANACCESS`  
  Filament section lists UtmBuilderPage but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add note that canAccess() on UtmBuilderPage checks marketing.utm.* permission AND BillingService::hasModule('marketing.utm').*
- **MEDIUM** `campaigns.md` · `SEC-RATELIMIT`  
  Public unauthenticated tracking/unsubscribe endpoints (TrackOpen, TrackClick, Unsubscribe) cite no rate limiter despite being internet-facing.  
  → *Add a rate limiter (throttle middleware, per-IP or per-token) to the public Track/Unsubscribe routes in the spec.*
- **MEDIUM** `content-cms.md` · `SEC-RATELIMIT`  
  Public blog (BlogController, /blog, /blog/{slug}) is an unauthenticated full-text-search-backed surface with no rate limiter cited.  
  → *Cite a throttle/rate limiter on the public blog and search routes to protect Meilisearch-backed queries from abuse.*
- **MEDIUM** `landing-pages.md` · `SEC-RATELIMIT`  
  RecordVisitAction is a public unauthenticated endpoint (increments visit_count) with no rate limiter cited, allowing count inflation/abuse.  
  → *Cite a per-IP throttle on the public visit/render route used by RecordVisitAction.*
- **MEDIUM** `marketing-analytics.md` · `SEC-RATELIMIT`  
  CSV report export is an expensive aggregate operation with no rate limiter cited.  
  → *Cite a rate limiter (throttle) on the CSV export action in the spec.*

### support  (10)

- **HIGH** `automations.md` · `SEC-CANACCESS`  
  ## Filament section lists AutomationRuleResource (and logs relation manager) but never states canAccess() = permission + BillingService::hasModule() gating.  
  → *Add canAccess() note: AutomationRuleResource gates on support.automations.* permission AND BillingService::hasModule('support.automations').*
- **HIGH** `canned-responses.md` · `SEC-CANACCESS`  
  ## Filament section lists CannedResponseResource but never states canAccess() = permission + BillingService::hasModule() gating.  
  → *Add canAccess() note: CannedResponseResource gates on support.canned.* permission AND BillingService::hasModule('support.canned').*
- **HIGH** `knowledge-base.md` · `SEC-CANACCESS`  
  ## Filament section lists KbArticleResource and KbCategoryResource but never states canAccess() = permission + BillingService::hasModule() gating.  
  → *Add canAccess() note: KB resources gate on support.kb.* permission AND BillingService::hasModule('support.kb').*
- **HIGH** `live-chat.md` · `SEC-CANACCESS`  
  ## Filament section lists ChatQueuePage and ChatTranscriptResource but never states canAccess() = permission + BillingService::hasModule() gating.  
  → *Add canAccess() note: ChatQueuePage/ChatTranscriptResource gate on support.chat.* permission AND BillingService::hasModule('support.chat').*
- **HIGH** `live-chat.md` · `SEC-EXTERNAL`  
  The embeddable public chat widget exposes an unauthenticated HTTP endpoint (StartChatData via widget_key, ChatWidgetController). Channel auth uses a per-chat signed token, but the spec never specifies which guard the public widget HTTP API runs under (Sanctum / dedicated unauth widget guard).  
  → *State the public widget HTTP endpoints run under an explicit scoped guard (Sanctum stateless / dedicated widget guard) limited to widget-key + per-chat token scope, not the panel session guard.*
- **HIGH** `sla.md` · `SEC-CANACCESS`  
  ## Filament section lists SlaPolicyResource, SlaMonitorPage, SlaComplianceWidget but never states canAccess() = permission + BillingService::hasModule() gating.  
  → *Add canAccess() note: SLA artifacts gate on support.sla.* permission AND BillingService::hasModule('support.sla').*
- **HIGH** `support-analytics.md` · `SEC-CANACCESS`  
  ## Filament section lists SupportDashboardPage (and analytics widgets) but never states canAccess() = permission + BillingService::hasModule() gating.  
  → *Add canAccess() note: SupportDashboardPage gates on support.analytics.view permission AND BillingService::hasModule('support.analytics').*
- **HIGH** `support-analytics.md` · `SEC-EXTERNAL`  
  The public CSAT page (/csat/{token}, CsatController + Vue, CsatResponseData) is an unauthenticated public surface. It is rate-limited and token-validated, but the spec never specifies the guard/middleware stack (Sanctum stateless / web guest) the public endpoint runs under.  
  → *State the public CSAT submit endpoint runs under an explicit unauthenticated guard/middleware (token-only, no panel session) alongside the existing rate limiter.*
- **HIGH** `tickets.md` · `SEC-CANACCESS`  
  ## Filament section lists TicketResource, TicketInboxPage, TicketStatsWidget, TicketCategoryResource but never states canAccess() = permission check + BillingService::hasModule() gating for any artifact.  
  → *Add a canAccess() note to the Filament section: every resource/page/widget gates on the relevant support.tickets.* permission AND BillingService::hasModule('support.tickets').*
- **MEDIUM** `tickets.md` · `SEC-UPLOAD`  
  Ticket attachments are handled via Media Library (Core Features + CreateTicketData attachments[]) but the spec gives no file-type whitelist, max size, or companies/{id}/ storage path.  
  → *Specify allowed attachment MIME/extension whitelist, a max file size, and the companies/{company_id}/ storage path for ticket attachments.*

### customer-success  (14)

- **HIGH** `churn-risk.md` · `SEC-CANACCESS`  
  ## Filament section defines ChurnRiskResource (#1) and ChurnRiskWidget (#6) but never states canAccess() = permission + BillingService::hasModule(). No gating contract documented for the Filament artifacts.  
  → *Add a canAccess() note to the Filament section: each Resource/Widget gated on cs.churn.view-any AND BillingService::hasModule('cs.churn').*
- **HIGH** `health-scores.md` · `SEC-CANACCESS`  
  ## Filament section defines HealthScoreResource (#1) and HealthDashboardPage (#6) but never states canAccess() = permission + BillingService::hasModule(). Anchor module of the domain lacks documented gating.  
  → *Add canAccess() note: HealthScoreResource/HealthDashboardPage gated on cs.health.view-any AND BillingService::hasModule('cs.health').*
- **HIGH** `nps.md` · `SEC-CANACCESS`  
  ## Filament section defines NpsSurveyResource, NpsResponseResource (#1) and NpsDashboardPage (#6) but never states canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: resources gated on cs.nps.view-any/manage AND BillingService::hasModule('cs.nps').*
- **HIGH** `nps.md` · `SEC-EXTERNAL`  
  Public NPS response surface /nps/{token} (Vue+Inertia row #16, NpsResponseController) is an unauthenticated external endpoint but the spec never specifies the auth/guard model. It relies only on a per-recipient token with no Sanctum guard declaration for the public surface.  
  → *Specify the public surface is served outside any authenticated panel guard, with token-scoped access only (no Sanctum session), and document token validity/single-use enforcement at the controller boundary.*
- **HIGH** `playbooks.md` · `SEC-CANACCESS`  
  ## Filament section defines PlaybookResource and PlaybookRunResource (#1) but never states canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: resources gated on cs.playbooks.view-any/manage AND BillingService::hasModule('cs.playbooks').*
- **HIGH** `qbr.md` · `SEC-CANACCESS`  
  ## Filament section defines QbrResource (#1) but never states canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: QbrResource gated on cs.qbr.view-any/manage AND BillingService::hasModule('cs.qbr').*
- **HIGH** `success-analytics.md` · `SEC-CANACCESS`  
  ## Filament section defines CsDashboardPage (#6) and four widgets but never states canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: CsDashboardPage/widgets gated on cs.analytics.view AND BillingService::hasModule('cs.analytics').*
- **MEDIUM** `success-analytics.md` · `SEC-RATELIMIT`  
  Core Features lists 'Export reports' (CsDashboardPage export) — an expensive aggregation/export operation over cs_health_scores, churn, nps, playbook runs and invoices — but no rate limiter is cited for the export action.  
  → *Cite a throttle/rate limiter on the export action (e.g. a per-user export throttle) in the Filament or Services section.*
- **LOW** `churn-risk.md` · `UI-THEME`  
  User-facing CS UI (ChurnRiskResource severity queue, ChurnRiskWidget) in /crm panel has no branding/theme note — relies on default Filament look.  
  → *Add a theming note confirming CS nav group artifacts inherit the /crm panel brand theme.*
- **LOW** `health-scores.md` · `UI-THEME`  
  User-facing CS UI (HealthScoreResource, HealthDashboardPage) in /crm has no theme/branding note.  
  → *Add a theming note confirming the CS nav group artifacts inherit the /crm panel brand.*
- **LOW** `nps.md` · `UI-THEME`  
  User-facing CS UIs (NpsSurveyResource, NpsResponseResource, NpsDashboardPage) in /crm have no theme/branding note; the public Respond.vue page also lacks a brand-styling note.  
  → *Add a theming note for the /crm CS artifacts and confirm the public Vue response page carries company/FlowFlex branding.*
- **LOW** `playbooks.md` · `UI-THEME`  
  User-facing CS UI (PlaybookResource, PlaybookRunResource) in /crm has no theme/branding note.  
  → *Add a theming note confirming CS nav group artifacts inherit the /crm panel brand.*
- **LOW** `qbr.md` · `UI-THEME`  
  User-facing CS UI (QbrResource) in /crm has no theme/branding note.  
  → *Add a theming note confirming the CS nav group artifact inherits the /crm panel brand.*
- **LOW** `success-analytics.md` · `UI-THEME`  
  User-facing CS dashboard UI (CsDashboardPage + apex charts) in /crm has no theme/branding note.  
  → *Add a theming note confirming the dashboard page inherits the /crm panel brand and chart palette.*

### operations  (13)

- **HIGH** `goods-receipt.md` · `SEC-CANACCESS`  
  ## Filament defines GoodsReceiptResource but the section does not state canAccess() gated on permission + BillingService::hasModule().  
  → *Add a canAccess() note to the ## Filament section: canAccess() returns permission check (operations.goods-receipt.view-any) AND BillingService::hasModule('operations.goods-receipt').*
- **HIGH** `inventory.md` · `SEC-CANACCESS`  
  ## Filament defines ItemResource, StockMovementResource and LowStockWidget but no canAccess() = permission + BillingService::hasModule() statement.  
  → *State in ## Filament that all resources/widgets gate canAccess() on the operations.inventory permission AND BillingService::hasModule('operations.inventory').*
- **HIGH** `operations-reporting.md` · `SEC-CANACCESS`  
  ## Filament defines OperationsDashboardPage (and valuation/spend widgets) with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating the dashboard page on operations.reporting.view AND BillingService::hasModule('operations.reporting').*
- **HIGH** `purchase-orders.md` · `SEC-CANACCESS`  
  ## Filament defines PurchaseOrderResource with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating on operations.purchase-orders.view-any AND BillingService::hasModule('operations.purchase-orders').*
- **HIGH** `stock-adjustments.md` · `SEC-CANACCESS`  
  ## Filament defines StockAdjustmentResource and StocktakePage with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating both the resource and the StocktakePage on operations.adjustments permissions AND BillingService::hasModule('operations.adjustments').*
- **HIGH** `suppliers.md` · `SEC-CANACCESS`  
  ## Filament defines OpsSupplierResource with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating on operations.suppliers.view-any AND BillingService::hasModule('operations.suppliers').*
- **HIGH** `warehouses.md` · `SEC-CANACCESS`  
  ## Filament defines WarehouseResource and WarehouseTransferResource with no canAccess() = permission + BillingService::hasModule() note.  
  → *Add canAccess() note gating on operations.warehouses permissions AND BillingService::hasModule('operations.warehouses').*
- **MEDIUM** `operations-reporting.md` · `SEC-RATELIMIT`  
  Excel export of operations reports is an expensive export operation but no rate limiter is cited.  
  → *Cite a rate limiter on the Excel export action (e.g. throttled export endpoint per user/company).*
- **MEDIUM** `purchase-orders.md` · `SEC-RATELIMIT`  
  PO send action generates a PDF and queues an outbound email to the supplier (expensive/external-facing operation) but no rate limiter is cited.  
  → *Cite a rate limiter on the send action / GeneratePoPdfJob+PurchaseOrderMail dispatch (e.g. per-company throttle) to prevent PDF/email abuse.*
- **MEDIUM** `stock-adjustments.md` · `SEC-RATELIMIT`  
  Stocktake mode supports bulk count entry / bulk adjustment generation (expensive bulk op) with no rate limiter cited.  
  → *Cite a rate limiter on the stocktake bulk submission to throttle large bulk adjustment runs per company.*
- **LOW** `inventory.md` · `UI-THEME`  
  User-facing ItemResource / StockMovementResource / LowStockWidget have no branding/theme note; rely on default Filament look.  
  → *Confirm operations panel theming is applied to these user-facing UIs.*
- **LOW** `purchase-orders.md` · `UI-THEME`  
  User-facing PurchaseOrderResource CRUD has no branding/theme note; relies on default Filament look.  
  → *Confirm operations panel theming is applied to this user-facing resource.*
- **LOW** `stock-adjustments.md` · `UI-THEME`  
  StocktakePage custom page and StockAdjustmentResource have no branding/theme note; rely on default Filament look.  
  → *Confirm operations panel theming is applied to the StocktakePage and resource.*

### workplace  (13)

- **HIGH** `desk-booking.md` · `SEC-CANACCESS`  
  The '## Filament' section lists DeskResource and DeskBookingPage but never states canAccess() must gate on a permission AND BillingService::hasModule(). No access-control mention for either artifact.  
  → *Add to the Filament section that every resource/page implements canAccess() = static::can('workplace.desks.*') && BillingService::hasModule('workplace.desks').*
- **HIGH** `desk-booking.md` · `UI-ROW`  
  DeskBookingPage is described as '#11-style map custom page' (floor map with positioned divs over a floor image, click-to-book). Row 11 of the ui-strategy decision table is 'Org chart / tree views', which does not cover an interactive floor/spatial map. This UI kind is not in the table.  
  → *Raise an ADR adding a decision-table row for spatial/floor-map custom pages, or re-map the page to an existing approved row, before building.*
- **HIGH** `maintenance.md` · `SEC-CANACCESS`  
  The '## Filament' section lists MaintenanceRequestResource and MaintenanceScheduleResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add a canAccess() note to the Filament section gating both resources on workplace.maintenance.* permissions AND BillingService::hasModule('workplace.maintenance').*
- **HIGH** `room-booking.md` · `SEC-CANACCESS`  
  The '## Filament' section lists RoomResource and RoomBookingPage but never states canAccess() must gate on permission AND BillingService::hasModule().  
  → *Add canAccess() = static::can('workplace.rooms.*') && BillingService::hasModule('workplace.rooms') to the Filament section.*
- **HIGH** `visitor-management.md` · `SEC-CANACCESS`  
  The '## Filament' section lists VisitorResource and VisitorKioskPage but does not state canAccess() gating on permission + BillingService::hasModule(). The kiosk page in particular must restrict to the kiosk role via canAccess().  
  → *Add canAccess() note: VisitorResource gated on workplace.visitors.* + hasModule('workplace.visitors'); VisitorKioskPage gated on workplace.visitors.kiosk + module check.*
- **HIGH** `visitor-management.md` · `SEC-ENCRYPT`  
  wp_visitors stores visitor personal email (and name/company_name) but frontmatter encrypted-fields is empty. Personal email of external visitors is regulated PII per the encryption criteria.  
  → *Add wp_visitors.email (and consider name) to encrypted-fields with the encrypted cast on a text column; keep the 12-month GDPR purge as defence-in-depth.*
- **HIGH** `workplace-analytics.md` · `SEC-CANACCESS`  
  The '## Filament' section lists WorkplaceDashboardPage but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() = static::can('workplace.analytics.view') && BillingService::hasModule('workplace.analytics') to the dashboard page.*
- **MEDIUM** `maintenance.md` · `SEC-UPLOAD`  
  Module accepts photo attachments (before/after via Media Library) but the spec does not specify a file-type whitelist, max file size, or a companies/{id}/ tenant-scoped storage path.  
  → *Add an upload note: restrict to image MIME types (jpg/png/webp), set a max size, and store under companies/{company_id}/maintenance/ for tenant isolation.*
- **MEDIUM** `visitor-management.md` · `SEC-RATELIMIT`  
  VisitorKioskPage exposes a self-service check-in endpoint on a shared kiosk device (visitor lookup by name + walk-in submit). No rate limiter is cited, leaving the lookup/submit open to enumeration or abuse from the kiosk surface.  
  → *Cite a rate limiter on the kiosk check-in/lookup actions (e.g. throttle per device session/IP).*
- **MEDIUM** `workplace-analytics.md` · `SEC-RATELIMIT`  
  Core feature 'Export reports' (plus utilisation report generation over date ranges aggregating four tables) is an expensive operation with no rate limiter cited.  
  → *Cite a rate limiter / throttle on the export and metrics-generation endpoints (e.g. per-user export throttle), consistent with the cached metrics strategy.*
- **LOW** `desk-booking.md` · `UI-THEME`  
  DeskBookingPage is a user-facing custom Filament page with no branding/theme note; relies on default Filament look.  
  → *Add a note confirming the workplace panel theme/branding is applied to the custom floor-map page.*
- **LOW** `room-booking.md` · `UI-THEME`  
  RoomBookingPage (#4 calendar custom page) is user-facing with no branding/theme note; relies on default Filament look.  
  → *Add a note confirming workplace panel theming is applied to the calendar booking page.*
- **LOW** `visitor-management.md` · `UI-THEME`  
  VisitorKioskPage (#7 custom page) is a user-facing self-service surface with no branding/theme note; relies on default Filament look.  
  → *Add a note confirming kiosk page branding/theme is applied (kiosk UIs are highly visible).*

### dms  (10)

- **HIGH** `approval-workflows.md` · `SEC-CANACCESS`  
  The ## Filament section defines ApprovalWorkflowResource and ApprovalRequestResource but does not state canAccess() = permission + BillingService::hasModule('dms.approvals').  
  → *Add a ## Filament note that both resources gate via canAccess() combining the dms.approvals permission and BillingService::hasModule('dms.approvals').*
- **HIGH** `document-library.md` · `SEC-CANACCESS`  
  The ## Filament section defines three artifacts (DocumentLibraryPage, DocumentViewerPage, FolderResource) but nowhere states canAccess() = permission + BillingService::hasModule(). No canAccess gating mentioned for the custom pages or the resource.  
  → *Add a note in ## Filament that every page/resource implements canAccess() returning auth user has the dms.library permission AND BillingService::hasModule('dms.library'); custom pages must override canAccess() explicitly (Filament does not auto-gate custom pages).*
- **HIGH** `retention-policies.md` · `SEC-CANACCESS`  
  The ## Filament section defines RetentionPolicyResource, LegalHoldResource and a read-only Retention log view but does not mention canAccess() = permission + BillingService::hasModule('dms.retention').  
  → *Add a ## Filament note that each resource/view gates via canAccess() combining the dms.retention permission and BillingService::hasModule('dms.retention').*
- **HIGH** `templates.md` · `SEC-CANACCESS`  
  The ## Filament section defines DocumentTemplateResource and the GenerateFromTemplatePage custom wizard but does not state canAccess() = permission + BillingService::hasModule('dms.templates'). Custom pages are not auto-gated by Filament.  
  → *Add a ## Filament note: DocumentTemplateResource and GenerateFromTemplatePage override canAccess() to require the dms.templates permission AND BillingService::hasModule('dms.templates').*
- **HIGH** `version-control.md` · `SEC-CANACCESS`  
  The ## Filament section adds a version-history relation manager and upload/lock actions on DocumentViewerPage but does not state canAccess()/visibility gating tied to dms.versions permission + BillingService::hasModule('dms.versions').  
  → *Add a ## Filament note that the version relation manager and version/lock actions are visible/authorized only when the user holds the dms.versions permission AND BillingService::hasModule('dms.versions').*
- **HIGH** `wiki.md` · `SEC-CANACCESS`  
  The ## Filament section defines WikiPageResource and the WikiViewerPage custom page but does not state canAccess() = permission + BillingService::hasModule('dms.wiki'). The custom viewer page needs explicit canAccess().  
  → *Add a ## Filament note that WikiPageResource and WikiViewerPage gate via canAccess() combining the dms.wiki permission and BillingService::hasModule('dms.wiki').*
- **MEDIUM** `document-library.md` · `SEC-UPLOAD`  
  Upload is the core operation but the spec only references 'architecture/security upload rules' generically. It does not restate the concrete type whitelist and max file size in the spec; the companies/{id}/dms/ path is noted (good) but the whitelist/size limits are not enumerated.  
  → *In Core Features / DTOs, state the allowed MIME/extension whitelist and max upload size enforced on UploadDocumentData (e.g. via mimes + max validation), referencing the security baseline values explicitly rather than only by link.*
- **MEDIUM** `document-library.md` · `SEC-RATELIMIT`  
  Full-text Meilisearch search and the text-extraction job are present but no rate limiter is cited for search queries or repeated uploads/searches, which are relatively expensive operations.  
  → *Cite a rate limiter (RateLimiter::for) on document search and upload endpoints, scoped per company/user, to prevent abuse of Meilisearch and storage.*
- **MEDIUM** `templates.md` · `SEC-RATELIMIT`  
  Document/PDF generation via spatie/laravel-pdf (TemplateService::generate) is an expensive operation invoked from the GenerateFromTemplatePage but no rate limiter is cited.  
  → *Cite a rate limiter on the generate action (per user/company) to throttle PDF rendering and document creation.*
- **MEDIUM** `version-control.md` · `SEC-UPLOAD`  
  UploadVersionData accepts a file uploaded under 'security rules' generically, but the spec does not restate the type whitelist, max size, or the companies/{id}/dms/ storage path for new versions.  
  → *State explicitly that version files reuse the document-library upload whitelist, max size, and companies/{id}/dms/ path (via CompanyPathGenerator) in the DTO/Services section.*

### it  (9)

- **HIGH** `access-provisioning.md` · `SEC-CANACCESS`  
  ## Filament section defines SystemResource, AccessGrantResource, AccessTemplateResource, and AccessReviewPage but never states canAccess() = permission + BillingService::hasModule('it.access'). No authorization/gating note on any artifact.  
  → *Add a canAccess() note to the Filament section requiring each resource/page to gate on its it.access.* permission AND BillingService::hasModule('it.access').*
- **HIGH** `asset-inventory.md` · `SEC-CANACCESS`  
  ## Filament section defines AssetResource and AssetExpiryWidget but does not state canAccess() = permission + BillingService::hasModule('it.assets').  
  → *Add canAccess() note: AssetResource gates on it.assets.view-any/manage AND BillingService::hasModule('it.assets'); widget gated similarly.*
- **HIGH** `helpdesk.md` · `SEC-CANACCESS`  
  ## Filament section defines ItTicketResource and ItHelpdeskQueuePage but does not state canAccess() = permission + BillingService::hasModule('it.helpdesk').  
  → *Add canAccess() note: ItHelpdeskQueuePage gates on it.helpdesk.respond/view-any AND BillingService::hasModule('it.helpdesk'); ItTicketResource gated on it.helpdesk.create-own + module.*
- **HIGH** `it-reporting.md` · `SEC-CANACCESS`  
  ## Filament section defines ItDashboardPage (plus AssetValue/LicenceSpend/Helpdesk/Compliance widgets in Build Manifest) but does not state canAccess() = it.reporting.view + BillingService::hasModule('it.reporting').  
  → *Add canAccess() note: ItDashboardPage gates on it.reporting.view AND BillingService::hasModule('it.reporting').*
- **HIGH** `mdm-integration.md` · `SEC-CANACCESS`  
  ## Filament section defines MdmDeviceResource and MdmConfigPage but does not state canAccess() = permission + BillingService::hasModule('it.mdm'). MdmConfigPage handles provider credentials and the resource exposes lock/wipe device actions, so missing gating is a real authorization hole.  
  → *Add canAccess() note: MdmConfigPage gates on it.mdm.manage-config AND BillingService::hasModule('it.mdm'); MdmDeviceResource gated on it.mdm.view-any + module, with lock/wipe actions further gated on it.mdm.lock / it.mdm.wipe.*
- **HIGH** `software-licences.md` · `SEC-CANACCESS`  
  ## Filament section defines LicenceResource and LicenceRenewalWidget but does not state canAccess() = permission + BillingService::hasModule('it.licences').  
  → *Add canAccess() note: LicenceResource gates on it.licences.view-any/manage AND BillingService::hasModule('it.licences'); widget gated similarly.*
- **MEDIUM** `access-provisioning.md` · `SEC-RATELIMIT`  
  AccessReviewPage builds an employees x systems matrix with export, but no rate limiter is cited for the export / matrix-build operation.  
  → *Cite a throttle on the matrix export action (e.g. RateLimiter per company-user) in the Filament/Services section.*
- **MEDIUM** `asset-inventory.md` · `SEC-RATELIMIT`  
  Core Feature 'Bulk import via Core Data Import' (soft-dep core.import) is a bulk operation but no rate limiter is cited for the import.  
  → *Note that bulk asset import inherits/uses a rate limiter (per architecture/security.md) on the import endpoint/action.*
- **MEDIUM** `it-reporting.md` · `SEC-RATELIMIT`  
  Core Feature 'Export reports' on the dashboard aggregates across multiple modules (potentially expensive cross-table queries) but no rate limiter is cited for the export action.  
  → *Cite a throttle on the report export action (per-company-user) in the Filament/Services section.*

### legal  (10)

- **HIGH** `compliance-registers.md` · `SEC-CANACCESS`  
  Filament artifacts (FrameworkResource, ControlResource, ComplianceDashboardPage) are listed but the ## Filament section does not state canAccess() = permission + BillingService::hasModule(). Module gating only referenced in the test checklist, not as a stated Filament authorization requirement.  
  → *Add a canAccess() note in the ## Filament section: every resource/page must gate on the legal.compliance.* permission AND BillingService::hasModule('legal.compliance').*
- **HIGH** `dsar-processing.md` · `SEC-CANACCESS`  
  Filament artifacts (DsarRequestResource extended, DsarFulfilmentPage) listed but the ## Filament section does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate DsarRequestResource and DsarFulfilmentPage on legal.dsar.* permission AND BillingService::hasModule('legal.dsar').*
- **HIGH** `legal-contracts.md` · `SEC-CANACCESS`  
  Filament artifacts (LegalContractResource, ContractRenewalWidget) listed but the ## Filament section does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate LegalContractResource on legal.contracts.* permission AND BillingService::hasModule('legal.contracts').*
- **HIGH** `legal-spend.md` · `SEC-CANACCESS`  
  Filament artifacts (LegalExpenseResource, LegalSpendDashboardPage) listed but the ## Filament section does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate LegalExpenseResource and LegalSpendDashboardPage on legal.spend.* permission AND BillingService::hasModule('legal.spend').*
- **HIGH** `matter-management.md` · `SEC-CANACCESS`  
  Filament artifact (MatterResource) listed but the ## Filament section does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate MatterResource on legal.matters.* permission AND BillingService::hasModule('legal.matters').*
- **HIGH** `policy-library.md` · `SEC-CANACCESS`  
  Filament artifacts (PolicyResource, PolicyAcknowledgementPage, MyPoliciesPage) listed but the ## Filament section does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate all three on the relevant legal.policies.* permission (PolicyResource/PolicyAcknowledgementPage on view-any/publish, MyPoliciesPage on acknowledge-own) AND BillingService::hasModule('legal.policies').*
- **MEDIUM** `compliance-registers.md` · `SEC-UPLOAD`  
  Per-control evidence attachments via Media Library (uploads) but no file-type whitelist, max size, or companies/{id}/ storage path specified.  
  → *Specify allowed evidence file types (e.g. pdf/png/jpg/docx), max size, and the companies/{id}/ scoped storage path for the Media Library collection.*
- **MEDIUM** `dsar-processing.md` · `SEC-ENCRYPT`  
  legal_dsar_actions stores identity-verification method and free-text notes tied to a data subject (verification evidence, rejection reasons referencing personal data) but encrypted-fields is empty. Verification details and notes are sensitive personal data.  
  → *Either confirm via ADR that the DSAR record/PII stays in core.privacy and these action rows hold only references (no PII), or add legal_dsar_actions.notes to encrypted-fields. Document the decision in the spec.*
- **MEDIUM** `legal-contracts.md` · `SEC-UPLOAD`  
  Signed-PDF document storage via Media Library (manual signed-PDF upload) but no file-type whitelist (pdf only), max size, or companies/{id}/ path specified.  
  → *Specify PDF-only whitelist, max size, and companies/{id}/ scoped storage path for the signed-contract Media Library collection.*
- **MEDIUM** `matter-management.md` · `SEC-UPLOAD`  
  Document association via Media Library (uploads) but no file-type whitelist, max size, or companies/{id}/ storage path specified.  
  → *Specify allowed document types, max size, and companies/{id}/ scoped storage path for the matter-document Media Library collection.*

### procurement  (7)

- **HIGH** `approvals.md` · `SEC-CANACCESS`  
  The ## Filament section defines three artifacts (ApprovalRuleResource, ApprovalDelegationResource, PendingApprovalsPage) but does not state canAccess() = permission check + BillingService::hasModule() for any of them.  
  → *Add a note to the Filament section that every resource/page implements canAccess() gating on the procurement.approvals permissions AND BillingService::hasModule('procurement').*
- **HIGH** `goods-receipt.md` · `SEC-CANACCESS`  
  The ## Filament section defines ThreeWayMatchResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate ThreeWayMatchResource on procurement.goods-receipt.view-matches plus BillingService::hasModule('procurement').*
- **HIGH** `purchase-orders.md` · `SEC-CANACCESS`  
  The ## Filament section defines ProcurementPoResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate ProcurementPoResource on procurement.purchase-orders permissions plus BillingService::hasModule('procurement').*
- **HIGH** `requisitions.md` · `SEC-CANACCESS`  
  The ## Filament section defines RequisitionResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate RequisitionResource on procurement.requisitions.view-any plus BillingService::hasModule('procurement').*
- **HIGH** `spend-analytics.md` · `SEC-CANACCESS`  
  The ## Filament section defines SpendAnalyticsDashboard (and widgets) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate SpendAnalyticsDashboard on procurement.spend.view plus BillingService::hasModule('procurement').*
- **HIGH** `supplier-catalogue.md` · `SEC-CANACCESS`  
  The ## Filament section defines CatalogueItemResource and SupplierStatusResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate both resources on procurement.catalogue permissions plus BillingService::hasModule('procurement').*
- **MEDIUM** `spend-analytics.md` · `SEC-RATELIMIT`  
  Core Features and the Filament dashboard both expose 'Export reports' / export, an expensive aggregation+export operation, but no rate limiter is cited anywhere in the spec.  
  → *Cite a rate limiter (e.g. throttle on the export action) for the spend report export per architecture/security.md.*

### ai  (11)

- **HIGH** `copilot.md` · `SEC-CANACCESS`  
  ## Filament section defines CopilotPage (chat custom page) but does not state canAccess() = permission (ai.copilot.use) + BillingService::hasModule(). No gating mention for the page.  
  → *Add a canAccess() note to the Filament section requiring both the ai.copilot.use permission and BillingService::hasModule('ai.copilot') before the page renders.*
- **HIGH** `document-intelligence.md` · `SEC-CANACCESS`  
  ## Filament defines DocumentExtractionResource (CRUD + review/apply) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add a canAccess() note requiring the ai.document-intelligence permission(s) plus BillingService::hasModule('ai.document-intelligence').*
- **HIGH** `document-intelligence.md` · `SEC-ENCRYPT`  
  extracted_data (jsonb) and confidence store parsed contents of invoices (vendor bank IBAN/BIC), receipts, and CVs (DOB, personal email, government IDs) — sensitive data — yet frontmatter encrypted-fields is empty.  
  → *Encrypt the sensitive payload column(s): add ai_extractions.extracted_data (and/or a dedicated sensitive sub-field) to encrypted-fields with an encrypted cast on a text column, or document field-level encryption of bank/PII fields.*
- **HIGH** `model-config.md` · `SEC-CANACCESS`  
  ## Filament defines AiConfigPage and AiUsageDashboardPage but does not state canAccess() = permission (ai.config.manage / ai.config.view-usage) + BillingService::hasModule().  
  → *Add canAccess() notes: AiConfigPage gated by ai.config.manage and AiUsageDashboardPage by ai.config.view-usage, both plus BillingService::hasModule('ai.config').*
- **HIGH** `workflow-builder.md` · `SEC-CANACCESS`  
  ## Filament defines WorkflowBuilderPage, WorkflowResource and WorkflowRunResource but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() notes mapping each artifact to ai.workflows permissions (view-any / manage / run-test) plus BillingService::hasModule('ai.workflows').*
- **MEDIUM** `copilot.md` · `SEC-RATELIMIT`  
  CopilotService::send is an expensive per-message LLM call path (SSE-streamed) with no rate limiter cited; only a token budget hard-stop is mentioned, which does not prevent request-rate abuse.  
  → *Cite a per-user/per-company rate limiter (RateLimiter / throttle) on copilot message sends in addition to the LlmGateway budget.*
- **MEDIUM** `document-intelligence.md` · `SEC-UPLOAD`  
  File upload notes a type whitelist (pdf/jpg/png) and the companies/{id}/ path in the test checklist, but no max file size is specified.  
  → *State an explicit max upload size (e.g. max:10240 KB) in the upload rules alongside the type whitelist and tenant-scoped path.*
- **MEDIUM** `workflow-builder.md` · `SEC-RATELIMIT`  
  The action registry includes a 'call webhook' outbound action that hits external URLs and a run loop driven by arbitrary domain events; no rate limiter/throttle is cited for webhook-calling actions or workflow execution volume.  
  → *Cite a rate limiter on outbound webhook actions (and/or per-workflow execution throttling) to prevent abuse/SSRF-amplification, in addition to the existing loop guard.*
- **LOW** `copilot.md` · `UI-THEME`  
  CopilotPage is a user-facing custom chat page with no branding/theme note; relies on default Filament look.  
  → *Add a note confirming FlowFlex panel theming/branding is applied to the copilot chat page.*
- **LOW** `document-intelligence.md` · `UI-THEME`  
  DocumentExtractionResource review page (confidence highlighting) is user-facing custom UI with no branding/theme note; relies on default Filament look.  
  → *Add a note confirming FlowFlex panel theming is applied to the extraction review UI.*
- **LOW** `workflow-builder.md` · `UI-THEME`  
  WorkflowBuilderPage node editor is a user-facing custom page with no branding/theme note; relies on default Filament look.  
  → *Add a note confirming FlowFlex panel theming is applied to the workflow builder page.*

### analytics  (13)

- **HIGH** `dashboards.md` · `SEC-CANACCESS`  
  ## Filament section declares DashboardBuilderPage (#6) and DashboardResource (#1) but does not state canAccess() = permission + BillingService::hasModule(). No gating contract specified for the panel artifacts.  
  → *Add an explicit canAccess() note to the Filament section: each page/resource gates on the analytics.dashboards permission AND BillingService::hasModule('analytics.dashboards').*
- **HIGH** `data-views.md` · `SEC-CANACCESS`  
  ## Filament section declares DataViewsPage (#6) and a per-view report page (#9) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate on analytics.data-views permission AND BillingService::hasModule('analytics.data-views').*
- **HIGH** `kpi-tracking.md` · `SEC-CANACCESS`  
  ## Filament section declares KpiResource (#1) and KpiDashboardPage (#6) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate on analytics.kpis permission AND BillingService::hasModule('analytics.kpis').*
- **HIGH** `report-builder.md` · `SEC-CANACCESS`  
  ## Filament section declares ReportBuilderPage (#9) and ReportResource (#1) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate on analytics.reports permission AND BillingService::hasModule('analytics.reports').*
- **HIGH** `scheduled-exports.md` · `SEC-CANACCESS`  
  ## Filament section declares ScheduledExportResource (#1) but does not state canAccess() = permission + BillingService::hasModule().  
  → *Add canAccess() note: gate on analytics.exports permission AND BillingService::hasModule('analytics.exports').*
- **MEDIUM** `data-views.md` · `SEC-RATELIMIT`  
  Core Features lists 'Export view data (Excel)' over heavy cross-domain aggregation queries, but no rate limiter is cited for this expensive export operation.  
  → *Cite a rate limiter (e.g. throttle on the export action) for the data-view export endpoint per architecture/security.md.*
- **MEDIUM** `report-builder.md` · `SEC-RATELIMIT`  
  Export to Excel/CSV (ExportReportJob, exports queue) is an expensive export operation but no rate limiter is cited on the run/export endpoint to prevent abuse.  
  → *Cite a rate limiter on the analytics.reports.run/export actions per architecture/security.md.*
- **MEDIUM** `scheduled-exports.md` · `SEC-UPLOAD`  
  Module generates and stores export files (Excel/PDF) with file_path and serves large files via signed links, but the spec does not specify the storage path convention (companies/{id}/) or format whitelist for the generated/attached files. Test checklist mentions 'tenant-scoped' storage but the spec body gives no path contract.  
  → *State that generated export files are stored under companies/{id}/exports/ with the company disk, and that signed links for large files are tenant-scoped and time-limited.*
- **LOW** `dashboards.md` · `UI-THEME`  
  DashboardBuilderPage and DashboardResource are user-facing domain UIs with no branding/theme note — relies on default Filament look.  
  → *Confirm Analytics panel theming/branding is applied (panel color, layout) per domain-panels.md.*
- **LOW** `data-views.md` · `UI-THEME`  
  DataViewsPage and per-view report pages are user-facing domain UIs with no branding/theme note.  
  → *Confirm Analytics panel theming is applied to the data-view pages.*
- **LOW** `kpi-tracking.md` · `UI-THEME`  
  KpiResource and KpiDashboardPage are user-facing domain UIs with no branding/theme note.  
  → *Confirm Analytics panel theming is applied to the KPI pages.*
- **LOW** `report-builder.md` · `UI-THEME`  
  ReportBuilderPage and ReportResource are user-facing domain UIs with no branding/theme note.  
  → *Confirm Analytics panel theming is applied to the report builder pages.*
- **LOW** `scheduled-exports.md` · `UI-THEME`  
  ScheduledExportResource is a user-facing domain UI with no branding/theme note.  
  → *Confirm Analytics panel theming is applied to the scheduled exports resource.*

### foundation  (3)

- **HIGH** `email-setup.md` · `SEC-WEBHOOK`  
  The inbound Resend bounce/complaint webhook `POST /api/resend/webhook` only references signature verification as an unconfirmed assumption — `signature-verified *(assumed: Resend svix signature)*`. The verification mechanism is not authoritatively specified: there is no named middleware/verifier, no mention of where the signing secret comes from, and the test checklist's 'invalid signature → 400' relies on an unspecified implementation. An inbound webhook that flips `users.email_deliverable = false` without a locked signature-verification step is a spoofing/denial-of-delivery vector.  
  → *Promote signature verification from *(assumed)* to a concrete requirement: specify the Svix/Resend signature header, the secret env var (e.g. RESEND_WEBHOOK_SECRET), and a dedicated verification middleware on the route that rejects unsigned/invalid requests with 403 before reaching the controller. Remove the *(assumed)* marker or resolve it via ADR.*
- **MEDIUM** `email-setup.md` · `SEC-RATELIMIT`  
  The public, unauthenticated, CSRF-exempt webhook endpoint `POST /api/resend/webhook` cites no rate limiter. A flood of forged/replayed bounce events could abuse the HandleEmailBounceAction (DB writes + admin notifications).  
  → *Add a throttle middleware to the webhook route (e.g. `throttle:resend-webhook` with a per-IP/per-source limit) in routes/api.php and document it in the Build Manifest alongside the signature-verification middleware.*
- **MEDIUM** `filament-panels.md` · `SEC-RATELIMIT`  
  This module establishes the `/admin` and `/app` login surfaces (Filament auth) but the spec does not cite any rate limiting on the authentication/login endpoints. Auth endpoints are sensitive surfaces that require brute-force throttling per the security baseline; it is only mentioned tangentially in test-suite.md ('rate limiter cleared in beforeEach for auth endpoint tests') with no defining spec.  
  → *Add a note to the Filament panel spec that login endpoints on both panels enforce a login throttle (Laravel's default Filament login rate limit or an explicit `throttle` rule), and reference architecture/security.md for the limit values.*
