---
type: module
domain: Business Travel
panel: travel
module-key: travel.policies
status: planned
color: "#4ADE80"
---

# Travel Policies

> Travel policy rules — per-diem rates, booking class limits, advance booking requirements, and route-specific overrides.

**Panel:** `travel`
**Module key:** `travel.policies`

---

## What It Does

Travel Policies is where company travel rules are defined and maintained. Finance or HR teams configure rules governing how employees can travel — the maximum daily hotel rate, the minimum advance booking window for flights, whether economy or business class is permitted for different journey durations, and per-diem meal allowance rates by country. These rules are automatically evaluated when a travel request is submitted, and violations are flagged for manager awareness before approval.

---

## Features

### Core
- Policy rules engine: create rules with conditions (journey type, destination country, employee seniority) and limits (max cost, class, advance days)
- Per-diem rates: configure daily meal and incidental allowance by country or region
- Booking class rules: define permitted booking class by flight duration (e.g. economy under 4 hours, business over 6 hours)
- Advance booking window: set minimum days of advance notice required for flight bookings
- Hotel rate caps: maximum nightly hotel rate by city or country
- Policy versioning: maintain a history of policy changes with effective dates

### Advanced
- Role-based policy tiers: different rules for different seniority levels (employee, manager, executive)
- Exception workflow: employees can request an exception to a policy rule with a justification; requires approval
- Destination risk flags: tag high-risk destinations with additional approval or insurance requirements
- Seasonal overrides: adjust hotel rate caps during known peak seasons
- Multi-currency per-diem: define per-diem rates in local currencies with automatic base currency conversion

### AI-Powered
- Policy compliance reporting: analyse historical trips and highlight the most frequently violated rules
- Per-diem benchmarking: compare company per-diem rates against published industry benchmarks
- Policy optimisation suggestions: identify rules that are never triggered and may be candidates for simplification

---

## Data Model

```erDiagram
    travel_policies {
        ulid id PK
        ulid company_id FK
        string name
        string version
        date effective_from
        date effective_to
        boolean is_active
        timestamps created_at_updated_at
    }

    travel_policy_rules {
        ulid id PK
        ulid policy_id FK
        string rule_type
        json conditions
        json limits
        text description
    }

    travel_per_diems {
        ulid id PK
        ulid policy_id FK
        string country_code
        string city
        decimal daily_allowance
        string currency
    }

    travel_policies ||--o{ travel_policy_rules : "contains"
    travel_policies ||--o{ travel_per_diems : "defines"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `travel_policies` | Policy versions | `id`, `company_id`, `name`, `effective_from`, `is_active` |
| `travel_policy_rules` | Individual rules | `id`, `policy_id`, `rule_type`, `conditions`, `limits` |
| `travel_per_diems` | Per-diem rates | `id`, `policy_id`, `country_code`, `daily_allowance`, `currency` |

---

## Permissions

```
travel.policies.view
travel.policies.create
travel.policies.update
travel.policies.delete
travel.policies.manage-exceptions
```

---

## Filament

- **Resource:** `App\Filament\Travel\Resources\TravelPolicyResource`
- **Pages:** `ListTravelPolicies`, `CreateTravelPolicy`, `EditTravelPolicy`, `ViewTravelPolicy`
- **Custom pages:** `PolicyComplianceReportPage`, `ExceptionQueuePage`
- **Widgets:** `PolicyViolationSummaryWidget`, `ActivePolicyWidget`
- **Nav group:** Policies

---

## Displaces

| Feature | FlowFlex | TravelPerk | Concur | Egencia |
|---|---|---|---|---|
| Rules engine | Yes | Yes | Yes | Yes |
| Per-diem rate management | Yes | Yes | Yes | Yes |
| Exception workflow | Yes | Yes | Yes | Yes |
| AI policy benchmarking | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[travel-requests]] — policies evaluated on request submission
- [[bookings]] — booking costs checked against hotel and class limits
- [[expense-reports]] — per-diem rates applied to expense calculations
