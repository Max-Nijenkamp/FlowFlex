---
type: module
domain: Partner & Channel
panel: partners
module-key: partners.onboarding
status: planned
color: "#4ADE80"
---

# Partner Onboarding

> Partner application form (public), review and approval, onboarding checklist (agreement signing, tax forms, portal setup), partner training tracks via LMS integration, and certification management with onboarding completion score.

**Panel:** `/partners`
**Module key:** `partners.onboarding`

## What It Does

Partner Onboarding manages the full lifecycle from a prospective partner's initial application through to their first active deal submission. A public application form (no login required) captures the applying organisation's details and type of partnership sought. The company reviews and approves or rejects applications in Filament. On approval, an automated onboarding checklist activates: the new partner must complete a series of tasks — sign the partnership agreement (via e-signature integration), submit a tax form (W-9 or W-8-BEN), complete onboarding training tracks in the LMS domain, and pass an optional certification assessment. An onboarding completion score (0–100%) is visible to both the company admin and the partner in the portal, giving the company visibility into how far each new partner has progressed.

## Features

### Core
- Public partner application form at `/partner-portal/apply` (no authentication required). Fields: company name, contact name, email, phone, website, country, partner type (reseller/affiliate/referral/integration), why they want to partner, annual revenue, and existing customer base description.
- Application review in Filament: view all pending applications, approve (creates `partners` record and sends portal login invite) or reject (with mandatory reason — rejection reason included in notification email to applicant).
- Onboarding task checklist: generated automatically on partner approval. Task types: agreement_signing / tax_form / training_track / certification / portal_setup / call_with_partner_manager. Each task has a due date and completion status.
- Partnership agreement e-signature: integrates with the e-signature integration (from the Legal domain) to send the agreement for digital signature. Task marked complete when the signed document returns.
- Portal setup task: guides the partner through creating their first portal user and exploring key features — completed when the partner user logs in for the first time.

### Advanced
- Training track assignment: on partner approval, the company's configured default training tracks for that partner type are auto-assigned in the LMS domain. The onboarding checklist includes a "Complete onboarding training" task that links to the partner's LMS learning path.
- Certification management: after completing training, partners can attempt a certification assessment (built in the LMS domain). Passing certification is displayed as a badge in the partner portal and in the Filament `PartnerResource` record.
- Onboarding completion score: calculated as `(completed_tasks / total_tasks) * 100`. Displayed as a progress bar in Filament and in the partner portal dashboard.
- Automated onboarding reminders: scheduled job sends email reminders to partners who have outstanding onboarding tasks older than 7 days. Escalation email to the partner manager if overdue > 14 days.
- Tax form collection: partner uploads W-9 (US) or W-8-BEN (international) via the portal. File stored via spatie/laravel-media-library and linked to the `partner_onboarding_tasks` record. Finance team can view in Filament.
- Custom onboarding checklists: company can create different checklist templates per partner type (reseller gets 6 tasks; affiliate gets 3 tasks). Default task sets configurable in Filament.

### AI-Powered
- Application quality scoring: Claude reads the application text fields and scores the application quality (0–100) based on company size signals, clarity of customer base description, and alignment with the company's ideal partner profile. Score surfaced as a sorting signal in the Filament application list — not shown to applicants.
- Welcome message personalisation: AI drafts a personalised welcome email for newly approved partners incorporating their company name, partner type, and stated goals from the application

## Data Model

```erDiagram
    partner_applications {
        ulid id PK
        ulid company_id FK
        string company_name
        string contact_name
        string contact_email
        string contact_phone
        string website
        string country
        string type
        text motivation
        string annual_revenue_range
        text customer_base_description
        string status
        integer ai_quality_score
        timestamp submitted_at
        ulid reviewed_by FK
        timestamp reviewed_at
        string rejection_reason
        ulid partner_id FK
        timestamps created_at/updated_at
    }

    partner_onboarding_tasks {
        ulid id PK
        ulid partner_id FK
        string task_type
        string title
        string description
        date due_date
        boolean is_required
        string status
        timestamp completed_at
        ulid completed_by FK
        ulid related_id
        string related_type
        timestamps created_at/updated_at
    }

    partner_certifications {
        ulid id PK
        ulid partner_id FK
        ulid partner_user_id FK
        string name
        string level
        ulid lms_assessment_id FK
        timestamp issued_at
        timestamp expires_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` on applications | pending / approved / rejected / withdrawn |
