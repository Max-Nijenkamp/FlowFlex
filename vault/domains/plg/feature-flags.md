---
type: module
domain: Product-Led Growth
panel: plg
module-key: plg.flags
status: planned
color: "#4ADE80"
---

# Feature Flags

> Feature flag management — create flags, target by company or user, configure gradual rollouts, and kill-switch features safely.

**Panel:** `plg`
**Module key:** `plg.flags`

---

## What It Does

Feature Flags provides a centralised flag management interface for controlling which FlowFlex features are visible or active for specific companies or users. This powers trial feature gating, beta programme access, gradual rollouts, and instant kill switches for problematic features. Flags can target individual companies, user roles, custom segments, or a random percentage of traffic. The flag state is evaluated server-side at runtime and cached for performance.

---

## Features

### Core
- Flag creation: key name, description, type (boolean toggle, string variant, number variant)
- On/Off toggle: globally enable or disable a flag with one click
- Targeting rules: enable a flag for specific companies, user IDs, or user roles
- Percentage rollout: gradually roll out a flag to 5%, 25%, 50%, 100% of companies or users
- Default value: configure the fallback value when no targeting rule matches
- Flag audit log: record every flag change with actor, timestamp, and old/new value

### Advanced
- Variants: configure multiple string or number variants for multi-variate flag experiments
- Prerequisite flags: a flag only evaluates when another flag is already enabled
- Scheduled activation: set a date and time for a flag to automatically turn on or off
- Environment scoping: separate flag states for development, staging, and production
- SDK evaluation cache: flag values cached per tenant for sub-millisecond evaluation

### AI-Powered
- Rollout recommendation: AI suggests optimal rollout percentage increments based on error rate monitoring
- Stale flag detection: identify flags that have been 100% on for more than 90 days as candidates for code removal
- Impact prediction: estimate user impact of enabling a flag based on target segment size

---

## Data Model

```erDiagram
    feature_flags {
        ulid id PK
        ulid company_id FK
        string key
        string name
        text description
        string type
        json default_value
        boolean is_enabled
        json targeting_rules
        decimal rollout_percentage
        timestamps created_at_updated_at
    }

    flag_evaluation_overrides {
        ulid id PK
        ulid flag_id FK
        string target_type
        string target_id
        json override_value
        timestamps created_at_updated_at
    }

    flag_audit_events {
        ulid id PK
        ulid flag_id FK
        ulid changed_by FK
        json old_state
        json new_state
        timestamp changed_at
    }

    feature_flags ||--o{ flag_evaluation_overrides : "overridden by"
    feature_flags ||--o{ flag_audit_events : "audited via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `feature_flags` | Flag definitions | `id`, `company_id`, `key`, `type`, `is_enabled`, `targeting_rules`, `rollout_percentage` |
| `flag_evaluation_overrides` | Per-target overrides | `id`, `flag_id`, `target_type`, `target_id`, `override_value` |
| `flag_audit_events` | Change log | `id`, `flag_id`, `changed_by`, `old_state`, `new_state`, `changed_at` |

---

## Permissions

```
plg.flags.view
plg.flags.create
plg.flags.update
plg.flags.delete
plg.flags.manage-targeting
```

---

## Filament

- **Resource:** `App\Filament\Plg\Resources\FeatureFlagResource`
- **Pages:** `ListFeatureFlags`, `CreateFeatureFlag`, `EditFeatureFlag`, `ViewFeatureFlag`
- **Custom pages:** `FlagAuditLogPage`, `RolloutDashboardPage`
- **Widgets:** `ActiveFlagsWidget`, `StaleFlagsWidget`
- **Nav group:** Analytics

---

## Displaces

| Feature | FlowFlex | LaunchDarkly | Unleash | Flagsmith |
|---|---|---|---|---|
| Boolean and variant flags | Yes | Yes | Yes | Yes |
| Percentage rollout | Yes | Yes | Yes | Yes |
| Audit log | Yes | Yes | Yes | Yes |
| AI rollout recommendation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Flag evaluation in Laravel:** The `FlagService::evaluate($flagKey, $context)` method is the runtime evaluation function called throughout the codebase wherever a flag is checked. It must be fast — cached in Redis with a TTL of 5 minutes. Cache key: `flag:{company_id}:{flag_key}:{context_hash}`. The context hash is a deterministic hash of the targeting context (user ID, company ID, role) so targeting overrides are cached correctly per context.

**`feature_flags.company_id` scoping:** There are two distinct use cases for feature flags in FlowFlex:
1. **FlowFlex internal flags** — used by FlowFlex engineers to control rollouts of FlowFlex platform features. These flags are managed in the `/admin` panel and scoped to `company_id = null` (global) or to specific tenant `company_id` values.
2. **Tenant-own flags** — used by tenant companies to control rollouts of their own product features. These are scoped to the tenant's `company_id`.

The current data model uses a single `feature_flags` table with `company_id FK`. This conflates both use cases. For FlowFlex internal flags, `company_id` should be nullable with a separate admin scope. Document this design decision — it affects the `FlagService::evaluate()` query logic.

**Percentage rollout:** `rollout_percentage` (e.g. 25.0) is applied using a deterministic hash of `(flag_key + company_id)` modulo 100. This ensures the same company always gets the same flag state within a rollout — it doesn't flip randomly on each request. PHP: `crc32($flagKey . $companyId) % 100 < $rolloutPercentage`.

**`FlagAuditLogPage`:** A custom Filament `Page` — a timeline view of `flag_audit_events` showing diffs of `old_state` vs `new_state` JSON. Rendered as a vertical timeline with colour-coded diffs (red for off, green for on). Not a standard Resource list.

**`RolloutDashboardPage`:** A custom Filament `Page` showing all flags with `rollout_percentage < 100` as a progress bar visualisation (chart.js or plain HTML progress elements) alongside error rate trend for each flag (requires integration with the analytics/anomaly-detection module).

**Environment scoping:** The spec mentions separate flag states for dev/staging/production. Implement via `APP_ENV` — the `FlagService` reads flags filtered by a `environment` column on `feature_flags`. Add `string environment default 'production'` to the data model. The admin UI allows toggling flags per environment independently.

## Related

- [[trial-management]] — trial feature access controlled by flags
- [[onboarding-flows]] — new features introduced via onboarding tours gated behind flags
- [[usage-analytics]] — feature adoption tracked against flag enablement
- [[activation-metrics]] — flag-gated features included in activation event definitions
