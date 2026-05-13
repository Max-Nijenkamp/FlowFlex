---
type: module
domain: Core Platform
panel: app
cssclasses: domain-admin
phase: 1
status: complete
migration_range: 010001–019999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# Setup Wizard & Guided Onboarding

A 5-step first-login wizard that guides a new company owner through the minimum configuration required before using FlowFlex. Appears automatically after account creation. Once all steps are completed the wizard is permanently hidden.

**Panel:** `app`  
**Phase:** 1 — required before any domain module is useful to a new tenant

---

## Features

### Step Flow

| Step | Key | Title | Links to |
|------|-----|-------|---------|
| 1 | `welcome` | Welcome to FlowFlex | — |
| 2 | `company` | Set up your company | Company & Workspace Settings |
| 3 | `team` | Invite your team | User Invitations |
| 4 | `modules` | Choose your modules | Module Marketplace |
| 5 | `branding` | Add your branding | Branding section of Company Settings |

- Each step renders an icon, title, description, and a tappable shortcut card that deep-links to the relevant settings page
- Progress bar shows numbered circles with connecting lines; completed steps turn green
- CTA button label changes per step: "Get started" → "Continue" → "Finish setup"
- Done state: centered success icon + "Go to dashboard" button

### Progress Persistence
- Progress is stored in `setup_wizard_progress` per company (one row, unique `company_id`)
- `completed_steps` JSON array tracks which step keys have been completed
- `current_step` string tracks where the user left off
- On re-visit the wizard resumes from `current_step`

### Access Guard
- `canAccess()` returns `false` once `completed = true` — wizard disappears from navigation
- Always accessible to company owners during onboarding regardless of permissions (no permission gate)

### Filament Implementation
- `app/Filament/App/Pages/SetupWizard.php`
- `getStepConfig()` returns icon, label, title, description per step key
- `mount()` loads or creates `SetupWizardProgress` for the current company
- `completeStep(string $step)` calls `progress->completeStep($step)` then advances `current_step`
- `getView()` method (not static `$view` property — PHP static/non-static conflict in Filament 5)
- Blade template: `resources/views/filament/app/pages/setup-wizard.blade.php`

---

## Data Model

```erDiagram
    setup_wizard_progress {
        ulid id PK
        ulid company_id FK "unique"
        string current_step
        json completed_steps
        boolean completed
        timestamp completed_at
        timestamps created_at/updated_at
    }
```

### Model Methods

- `steps(): array` — returns all 5 step keys in order
- `hasStep(string $step): bool` — checks if `$step` is in `completed_steps`
- `completeStep(string $step): void` — appends step to `completed_steps`, advances `current_step`, sets `completed = true` and `completed_at` if all steps done

---

## Permissions

None — the wizard is always accessible to the authenticated company owner during onboarding. No permission gate required.

---

## Related

- [[MOC_CorePlatform]]
- [[company-workspace-settings]] — Step 2 and 5 link here
- [[rbac-management-ui]] — Step 3 depends on invite/user management
- [[module-billing-engine]] — Step 4 links to module marketplace
