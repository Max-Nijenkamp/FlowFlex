---
type: module
domain: Core Platform
panel: app
module-key: core.setup
status: planned
color: "#4ADE80"
---

# Setup Wizard

First-login onboarding wizard for new company owners. Multi-step flow covering workspace identity, locale settings, team invitations, and first module selection. Completes in ~5 minutes.

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

---

## Filament

**`/app` panel:**
- `SetupWizardPage` (custom page) — multi-step form with step progress indicator

---

## Related

- [[domains/core/company-settings]]
- [[domains/core/module-marketplace]]
- [[frontend/_index]] — Onboarding section for the public Vue registration flow
