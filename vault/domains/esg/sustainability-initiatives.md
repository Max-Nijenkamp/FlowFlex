---
type: module
domain: ESG & Sustainability
panel: esg
module-key: esg.initiatives
status: planned
color: "#4ADE80"
---

# Sustainability Initiatives

> Sustainability project tracking — goal, owner, milestones, budget, and impact measurement against ESG KPIs.

**Panel:** `esg`
**Module key:** `esg.initiatives`

---

## What It Does

Sustainability Initiatives tracks the operational projects the company undertakes to improve its ESG performance — installing solar panels, switching to electric vehicle fleet, launching a volunteering programme, achieving ISO 14001 certification. Each initiative has a stated goal, an owner, a budget, milestones, and is linked to the ESG KPIs it is expected to move. Progress is tracked through milestone completion and KPI movement, giving sustainability teams a clear picture of whether their programmes are delivering measurable impact.

---

## Features

### Core
- Initiative creation: name, description, sustainability dimension (E/S/G), goal statement, owner, start/end date
- Milestone tracking: ordered milestones with due dates, status, and completion notes
- Budget tracking: allocated budget and actual spend against the initiative
- KPI linkage: link one or more ESG KPIs to the initiative to track expected impact
- RAG status: overall initiative health based on milestone completion vs timeline
- Progress updates: regular notes field for the owner to log progress narrative

### Advanced
- Impact projection: define the expected KPI improvement attributable to this initiative on completion
- Initiative portfolio view: all active sustainability initiatives with owner, status, and budget health
- UN SDG mapping: tag initiatives to relevant UN Sustainable Development Goals
- Evidence upload: attach documents, certifications, or photos as evidence of progress
- Stakeholder visibility: optionally mark initiatives as visible in external stakeholder reports

### AI-Powered
- Similar initiative library: suggest initiatives adopted by similar-profile companies in the same industry
- Milestone delay risk: flag initiatives where milestone completion rate suggests timeline slippage
- Impact confidence scoring: estimate how reliably a completed initiative will achieve its projected KPI improvement

---

## Data Model

```erDiagram
    sustainability_initiatives {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string dimension
        text goal
        ulid owner_id FK
        decimal allocated_budget
        decimal actual_spend
        string currency
        date start_date
        date end_date
        string rag_status
        json un_sdg_tags
        timestamps created_at_updated_at
    }

    initiative_milestones {
        ulid id PK
        ulid initiative_id FK
        string title
        date due_date
        string status
        text completion_notes
    }

    initiative_kpi_links {
        ulid id PK
        ulid initiative_id FK
        ulid kpi_id FK
        decimal expected_improvement
        string improvement_unit
    }

    sustainability_initiatives ||--o{ initiative_milestones : "has"
    sustainability_initiatives ||--o{ initiative_kpi_links : "linked to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `sustainability_initiatives` | Initiative records | `id`, `company_id`, `name`, `dimension`, `owner_id`, `allocated_budget`, `rag_status` |
| `initiative_milestones` | Milestone steps | `id`, `initiative_id`, `title`, `due_date`, `status` |
| `initiative_kpi_links` | KPI connections | `id`, `initiative_id`, `kpi_id`, `expected_improvement` |

---

## Permissions

```
esg.initiatives.view
esg.initiatives.create
esg.initiatives.update
esg.initiatives.delete
esg.initiatives.manage-milestones
```

---

## Filament

- **Resource:** `App\Filament\Esg\Resources\SustainabilityInitiativeResource`
- **Pages:** `ListSustainabilityInitiatives`, `CreateSustainabilityInitiative`, `EditSustainabilityInitiative`, `ViewSustainabilityInitiative`
- **Custom pages:** `InitiativePortfolioPage`, `ImpactDashboardPage`
- **Widgets:** `ActiveInitiativesWidget`, `MilestoneDueWidget`, `ImpactSummaryWidget`
- **Nav group:** Social

---

## Displaces

| Feature | FlowFlex | Plan A | Watershed | Salesforce Net Zero |
|---|---|---|---|---|
| Initiative project tracking | Yes | Yes | No | Yes |
| KPI impact linkage | Yes | No | No | No |
| UN SDG mapping | Yes | Yes | No | Yes |
| AI similar initiative library | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[esg-kpis]] — initiatives are linked to the KPIs they aim to improve
- [[carbon-footprints]] — carbon reduction initiatives tracked here
- [[esg-reports]] — initiatives included in framework reports
- [[stakeholder-reporting]] — public-facing initiative summaries
