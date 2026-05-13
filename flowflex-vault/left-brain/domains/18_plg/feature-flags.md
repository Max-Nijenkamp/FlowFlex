---
type: module
domain: Product-Led Growth
panel: plg
cssclasses: domain-plg
phase: 7
status: complete
migration_range: 890000–892999
last_updated: 2026-05-12
---

# Feature Flags

Boolean and percentage-based feature toggles, environment targeting, kill switches, and gradual rollouts. Replaces LaunchDarkly and Unleash for SaaS teams.

---

## Core Functionality

### Flag Types
| Type | Behaviour |
|---|---|
| **Boolean** | Simple on/off for all users |
| **Percentage rollout** | Enable for X% of users (consistent hashing per user ID) |
| **User list** | Enable for specific user IDs / emails |
| **Segment** | Enable for users matching a segment (from [[user-segmentation]]) |
| **Environment** | Different value per environment (dev/staging/production) |

### Flag Structure
Each flag has:
- Key (code-friendly slug, immutable after creation, e.g., `new_checkout_flow`)
- Name (human label)
- Description
- Environments: separate on/off per environment
- Targeting rules (evaluated in order, first match wins)
- Default value (fallback if no rule matches)
- Kill switch: immediately disable everywhere, overrides all rules

### Targeting Rules
```
IF user.segment = "beta_users" → return true
IF user.plan = "enterprise"   → return true
IF rollout_percentage <= 20   → return true (20% of users)
DEFAULT                       → return false
```

Rule evaluation uses consistent hashing (user ID + flag key → deterministic bucket 0–99) so a user always gets the same treatment.

---

## SDK Integration

### JavaScript SDK
```html
<script src="https://plg.flowflex.io/sdk.js?key=PUBLIC_KEY"></script>
```

```javascript
const ff = await FlowFlex.flags.init({ userId: 'usr_123', traits: { plan: 'pro' } });
if (ff.isEnabled('new_checkout_flow')) {
  showNewCheckout();
}
```

### Server-Side SDK (PHP/Laravel)
```php
use FlowFlex\PLG\FeatureFlags;

if (FeatureFlags::isEnabled('new_checkout_flow', $user)) {
    // ...
}
```

### REST API
```
GET /api/plg/flags/evaluate?userId=usr_123
Returns: { "new_checkout_flow": true, "sidebar_redesign": false, ... }
```

Evaluation happens server-side. Client SDK caches results and re-evaluates on focus/visibility change.

---

## Analytics Integration

When a flag is evaluated, an event fires:
```
flag_evaluated { flag_key, user_id, value, timestamp }
```

This feeds into [[product-usage-analytics]] for:
- Exposure analysis: "how many users were in the treatment group?"
- Conversion funnels segmented by flag value (A/B test analysis)

---

## Data Model

### `plg_flags`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| key | varchar(100) | unique per tenant, immutable |
| name | varchar(200) | |
| description | text | nullable |
| kill_switch | bool | if true, returns default_value everywhere |
| default_value | bool | |
| archived_at | timestamp | nullable |

### `plg_flag_environments`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| flag_id | ulid | FK |
| environment | enum | development/staging/production |
| enabled | bool | |
| targeting_rules | json | ordered array of rules |
| rollout_percentage | int | 0–100 |

---

## Migration

```
890000_create_plg_flags_table
890001_create_plg_flag_environments_table
890002_create_plg_flag_evaluation_logs_table
```

---

## Related

- [[MOC_PLG]]
- [[user-segmentation]] — segment-based targeting
- [[product-usage-analytics]] — A/B test analysis
- [[in-app-tours-onboarding]] — flag-gated onboarding flows
