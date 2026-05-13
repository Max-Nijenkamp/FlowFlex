---
type: module
domain: Core Platform
panel: app
module-key: core.setup
status: planned
color: "#4ADE80"
---

# Setup Wizard

> First-login multi-step wizard that takes a new company from blank workspace to operational in under ten minutes — company profile, team invite, module selection, and settings.

**Panel:** `app`
**Module key:** `core.setup`

## What It Does

The Setup Wizard is shown automatically on first login after a company is created by FlowFlex staff. It walks the company owner through five steps: confirm company profile details, upload branding, invite initial team members, activate the first modules from the marketplace, and review workspace settings. Each step is a Filament Wizard page. The wizard can be dismissed and resumed later from a dashboard banner. Once all steps are marked complete, the wizard is hidden and the company is marked `onboarded`.

## Features

### Core
- Five-step Filament Wizard: (1) Company Profile — name, timezone, locale, currency; (2) Branding — logo, favicon, primary colour; (3) Invite Team — add up to 10 email addresses with role assignments; (4) Activate Modules — browse module catalog, toggle desired modules; (5) Review & Go Live — summary + launch button
- Step completion persisted to `company_setup_steps` so the wizard resumes at the last incomplete step
- "Skip for now" on each step — wizard dismissable but accessible again from dashboard banner
- Dashboard banner shown until all steps complete and owner clicks "dismiss permanently"
- Company `onboarded_at` timestamp set when owner clicks launch on step 5

### Advanced
- Team invite step: sends invite emails immediately on step save; invitees can join before wizard is completed
- Module activation step links to the same catalog used by the Module Marketplace — no duplicate UI
- Branding step triggers the Vite/Tailwind CSS recompile job if primary colour changes
- Wizard page accessible at `/app/setup` for manual return; nav link hidden after `onboarded_at` is set
- Audit log entry created for each completed step: `company.setup.step-completed` with step name

### AI-Powered
- Smart defaults: locale, currency, and timezone pre-filled based on company country (set during admin creation of the company)
- Suggested modules: based on company size and industry (set during admin creation), the module activation step highlights recommended modules first

## Data Model

```erDiagram
    company_setup_steps {
        ulid id PK
        ulid company_id FK "unique"
        boolean step_profile
        boolean step_branding
        boolean step_team
        boolean step_modules
        boolean step_review
        timestamp completed_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `step_profile` | True once company profile step saved |
| `step_branding` | True once branding step saved |
| `step_team` | True once at least one invite sent |
| `step_modules` | True once at least one module activated |
| `step_review` | True once launch button clicked |
| `completed_at` | Set when all five steps are true |

## Permissions

- `core.setup.view`
- `core.setup.complete-steps`
- `core.setup.invite-team`
- `core.setup.activate-modules`
- `core.setup.dismiss`

## Filament

- **Resource:** None
- **Pages:** `SetupWizardPage` — five-step Filament Wizard at `/app/setup`
- **Custom pages:** `SetupWizardPage`
- **Widgets:** `SetupProgressWidget` — dashboard banner with progress bar shown until wizard complete
- **Nav group:** Hidden (auto-redirected on first login, manual access via dashboard banner)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Notion | Initial workspace configuration wizard |
| Monday.com | Onboarding checklist and team setup flow |
| HubSpot | CRM onboarding wizard |
| Intercom | Product tour and onboarding checklist |

## Implementation Notes

**Filament Wizard:** `SetupWizardPage` uses Filament's native `HasWizard` trait on a custom `Page` class (Filament 5 supports wizard steps natively via `Steps\Step` components within a `Wizard` form component on a Page). Each step is a `Steps\Step` with its own form schema. The wizard persists step completion to `company_setup_steps` after each `nextStep()` action — not only at final submission. This allows resume-from-any-step on next login.

**Branding step — Vite recompile:** When the primary colour changes in step 2, a `BrandingUpdatedJob` is dispatched. In production, this job triggers a Vite build via `Process::run('npm run build --filter=filament-theme')` on the server — this is an expensive operation. For MVP, accept that the custom theme colour requires a manual deployment after branding is set. Flag this as a known limitation. For Phase 2, implement a CSS custom property override approach where the primary colour is injected as a CSS variable in the panel layout, bypassing the Vite build step entirely.

**Team invite step:** Invitations are sent via `NewUserInviteNotification` dispatched through the `notifications` module. The invite contains a signed URL (`URL::temporarySignedRoute('invite.accept', 3600*24*7, ...)`) that sets the invitee's password and activates their account. This requires the `users` table to have an `invite_token` and `invite_expires_at` column — verify these exist in the Foundation module.

**Module activation step:** The step renders the Module Marketplace catalog in a compact toggle layout. The same `BillingService::activateModule($moduleKey)` method used in the marketplace is called here — no duplicate UI logic.

**Dashboard banner:** `SetupProgressWidget` is a standard Filament `Widget` class added to the `app` panel's dashboard. It checks `company_setup_steps` for the current company and renders a progress bar with a "Continue setup" button. The widget returns `null` (empty) once `company_setup_steps.completed_at` is set and the owner has dismissed it — store dismiss state in `company_setup_steps.banner_dismissed_at`.

**AI features:** Smart defaults for locale/currency/timezone are pure PHP lookups against a country→defaults mapping array (no LLM required). Suggested modules are driven by a PHP scoring function that reads `companies.industry` and `companies.headcount_range` against a hardcoded module recommendation matrix.

## Related

- [[company-settings]]
- [[module-marketplace]]
- [[notifications]]
- [[billing-engine]]
