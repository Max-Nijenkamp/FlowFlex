---
tags: [flowflex, domain/it, assets, licences, phase/6]
domain: IT & Security
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-07
---

# IT Asset Management (ITAM)

Full hardware and software lifecycle. Know what you have, what licences you're paying for, and when things expire. Separate from Operations Asset Management — this covers IT hardware, software licences, and compliance.

**Who uses it:** IT team, finance (licence costs)
**Filament Panel:** `it`
**Depends on:** [[HR — Employee Profiles]], [[Onboarding]], [[Offboarding]]
**Phase:** 6
**Build complexity:** High — 3 resources, 1 page, 3 tables

---

## Features

- **Hardware asset register** — track every laptop, desktop, server, mobile, printer, and network device; capture make, model, serial, asset tag, purchase date, and warranty expiry
- **Asset assignment** — assign hardware to a specific tenant; track assignment history; unassign on offboarding
- **Status lifecycle** — in_use / in_storage / decommissioned; status changes trigger notifications
- **Warranty expiry alerts** — alert IT team X days before `warranty_expires`; configurable threshold
- **Software licence register** — record all software licences with vendor, type, seats purchased vs used, annual cost, and renewal date
- **Licence allocation** — track which tenants are allocated seats in each licence; `licence_allocations` records allocated_at and revoked_at
- **Licence compliance** — compare `seats_purchased` vs `seats_used`; show over/under-licensed status per licence; compliance dashboard widget
- **`SoftwareLicenceExpiring` event** — fired when `renewal_date` is within threshold; notifies IT and finance teams
- **Total cost of ownership** — sum purchase price, ongoing licence costs, and maintenance costs per asset
- **OS and version tracking** — track operating system and version per hardware asset; flag outdated OS versions
- **Integration with Onboarding** — `EmployeeHired` event creates a provisioning checklist in the IT helpdesk for standard hardware and software assignment
- **Integration with Offboarding** — `OffboardingCompleted` event creates a deprovisioning task; revokes all `licence_allocations` for the departing employee

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `it_assets`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Max's MacBook Pro" |
| `type` | enum | `laptop`, `desktop`, `server`, `mobile`, `printer`, `network`, `other` |
| `make` | string nullable | |
| `model` | string nullable | |
| `serial_number` | string nullable | |
| `asset_tag` | string nullable | internal asset tag |
| `purchase_date` | date nullable | |
| `purchase_price` | decimal(10,2) nullable | |
| `warranty_expires` | date nullable | |
| `status` | enum | `in_use`, `in_storage`, `decommissioned` |
| `assigned_to` | ulid FK nullable | → tenants |
| `assigned_at` | timestamp nullable | |
| `os` | string nullable | e.g. "macOS" |
| `os_version` | string nullable | e.g. "14.4" |
| `notes` | text nullable | |

### `software_licences`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Adobe Creative Cloud" |
| `vendor` | string nullable | |
| `licence_type` | string nullable | e.g. "per-seat", "site", "concurrent" |
| `seats_purchased` | integer nullable | |
| `seats_used` | integer default 0 | computed from allocations |
| `cost_annual` | decimal(10,2) nullable | |
| `renewal_date` | date nullable | |
| `is_auto_renew` | boolean default false | |
| `assigned_tenant_ids` | json nullable | denormalised list for quick display |
| `notes` | text nullable | |

### `licence_allocations`
| Column | Type | Notes |
|---|---|---|
| `software_licence_id` | ulid FK | → software_licences |
| `tenant_id` | ulid FK | → tenants |
| `allocated_at` | timestamp | |
| `revoked_at` | timestamp nullable | |
| `notes` | string nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `SoftwareLicenceExpiring` | `software_licence_id`, `renewal_date` | Notification to IT team and finance team |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `EmployeeHired` | [[HR — Employee Profiles]] | Creates IT provisioning task in [[Internal IT Helpdesk]] |
| `OffboardingCompleted` | [[Offboarding]] | Revokes all `licence_allocations` for departing tenant; creates deprovisioning IT ticket |

---

## Permissions

```
it.it-assets.view
it.it-assets.create
it.it-assets.edit
it.it-assets.delete
it.it-assets.assign
it.software-licences.view
it.software-licences.create
it.software-licences.edit
it.software-licences.delete
it.licence-allocations.view
it.licence-allocations.create
it.licence-allocations.revoke
```

---

## Related

- [[IT Overview]]
- [[SaaS Spend Management]]
- [[Onboarding]]
- [[Offboarding]]
- [[Access & Permissions Audit]]