| `partner_id` on applications | populated when application is approved — FK to created `partners` record |
| `task_type` | agreement_signing / tax_form / training_track / certification / portal_setup / call_with_partner_manager |
| `status` on tasks | pending / in_progress / completed / skipped |
| `related_id` + `related_type` | polymorphic: points to e.g. the `LmsLearningPath` for a training task, or the signed document for an agreement task |
| `level` on certifications | foundation / professional / expert |

## Permissions

```
partners.onboarding.view
partners.onboarding.review-applications
partners.onboarding.manage-tasks
partners.onboarding.certifications
partners.onboarding.configure-checklists
```

## Filament

- **Resource:** `PartnerApplicationResource` — list view with status filter, AI quality score column (sortable), submitted date. Actions: approve (opens modal asking for partner type and tier confirmation) and reject (opens modal with reason text area). `PartnerOnboardingResource` — shows all partners in onboarding (not yet 100% complete) with a progress bar column. Click-through to individual partner onboarding detail view with task list.
- **Pages:** `ListPartnerApplications`, `ViewPartnerApplication`, `ListPartnerOnboarding`, `ViewPartnerOnboarding` (task checklist with mark-complete actions for admin-completable tasks like "call scheduled")
- **Custom pages:** `OnboardingChecklistConfigPage` — per-partner-type checklist template editor. Company admin defines which task types are included, whether required, and default due date offset (e.g. "tax form due within 14 days of approval"). Single config page per partner type. Class: `App\Filament\Partners\Pages\OnboardingChecklistConfigPage`.
- **Widgets:** `PendingApplicationsWidget` (count + avg days pending), `OnboardingProgressWidget` (partners by onboarding completion bucket: 0–25%, 26–75%, 76–99%, 100%) — on Partners panel dashboard
- **Nav group:** Partners (partners panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Kiflo | Partner onboarding, checklists |
| PartnerStack | Partner recruitment and onboarding |
| Alliances.io | Partner application and approval |
| Manual spreadsheets | Partner onboarding tracking |
| DocuSign (standalone) | E-signature for partner agreements |

## Related

- [[partner-portal]]
- [[deal-registration]]
- [[partner-commissions]]
- [[domains/lms/INDEX]]
- [[domains/legal/INDEX]]

## Implementation Notes

- **Public application form:** Rendered at `/partner-portal/apply` via Inertia — no authentication required. CSRF protected. Rate-limited to 5 submissions per IP per hour. On submit: creates `partner_applications` record, sends acknowledgement email to applicant, sends Filament notification to partner manager users ("New partner application from [company_name]").
- **Approval process:** `ApprovePartnerApplication` action in Filament creates the `partners` record, generates the onboarding task checklist from the configured template for that partner type, sends the portal invite email to the contact email, and dispatches `PartnerApproved` event for any other listeners (e.g. LMS training assignment).
- **Task checklist generation:** `OnboardingChecklistGenerator` service reads the configured template for the partner type from `partner_portal_configs` (or a separate `onboarding_checklist_templates` table), instantiates each task with calculated due dates, and bulk-inserts into `partner_onboarding_tasks`. For training track tasks: calls `LmsService::assignLearningPath($partnerId, $learningPathId)`.
- **E-signature integration:** Depends on the Legal domain's e-signature integration module being active. `AgreementSigningTask` dispatches a `SendDocumentForSignature` event handled by the Legal domain's e-signature service (DocuSign or Dropbox Sign adapter). Webhook callback from the e-signature provider at `/webhooks/esignature/{provider}` updates the task status to completed.
- **Completion score calculation:** Computed real-time on `ViewPartnerOnboarding` page and cached in `partners.onboarding_score` (integer 0–100) updated via `PartnerOnboardingTaskObserver::updated()` whenever a task status changes. Score = `(completed_task_count / total_required_task_count) * 100` rounded down.
