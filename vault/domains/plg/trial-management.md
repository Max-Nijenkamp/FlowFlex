---
type: module
domain: Product-Led Growth
panel: plg
module-key: plg.trials
status: planned
color: "#4ADE80"
---

# Trial Management

> Free trial configuration — duration, feature access limits, conversion triggers, and expiry actions.

**Panel:** `plg`
**Module key:** `plg.trials`

---

## What It Does

Trial Management defines the rules of a company's free trial experience. Administrators configure how long a trial lasts, which features and modules are accessible during the trial period, and what happens when the trial expires (account locked, downgraded to a free tier, or grace period). Conversion triggers detect in-product signals that indicate high purchase intent and can fire automated outreach or upgrade prompts. All trial companies are tracked with their current trial status and days remaining.

---

## Features

### Core
- Trial plan creation: name, duration in days, and which feature flags or modules are enabled
- Trial activation: applied to new company registrations automatically or manually
- Days-remaining counter: visible to trial company administrators in their settings panel
- Trial expiry actions: configurable — lock account, notify sales, downgrade to free tier, grant grace period
- Conversion event tracking: define events that indicate purchase intent (e.g. invited 5 users, created first project)
- Trial company list: view all active trials with status, days remaining, and conversion event completions

### Advanced
- Trial extension: manually extend a specific company's trial duration with a reason log
- A/B trial variants: test different trial durations or feature sets against each other
- Trial-to-paid conversion reporting: cohort analysis of conversion rates by trial variant
- Sales handoff trigger: notify the assigned SDR when a trial company reaches a conversion threshold
- Custom trial end emails: branded emails sent at trial start, midpoint, and expiry

### AI-Powered
- Conversion likelihood scoring: AI scores each trial company on likelihood to convert based on usage patterns
- Optimal outreach timing: recommend the best moment for a sales touchpoint based on in-product behaviour
- At-risk trial identification: flag trial companies with declining engagement before expiry

---

## Data Model

```erDiagram
    trial_plans {
        ulid id PK
        ulid company_id FK
        string name
        integer duration_days
        json enabled_features
        json expiry_actions
        boolean is_active
        timestamps created_at_updated_at
    }

    company_trials {
        ulid id PK
        ulid trial_plan_id FK
        ulid tenant_company_id FK
        date started_at
        date expires_at
        string status
        decimal conversion_score
        json conversion_events_completed
        timestamp converted_at
        timestamps created_at_updated_at
    }

    trial_plans ||--o{ company_trials : "applied as"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `trial_plans` | Trial configurations | `id`, `company_id`, `name`, `duration_days`, `enabled_features`, `expiry_actions` |
| `company_trials` | Per-tenant trial records | `id`, `trial_plan_id`, `tenant_company_id`, `expires_at`, `status`, `conversion_score` |

---

## Permissions

```
plg.trials.view-any
plg.trials.create-plans
plg.trials.manage-company-trials
plg.trials.extend-trials
plg.trials.export
```

---

## Filament

- **Resource:** `App\Filament\Plg\Resources\TrialPlanResource`
- **Pages:** `ListTrialPlans`, `CreateTrialPlan`, `EditTrialPlan`
- **Custom pages:** `ActiveTrialsDashboardPage`, `ConversionFunnelPage`
- **Widgets:** `ActiveTrialsWidget`, `ExpiringTrialsWidget`, `ConversionRateWidget`
- **Nav group:** Trials

---

## Displaces

| Feature | FlowFlex | Chargebee | LaunchDarkly | Custom code |
|---|---|---|---|---|
| Trial plan configuration | Yes | Yes | No | Custom |
| Conversion event tracking | Yes | No | No | Custom |
| AI conversion scoring | Yes | No | No | No |
| A/B trial variants | Yes | No | Yes | Custom |
| Included in platform | Yes | No | No | No |

---

## Related

- [[feature-flags]] — trial feature access controlled via feature flags
- [[onboarding-flows]] — trial-specific onboarding tours
- [[activation-metrics]] — activation events cross-reference with trial conversion
- [[subscription-billing/plans]] — conversion moves to a paid subscription plan
