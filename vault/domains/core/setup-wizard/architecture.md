---
domain: core
module: setup-wizard
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Setup Wizard — Architecture

Parent: [[_module]] · See also [[security]]

## Page

`SetupWizardPage` (`/app`, custom Filament page using the Filament Wizard component) with a step progress indicator. Navigation is hidden (`$shouldRegisterNavigation = false`); the page is reached only via redirect middleware.

## Steps

| Step | Writes to | Reuses DTO |
|---|---|---|
| 1 — Identity | company settings (name, logo, primary color) | company settings classes |
| 2 — Locale | company settings (timezone, locale, currency, fiscal year) | company settings classes |
| 3 — Team | invitations (skippable) | `CreateInvitationData` ([[../invitation-system/_module]]) |
| 4 — First Module | module activation (skippable) | `ActivateModuleData` ([[../module-marketplace/_module]]) |

No wizard-specific DTOs — each step reuses the owning module's inputs.

## Completion + resume

- `CompleteSetupAction::run(): void` — sets `companies.setup_completed_at`, redirects to the dashboard.
- Step progress is Livewire page state persisted per session; resume restarts at the first incomplete step, computed from the presence of settings/invites *(assumed — no extra table)*.

## Redirect middleware

`RedirectToSetupWizard` — an owner whose `companies.setup_completed_at` is null hitting `/app` is redirected to the wizard *(assumed: registered on the app panel)*.

## Filament Artifacts

**Nav group:** (none — navigation hidden; reached only via redirect middleware)

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SetupWizardPage` | #7 Multi-step wizard custom page | [[../../../architecture/patterns/page-blueprints#Wizard]] | `$shouldRegisterNavigation = false`; 6-step Filament Wizard; Livewire session-state resume; each step reuses the owning module's DTO |

**Access contract (mandatory):** `SetupWizardPage` is a custom page and MUST state its gate explicitly — Filament does not auto-gate custom pages:
`canAccess() = Auth::user()->can('core.setup.complete') && Auth::user()->isOwner() && is_null(company()->setup_completed_at)`
per [[../../../architecture/filament-patterns]] #1. `core.setup` is a platform module (always active), so there is **no** `hasModule()` gate — the page instead disappears once `setup_completed_at` is set. Owner-only; non-owners never see it. See [[security]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Setup completion (`CompleteSetupAction` sets `companies.setup_completed_at`) | n/a | Single-owner, one-shot idempotent flag — only the owner runs their own wizard; a re-complete is a no-op once the timestamp is set |
| Step writes (settings / invites / module activation) | n/a (delegated) | Each write goes through the owning module's service/action ([[../company-settings/_module]], [[../invitation-system/_module]], [[../module-marketplace/_module]]); that module owns the concurrency tier — the wizard owns no tables of its own |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Flow

```mermaid
flowchart TD
    Login[Owner first login] --> MW[RedirectToSetupWizard]
    MW -->|setup_completed_at null| Wiz[SetupWizardPage]
    MW -->|already set| Dash[/app dashboard]
    Wiz --> S1[1 Identity → settings]
    S1 --> S2[2 Locale → settings]
    S2 --> S3[3 Team → CreateInvitationData]
    S3 --> S4[4 First Module → ActivateModuleData]
    S3 -.skip.-> Done
    S4 -.skip.-> Done
    S4 --> Done[CompleteSetupAction]
    Done --> Set[companies.setup_completed_at set] --> Dash
```
