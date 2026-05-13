---
type: module
domain: Customer Success
panel: cs
module-key: cs.plans
status: planned
color: "#4ADE80"
---

# Success Plans

> Shared customer success plans — goals, milestones, owner assignment, and customer-visible progress tracking.

**Panel:** `cs`
**Module key:** `cs.plans`

---

## What It Does

Success Plans provides a structured, co-owned document between the CSM and the customer that defines what success looks like for this engagement. The CSM creates a plan with shared goals (e.g. reduce invoice processing time by 50%), milestones to achieve those goals, and the owner responsible for each. The plan is shared with designated customer contacts via a secure link, so both sides can see progress in real time. This transparency strengthens the relationship and provides a shared reference point for QBRs and renewal conversations.

---

## Features

### Core
- Success plan creation: linked to an account, with goals, time horizon, and plan owner
- Goal definition: goal statement, success metric, target value, and measurement method
- Milestones: ordered steps contributing to each goal with due date, owner (internal or customer), and status
- Customer sharing: generate a secure link for the customer to view and comment on the plan
- Progress tracking: milestone completion drives overall plan progress percentage
- CSM notes: internal notes on plan health not visible to the customer

### Advanced
- Plan templates: pre-built success plan structures for common customer types or product use cases
- Co-editing: customer contacts can update milestones they own directly via the shared link
- QBR export: generate a formatted PDF of the plan for quarterly business review use
- Version history: maintain previous versions of the plan as it evolves through the customer lifecycle
- Renewal linkage: mark a plan as underpinning the upcoming renewal; visible in the renewal opportunity

### AI-Powered
- Goal suggestion: AI recommends common goals for the account's industry and product usage pattern
- Progress risk: flag plans where milestone completion rate is lagging behind the time horizon
- Summary generation: AI drafts the QBR plan summary section from milestone completion and notes

---

## Data Model

```erDiagram
    success_plans {
        ulid id PK
        ulid account_id FK
        ulid company_id FK
        ulid csm_id FK
        string title
        date start_date
        date target_date
        string status
        string share_token
        datetime share_expires_at
        timestamps created_at_updated_at
    }

    success_plan_goals {
        ulid id PK
        ulid plan_id FK
        string goal_statement
        string success_metric
        decimal target_value
        string measurement_method
    }

    success_plan_milestones {
        ulid id PK
        ulid plan_id FK
        ulid goal_id FK
        string title
        date due_date
        string owner_type
        ulid owner_id FK
        string status
        text completion_notes
    }

    success_plans ||--o{ success_plan_goals : "has"
    success_plan_goals ||--o{ success_plan_milestones : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `success_plans` | Plan records | `id`, `account_id`, `csm_id`, `title`, `start_date`, `target_date`, `share_token` |
| `success_plan_goals` | Goals within a plan | `id`, `plan_id`, `goal_statement`, `target_value` |
| `success_plan_milestones` | Milestones per goal | `id`, `plan_id`, `goal_id`, `title`, `due_date`, `owner_type`, `status` |

---

## Permissions

```
cs.plans.view-any
cs.plans.create
cs.plans.update
cs.plans.share-with-customer
cs.plans.export
```

---

## Filament

- **Resource:** `App\Filament\Cs\Resources\SuccessPlanResource`
- **Pages:** `ListSuccessPlans`, `CreateSuccessPlan`, `EditSuccessPlan`, `ViewSuccessPlan`
- **Custom pages:** `CustomerPlanViewPage` (unauthenticated customer view), `QbrExportPage`
- **Widgets:** `PlansAtRiskWidget`, `MilestonesDueWidget`
- **Nav group:** Accounts

---

## Displaces

| Feature | FlowFlex | Gainsight | ChurnZero | Google Docs |
|---|---|---|---|---|
| Structured success plans | Yes | Yes | No | Manual |
| Customer-visible sharing | Yes | Yes | No | Manual |
| Milestone tracking | Yes | Yes | No | Manual |
| AI goal suggestions | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[health-scores]] — plan progress is a health score input
- [[playbooks]] — renewal playbook tasks reference the success plan
- [[churn-risk]] — plans behind schedule flag as churn risk
- [[onboarding-tracking]] — onboarding milestones link to the success plan
