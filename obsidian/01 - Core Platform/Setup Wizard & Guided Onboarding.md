---
tags: [flowflex, domain/core, onboarding, setup-wizard, phase/1]
domain: Core Platform
panel: workspace
color: "#0F172A"
status: planned
last_updated: 2026-05-08
---

# Setup Wizard & Guided Onboarding

The zero-consultant setup experience. A new company signs up → answers 7 questions about their business → FlowFlex configures the right modules, imports their data, and walks their team through the first week. No professional services required. No "implementation partner" needed.

**Who uses it:** Company owners, admins, new team members
**Filament Panel:** `workspace` (super-admin); Vue + Inertia (setup flow)
**Depends on:** Core, all domain modules
**Phase:** 1

---

## Features

### Company Setup Wizard

6-step wizard shown on first login after account creation:

**Step 1 — Company basics**
- Company name, industry (dropdown: Technology, Retail, Professional Services, Healthcare, Manufacturing, Other)
- Company size: 1–10, 11–50, 51–200, 201–1000, 1000+
- Country and timezone (auto-detected, editable)
- Financial year start month

**Step 2 — What are you replacing?**
- Multi-select checkboxes: BambooHR, Xero, QuickBooks, HubSpot, Salesforce, Jira, Notion, Slack, Zoom, DocuSign, Other
- Or: "Building from scratch — we don't use these tools yet"
- System maps checked tools → recommended module set

**Step 3 — Recommended modules**
- Shows curated module set based on steps 1–2
- User can add/remove modules from the recommendation
- Shows cost impact per module change (live pricing)
- Module categories grouped by "Start now" vs "Add later"

**Step 4 — Invite your team**
- Add up to 5 team members in wizard (more later)
- Assign role: Admin, Manager, Employee
- Toggle: "send welcome email now" or "invite later"

**Step 5 — Data import**
- Quick imports available:
  - Employees: CSV upload with column mapper
  - Contacts/Companies: CSV or connect HubSpot/Salesforce
  - Products: CSV upload
- Or: "I'll add data manually — skip this step"

**Step 6 — Done!**
- Confetti animation
- 3 "Start here" quick actions surfaced based on imported data
- Link to: video walkthrough, help docs, book a free onboarding call

### Interactive Checklist (Post-Setup)

- Persistent checklist visible on dashboard until all items complete
- Checklist items adapt to activated modules:
  - HR: "Add your first employee" / "Set up leave policies"
  - Finance: "Connect your bank account" / "Create your first invoice"
  - CRM: "Import your contacts" / "Set up your pipeline"
  - Projects: "Create your first project"
- Progress: "6 of 12 tasks complete — you're 50% set up"
- Completion reward: celebrate with a "Your workspace is ready!" message + remove checklist

### In-App Onboarding Tooltips

- Feature discovery tooltips on first visit to each module
- Dismissible: "Got it" or "Don't show again"
- Reset: admin can reset tooltips for new team members
- Context-aware: different tips for different roles (manager vs employee)

### Module Activation Flow

- When activating a new module (after setup):
  - Module-specific mini wizard (2–3 screens)
  - Example: activating Payroll → set pay period, currency, first pay date
  - Example: activating CRM → set pipeline stages, import contacts
  - Sensible defaults provided for every setting
- Guided first action: "Great — now create your first [thing]" with pre-filled template

### Data Import Hub

- Accessible from any module's settings
- Importers per module: Employees, Contacts, Products, Invoices, Projects, Assets
- Universal CSV mapper: drag column headers to match FlowFlex fields
- Validation: flag rows with errors before import, downloadable error report
- History: log of all past imports with row counts and error rates
- Re-import: fix your CSV and re-import without losing previous clean data

### Onboarding Progress Dashboard (Admin)

- Shows: setup wizard completion %, team member activation status, module usage
- Team activation: who has logged in, who hasn't
- First-week engagement: which modules each team member has used
- Nudge: one-click send reminder to team members who haven't logged in yet

---

## Database Tables (2)

### `workspace_setup_state`
| Column | Type | Notes |
|---|---|---|
| `company_id` | ulid FK | |
| `wizard_completed` | boolean | |
| `wizard_step` | integer | 0–6 |
| `industry` | string nullable | |
| `company_size` | string nullable | |
| `tools_replacing` | json | string[] |
| `checklist_items` | json | [{key, completed_at}] |
| `completed_at` | timestamp nullable | |

### `workspace_data_imports`
| Column | Type | Notes |
|---|---|---|
| `module` | string | employees, contacts, products, etc. |
| `file_id` | ulid FK | |
| `row_count_total` | integer | |
| `row_count_success` | integer | |
| `row_count_error` | integer | |
| `error_file_id` | ulid FK nullable | CSV of failed rows |
| `imported_by` | ulid FK | |
| `status` | enum | `processing`, `complete`, `failed` |

---

## Permissions

```
workspace.setup.manage
workspace.imports.create
workspace.imports.view
workspace.onboarding.view-team-progress
```

---

## Why This Is The Differentiator

Every ERP and SaaS platform fails the "zero-consultant" test:
- **Salesforce**: avg €15k implementation cost
- **SAP**: €100k+ minimum
- **HubSpot**: 6-week onboarding programme
- **BambooHR**: dedicated implementation specialist required

FlowFlex's Setup Wizard + Checklist makes self-service setup achievable in under 2 hours for most SMBs. This is a core product value, not an afterthought.

---

## Related

- [[Core Platform]]
- [[Multi-Tenancy & Workspace]]
- [[Module Billing Engine]]
- [[Roles & Permissions (RBAC)]]
- [[Authentication & Identity]]
