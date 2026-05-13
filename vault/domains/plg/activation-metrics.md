---
type: module
domain: Product-Led Growth
panel: plg
module-key: plg.activation
status: planned
color: "#4ADE80"
---

# Activation Metrics

> Activation funnel tracking — define the events that constitute "activated", measure cohort activation rates, and identify friction points.

**Panel:** `plg`
**Module key:** `plg.activation`

---

## What It Does

Activation Metrics allows PLG teams to define exactly what "product activation" means for their product — the set of actions a new user must complete to experience the core value of the platform. Once defined, the module tracks each new user and company through the activation funnel, measuring what percentage reach activation and how long it takes. Cohort analysis shows whether recent sign-up cohorts are activating faster than previous ones, and the funnel view identifies which step is causing the most drop-off.

---

## Features

### Core
- Activation event definition: select from a library of FlowFlex product events (first invite sent, first record created, first module visited, etc.)
- Activation milestone: define the specific combination or sequence of events that constitutes full activation
- Funnel view: visualise the step-by-step progression from sign-up to activation with drop-off percentages
- User-level activation status: see which individual users have reached activation and which are stalled
- Time-to-activation: median and average time from sign-up to activation milestone

### Advanced
- Cohort analysis: compare activation rates and time-to-activation across weekly or monthly sign-up cohorts
- Segment breakdown: activation rate by company size, industry, or acquisition channel
- Partial activation status: track users who have completed some but not all activation steps
- Activation goal tracking: set a target activation rate and monitor progress over time
- Email trigger: automatically send a nudge email to users who have stalled at a specific funnel step

### AI-Powered
- Friction point identification: AI identifies which activation step has the highest anomalous drop-off
- Activation path optimisation: suggest reordering the activation milestone definition to reduce time-to-value
- At-risk user detection: flag new users who have been idle for 3+ days and have not yet activated

---

## Data Model

```erDiagram
    activation_definitions {
        ulid id PK
        ulid company_id FK
        string name
        json required_events
        string completion_logic
        boolean is_active
        timestamps created_at_updated_at
    }

    user_activation_records {
        ulid id PK
        ulid definition_id FK
        ulid user_id FK
        ulid tenant_company_id FK
        json events_completed
        boolean is_activated
        integer hours_to_activation
        timestamp activated_at
        timestamps created_at_updated_at
    }

    activation_definitions ||--o{ user_activation_records : "tracks"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `activation_definitions` | Activation criteria | `id`, `company_id`, `name`, `required_events`, `completion_logic`, `is_active` |
| `user_activation_records` | Per-user tracking | `id`, `definition_id`, `user_id`, `events_completed`, `is_activated`, `hours_to_activation` |

---

## Permissions

```
plg.activation.view
plg.activation.manage-definitions
plg.activation.view-cohort-data
plg.activation.export
plg.activation.configure-triggers
```

---

## Filament

- **Resource:** `App\Filament\Plg\Resources\ActivationDefinitionResource`
- **Pages:** `ListActivationDefinitions`, `CreateActivationDefinition`, `EditActivationDefinition`
- **Custom pages:** `ActivationFunnelPage`, `CohortAnalysisPage`
- **Widgets:** `ActivationRateWidget`, `TimeToActivationWidget`, `StuckUsersWidget`
- **Nav group:** Analytics

---

## Displaces

| Feature | FlowFlex | Pendo | Amplitude | Mixpanel |
|---|---|---|---|---|
| Custom activation definition | Yes | Yes | Yes | Yes |
| Cohort analysis | Yes | Yes | Yes | Yes |
| AI friction identification | Yes | No | No | No |
| Native platform events | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[trial-management]] — activation events correlate with trial conversion
- [[onboarding-flows]] — onboarding completions are activation events
- [[usage-analytics]] — activation data is a subset of overall usage analytics
- [[feature-flags]] — new feature adoption tracks alongside activation
