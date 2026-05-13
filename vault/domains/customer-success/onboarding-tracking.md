---
type: module
domain: Customer Success
panel: cs
module-key: cs.onboarding
status: planned
color: "#4ADE80"
---

# Onboarding Tracking

> Customer onboarding milestone tracking — steps, completion status, time-to-value measurement, and stall detection.

**Panel:** `cs`
**Module key:** `cs.onboarding`

---

## What It Does

Onboarding Tracking gives CSMs a structured view of where each new customer is in their onboarding journey. CS teams define onboarding milestone templates for each product or customer tier (e.g. kickoff call done, data imported, first team trained, first value event achieved), and the system tracks completion status for each new account against the template. Time-to-value — the time from contract sign to first value milestone — is measured per account and cohort. Stalled accounts that have been stuck on the same step for too long are surfaced automatically.

---

## Features

### Core
- Onboarding template creation: ordered milestone sequences with expected time-to-completion per step
- Account onboarding plan: apply a template to a new account with target milestone dates
- Milestone completion: CSM or the customer marks milestones as complete with a date and notes
- Time-to-value tracking: measure days from contract start to the designated "first value" milestone
- Progress percentage: overall onboarding completion percentage per account
- Stall detection: flag accounts that have not progressed on a milestone in more than a configurable number of days

### Advanced
- Customer self-reporting: share a checklist with the customer so they can mark their own progress
- Onboarding by segment: different onboarding templates for enterprise, mid-market, and SMB customers
- Cohort time-to-value: compare TTV across sign-up cohorts to measure onboarding programme improvement
- Escalation workflow: auto-notify the CS team lead when an account is stalled past a severity threshold
- Blockers log: record the reason for a stall (waiting for customer data, IT approval, etc.)

### AI-Powered
- Completion prediction: estimate the expected completion date for an account based on its current pace
- Risk scoring: flag accounts with a combination of late start, slow progress, and high contract value as high-priority onboarding risks
- Optimal milestone sequence: analyse historical onboarding data and recommend the most effective milestone ordering

---

## Data Model

```erDiagram
    onboarding_templates {
        ulid id PK
        ulid company_id FK
        string name
        string target_segment
        json milestones
        timestamps created_at_updated_at
    }

    account_onboarding_plans {
        ulid id PK
        ulid template_id FK
        ulid account_id FK
        ulid company_id FK
        ulid csm_id FK
        date start_date
        date target_completion_date
        integer time_to_value_days
        string status
        timestamps created_at_updated_at
    }

    onboarding_milestone_completions {
        ulid id PK
        ulid plan_id FK
        string milestone_key
        date completed_date
        text notes
        boolean is_stalled
    }

    onboarding_templates ||--o{ account_onboarding_plans : "applied as"
    account_onboarding_plans ||--o{ onboarding_milestone_completions : "tracks"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `onboarding_templates` | Milestone sequence definitions | `id`, `company_id`, `name`, `target_segment`, `milestones` |
| `account_onboarding_plans` | Per-account plans | `id`, `template_id`, `account_id`, `csm_id`, `start_date`, `time_to_value_days`, `status` |
| `onboarding_milestone_completions` | Milestone status | `id`, `plan_id`, `milestone_key`, `completed_date`, `is_stalled` |

---

## Permissions

```
cs.onboarding.view-any
cs.onboarding.manage-templates
cs.onboarding.update-progress
cs.onboarding.view-cohort-data
cs.onboarding.export
```

---

## Filament

- **Resource:** `App\Filament\Cs\Resources\AccountOnboardingPlanResource`
- **Pages:** `ListAccountOnboardingPlans`, `CreateAccountOnboardingPlan`, `ViewAccountOnboardingPlan`
- **Custom pages:** `OnboardingDashboardPage`, `TtvCohortPage`, `StalledAccountsPage`
- **Widgets:** `MedianTtvWidget`, `StalledOnboardingsWidget`, `OnboardingCompletionWidget`
- **Nav group:** Accounts

---

## Displaces

| Feature | FlowFlex | Gainsight | ChurnZero | Totango |
|---|---|---|---|---|
| Milestone template tracking | Yes | Yes | Yes | Yes |
| Time-to-value measurement | Yes | Yes | Yes | Yes |
| Stall detection | Yes | Yes | Yes | Yes |
| AI completion prediction | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[health-scores]] — onboarding completion is a health score metric
- [[playbooks]] — onboarding stall triggers a CSM playbook
- [[success-plans]] — first onboarding milestones are part of the success plan
- [[plg/onboarding-flows]] — in-app onboarding tours complement CSM-tracked milestones
