---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.setup
status: planned
priority: v1
depends-on: [core.settings, core.invitations, core.marketplace]
soft-depends: [core.files]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: core.setup
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Setup Wizard

First-login onboarding wizard for new company owners. Multi-step flow covering workspace identity, locale settings, team invitations, and first module selection. Completes in ~5 minutes. A custom Filament page in `/app` — NOT a public Vue flow ([[build/decisions/decision-2026-06-10-no-public-registration]]).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/company-settings\|core.settings]] | steps 1–2 write settings |
| Hard | [[domains/core/invitation-system\|core.invitations]] | step 3 sends invites |
| Hard | [[domains/core/module-marketplace\|core.marketplace]] | step 4 activates first module |
| Soft | [[domains/core/file-storage\|core.files]] | logo upload in step 1 |

---

## Core Features

- Triggered on first login for users with `owner` role and incomplete setup
- Multi-step wizard: Identity → Locale → Team → First Module
- Step 1 — Identity: company name, upload logo, set primary color
- Step 2 — Locale: timezone, locale, base currency, fiscal year start
- Step 3 — Team: invite team members by email with role assignment
- Step 4 — First Module: module marketplace quick-start (select first paid module to activate)
- Progress saved between steps — resume if user closes the wizard
- Skip available on steps 3 and 4
- Wizard state tracked on `companies.setup_completed_at`

---

## Data Model

| Column (on `companies`) | Notes |
|---|---|
| `setup_completed_at` | null = wizard not completed, timestamp = done |

Step progress: Livewire page state persisted per session; resume restarts at first incomplete step computed from settings/invites presence *(assumed — no extra table)*.

---

## DTOs

Steps reuse the owning modules' inputs: settings classes (steps 1–2), `CreateInvitationData` (step 3), `ActivateModuleData` (step 4). No wizard-specific DTOs.

## Services & Actions

- `CompleteSetupAction::run(): void` — sets `setup_completed_at`, redirects to dashboard
- Redirect middleware: owner with `setup_completed_at = null` hitting `/app` → wizard *(assumed: middleware on app panel)*

---

## Filament

**Nav group:** hidden (`$shouldRegisterNavigation = false`)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SetupWizardPage` | #7 multi-step wizard custom page | Filament Wizard component, step progress indicator, skip on 3–4 |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('core.setup.view-any') && BillingService::hasModule('core.setup')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`core.setup.complete` — owner role only; page invisible to other roles and after completion.

---

## Test Checklist

- [ ] Owner first login redirects to wizard; non-owner does not
- [ ] Completed wizard (`setup_completed_at` set) never redirects again
- [ ] Step 1–2 persist into settings classes
- [ ] Step 3 sends invitations (queued)
- [ ] Step 4 activates chosen module via BillingService
- [ ] Skip on steps 3/4 completes wizard without invites/module
- [ ] Resume lands on first incomplete step

---

## Build Manifest

```
app/Filament/App/Pages/SetupWizardPage.php
resources/views/filament/app/pages/setup-wizard.blade.php
app/Actions/Core/CompleteSetupAction.php
app/Http/Middleware/RedirectToSetupWizard.php
tests/Feature/Core/SetupWizardTest.php
```

---

## Related

- [[domains/core/company-settings]]
- [[domains/core/module-marketplace]]
- [[domains/core/invitation-system]] — team invites sent from wizard step 3
- [[build/decisions/decision-2026-06-10-no-public-registration]]
