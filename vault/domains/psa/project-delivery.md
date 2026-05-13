---
type: module
domain: Professional Services (PSA)
panel: psa
module-key: psa.delivery
status: planned
color: "#4ADE80"
---

# Project Delivery

> Client project delivery tracking — phases, deliverables, milestone acceptance, and formal client sign-off.

**Panel:** `psa`
**Module key:** `psa.delivery`

---

## What It Does

Project Delivery is the operational centre of the PSA panel, tracking the lifecycle of each client engagement from kick-off to closure. Delivery managers define project phases and deliverables, assign owners, and record milestone completions. Clients can be invited to a read-only view to review deliverable status and formally sign off on completed milestones. The status is linked to billing — sign-off on a deliverable can trigger invoice generation in the Time & Billing module.

---

## Features

### Core
- Client project creation: linked to a CRM account, project type, start and end date, contract value
- Phase definition: ordered project phases with name, description, and target date
- Deliverable tracking: deliverables within each phase with owner, due date, and status
- Milestone acceptance: client-facing confirmation step where deliverable is formally accepted
- Project status: overall RAG status (on track, at risk, delayed) with last-updated note
- Project dashboard: portfolio view of all active projects with status, phase, and budget health

### Advanced
- Project templates: pre-built phase and deliverable templates for common engagement types
- Dependency tracking: mark one deliverable as dependent on the completion of another
- Change log: record scope changes, timeline extensions, and their budget impact
- Client portal view: read-only view for the client contact to see project status and deliverables
- Risk log: project-level risk register linked to specific phases

### AI-Powered
- Delivery risk scoring: AI flags projects at risk of missing milestones based on current completion rate vs timeline
- Scope creep detection: analyse change log entries and flag when scope additions are approaching a threshold
- Project summary generation: AI drafts a plain-language project status update from structured data

---

## Data Model

```erDiagram
    psa_projects {
        ulid id PK
        ulid company_id FK
        ulid client_account_id FK
        string name
        string project_type
        date start_date
        date end_date
        decimal contract_value
        string rag_status
        string status
        timestamps created_at_updated_at
    }

    psa_phases {
        ulid id PK
        ulid project_id FK
        string name
        integer sort_order
        date target_date
        string status
    }

    psa_deliverables {
        ulid id PK
        ulid phase_id FK
        ulid owner_id FK
        string title
        text description
        date due_date
        string status
        boolean client_accepted
        timestamp accepted_at
    }

    psa_projects ||--o{ psa_phases : "has"
    psa_phases ||--o{ psa_deliverables : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `psa_projects` | Project records | `id`, `company_id`, `client_account_id`, `contract_value`, `rag_status`, `status` |
| `psa_phases` | Project phases | `id`, `project_id`, `name`, `target_date`, `status` |
| `psa_deliverables` | Deliverables | `id`, `phase_id`, `owner_id`, `due_date`, `status`, `client_accepted` |

---

## Permissions

```
psa.delivery.view-any
psa.delivery.create
psa.delivery.update
psa.delivery.delete
psa.delivery.manage-client-access
```

---

## Filament

- **Resource:** `App\Filament\Psa\Resources\PsaProjectResource`
- **Pages:** `ListPsaProjects`, `CreatePsaProject`, `EditPsaProject`, `ViewPsaProject`
- **Custom pages:** `ProjectPortfolioPage`, `ClientPortalPage`
- **Widgets:** `ProjectRAGWidget`, `DeliverablesDueWidget`
- **Nav group:** Delivery

---

## Displaces

| Feature | FlowFlex | Teamwork | Mavenlink | Asana (PSA) |
|---|---|---|---|---|
| Phase and deliverable tracking | Yes | Yes | Yes | Partial |
| Client acceptance workflow | Yes | No | Yes | No |
| AI delivery risk scoring | Yes | No | No | No |
| Native billing integration | Yes | Partial | Yes | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[resource-planning]] — resources allocated across project phases
- [[time-billing]] — time logged against project deliverables
- [[client-reporting]] — status reports built from project data
- [[profitability]] — budget vs actual tracked at project level
