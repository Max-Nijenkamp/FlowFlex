---
type: module
domain: Product-Led Growth
panel: plg
module-key: plg.onboarding
status: planned
color: "#4ADE80"
---

# Onboarding Flows

> In-app onboarding tours with configurable step sequences, completion tracking, and personalisation by user role or company type.

**Panel:** `plg`
**Module key:** `plg.onboarding`

---

## What It Does

Onboarding Flows delivers contextual in-app guidance to new users and companies by displaying a series of tooltip-based, modal-based, or checklist-based steps that walk them through key features. Each flow is triggered by a condition — new user sign-in, first visit to a panel, or manual trigger — and tracks which users have completed each step. Admins can create multiple flows for different personas (e.g. a flow for the first manager who sets up HR, a different flow for an employee completing their profile).

---

## Features

### Core
- Flow creation: name, trigger condition, target audience (role, company type, or new user), and step sequence
- Step types: tooltip (anchor to a UI element), modal (overlay with text and media), checklist item (freestanding task)
- Completion tracking: per-user and per-company completion rates for each flow and step
- Dismiss option: users can dismiss a flow or individual steps; admins can set whether dismissed steps reappear
- Progress indicator: floating checklist widget visible to the user showing their onboarding progress

### Advanced
- Branching: show different next steps based on the user's role or previous step response
- Personalisation variables: inject the user's name or company name into step copy
- Flow scheduling: trigger a flow at a specific time after sign-up (e.g. day 3 of trial)
- A/B testing: split users across two flow variants and compare completion rates
- Flow preview mode: preview how a flow will appear before activating it

### AI-Powered
- Step effectiveness analysis: identify which steps have the lowest completion rates for optimisation
- Flow recommendation: suggest which onboarding flow to show a user based on their role and initial actions
- Copy optimisation: AI suggests alternative step copy based on completion rate data

---

## Data Model

```erDiagram
    onboarding_flows {
        ulid id PK
        ulid company_id FK
        string name
        string trigger_condition
        json target_audience
        json steps
        boolean is_active
        timestamps created_at_updated_at
    }

    onboarding_completions {
        ulid id PK
        ulid flow_id FK
        ulid user_id FK
        ulid company_id FK
        string status
        json steps_completed
        timestamp started_at
        timestamp completed_at
    }

    onboarding_flows ||--o{ onboarding_completions : "tracked via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `onboarding_flows` | Flow definitions | `id`, `company_id`, `name`, `trigger_condition`, `steps`, `is_active` |
| `onboarding_completions` | User progress | `id`, `flow_id`, `user_id`, `status`, `steps_completed`, `completed_at` |

---

## Permissions

```
plg.onboarding.view-any
plg.onboarding.create
plg.onboarding.update
plg.onboarding.delete
plg.onboarding.view-completions
```

---

## Filament

- **Resource:** `App\Filament\Plg\Resources\OnboardingFlowResource`
- **Pages:** `ListOnboardingFlows`, `CreateOnboardingFlow`, `EditOnboardingFlow`
- **Custom pages:** `FlowPreviewPage`, `CompletionAnalyticsPage`
- **Widgets:** `OnboardingCompletionWidget`, `FlowDropOffWidget`
- **Nav group:** Onboarding

---

## Displaces

| Feature | FlowFlex | Appcues | Pendo | Intercom Tours |
|---|---|---|---|---|
| No-code flow builder | Yes | Yes | Yes | Yes |
| Branching flows | Yes | Yes | Yes | No |
| A/B testing | Yes | Yes | Yes | No |
| Native platform data | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[trial-management]] — trial-specific onboarding flows
- [[activation-metrics]] — flow completions tied to activation events
- [[feature-flags]] — flows gated behind feature flags
- [[changelog]] — new feature flows complement changelog announcements
