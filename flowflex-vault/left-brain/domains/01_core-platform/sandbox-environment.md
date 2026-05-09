---
type: module
domain: Core Platform
panel: admin
cssclasses: domain-admin
phase: 1
status: planned
migration_range: 000000–099999
last_updated: 2026-05-09
---

# Sandbox Environment

Per-tenant isolated staging environment. Customers test configuration changes, new module setups, and training without affecting live data. Required for enterprise sales — no enterprise buyer accepts a platform with no staging environment.

**Panel:** `admin`  
**Phase:** 1 — needed before enterprise onboarding

---

## Features

### Sandbox Provisioning
- One sandbox per company (included in Pro/Enterprise plans)
- Provisioned on-demand via admin panel (takes ~2 minutes)
- Same subdomain pattern: `sandbox.company.flowflex.com`
- Separate database, separate Redis namespace, shared S3 (different prefix)

### Data Seeding Options
- **Empty** — fresh install, no data
- **Sample data** — auto-generated realistic fake data (100 employees, 500 contacts, 1000 invoices etc.)
- **Clone from production** — copy current production data (PII scrubbed: names anonymised, emails replaced with `user_X@sandbox.test`)

### Sync Direction
- Production → Sandbox: push config changes (module settings, roles, workflows) to sandbox for testing
- Sandbox → Production: promote tested config changes back to production (NOT data, only settings)
- No automatic two-way sync (too dangerous)

### Sandbox Limitations
- Email sending disabled (all emails captured in sandbox inbox viewer, not sent externally)
- Payment processing: Stripe test mode keys only
- Webhooks: sent to sandbox webhook URL, flagged with `X-FlowFlex-Sandbox: true` header
- External integrations: use sandbox/test credentials only
- File storage: capped at 1GB

### Sandbox Reset
- Reset to empty / reset to sample data / reset to last production clone
- Scheduled auto-reset: weekly or monthly (keeps sandbox clean)
- Reset is immediate (destroys all sandbox data)

### Access Control
- Sandbox has its own login (same users mirrored, sandbox-only sessions)
- Can grant sandbox access to external consultants without production access
- Super-admin can access any sandbox (audit logged)

---

## Data Model

No separate tables — sandbox is a separate database schema/namespace. Config stored in:

```erDiagram
    company_sandboxes {
        ulid id PK
        ulid company_id FK
        string status
        string db_name
        string redis_prefix
        string s3_prefix
        string subdomain
        timestamp last_cloned_at
        string seed_type
        timestamp reset_scheduled_at
    }
```

---

## Permissions

```
core.sandbox.create
core.sandbox.clone-from-production
core.sandbox.reset
core.sandbox.promote-config
core.sandbox.manage-access
```

---

## Related

- [[MOC_CorePlatform]]
- [[concept-multi-tenancy]]
- [[entity-company]]
