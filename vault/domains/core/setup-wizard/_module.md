---
domain: core
module: setup-wizard
type: module
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Setup Wizard

First-login onboarding wizard for the new company **owner** (revamped 2026-06-20). A guided 6-step flow —
welcome/owner → company profile → localisation → **choose modules** → team & roles → finish — completing in
~5 minutes and ending on the [[../workspace-hub/_module|Workspace Hub]]. Custom Filament page in `/app`,
**not** a public Vue flow (see [[decisions]]). Owns no tables: completion tracked on `companies.setup_completed_at`.

- **module-key:** `core.setup` · **panel:** app · **priority:** v1
- **fires-events:** none · **consumes-events:** none

## Sibling notes

- [[architecture]] — wizard steps, `CompleteSetupAction`, redirect middleware + flow
- [[security]] — owner-only, page hidden after completion
- Features: [[features/onboarding-steps]]

(No `data-model` note — the module owns no tables; no `api` note — steps reuse other modules' DTOs.)

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../company-settings/_module]] | steps 2–3 write settings |
| Hard | [[../module-marketplace/_module]] | step 4 activates modules (drives hub + RBAC) |
| Hard | [[../invitation-system/_module]] | step 5 sends invites |
| Hard | [[../rbac/_module]] | step 5 role assignment (module-scoped) |
| Hard | [[../workspace-hub/_module]] | step 6 lands the owner on the hub |
| Soft | [[../two-factor-auth/_module]] | step 1 optional 2FA enrol |
| Soft | core.files | logo upload in step 2 |

## Core Features (revamped 2026-06-20)

Guided, resumable, **6-step** flow for the company **owner** on first login (while
`companies.setup_completed_at` is null). Progress rail, per-step validation, a "why this matters" hint per
step. Non-owners never see it. Ends by dropping the owner on the [[../workspace-hub/_module|Workspace Hub]].

| # | Step | Does | Skip? |
|---|---|---|---|
| 1 | **Welcome & owner** | Confirm owner; prompt strong password + enable 2FA ([[../two-factor-auth/_module]]) | no |
| 2 | **Company profile** | Name, logo, primary colour, industry, size → [[../company-settings/_module]] | no |
| 3 | **Localisation** | Timezone, locale, base currency, fiscal-year start, number/date format | no |
| 4 | **Choose modules** | Marketplace quick-pick — **pivotal**: activation decides the hub's domain tiles **and** RBAC's assignable permissions ([[../rbac/features/module-scoped-permissions]]) | defer (core stays on) |
| 5 | **Team & roles** | Invite members ([[../invitation-system/_module]]); assign roles limited to the step-4 modules | yes |
| 6 | **Finish** | Summary → set `setup_completed_at` → land on the Workspace Hub | — |

- Progress saved between steps (resume at first incomplete step); back-navigation to revisit.
- Step 4 is the linchpin — it feeds both the hub and RBAC. Owner-only; hidden once complete.

## Test Checklist

- [ ] Owner first login redirects to wizard; non-owner does not
- [ ] Completed wizard (`setup_completed_at` set) never redirects again
- [ ] Step 1–2 persist into settings classes
- [ ] Step 3 sends invitations (queued)
- [ ] Step 4 activates chosen module via BillingService
- [ ] Skip on steps 3/4 completes wizard without invites/module
- [ ] Resume lands on first incomplete step

## Build Manifest (corrected to flat paths)

```
app/Filament/App/Pages/SetupWizardPage.php
resources/views/filament/app/pages/setup-wizard.blade.php
app/Actions/CompleteSetupAction.php
app/Http/Middleware/RedirectToSetupWizard.php
tests/Feature/Core/SetupWizardTest.php
```

> [!note]
> Spec listed `app/Actions/Core/CompleteSetupAction.php`; real layout is flat — corrected above. `app/Filament/...`, `resources/views/...`, `app/Http/Middleware/...`, and `tests/Feature/Core/...` kept as-is.

## Related

- [[../company-settings/_module]]
- [[../module-marketplace/_module]]
- [[../invitation-system/_module]] — team invites sent from step 3
- [[decisions]]
