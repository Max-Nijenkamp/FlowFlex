---
domain: workplace
module: visitor-management
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Visitor Management — API / DTOs

## `PreRegisterVisitorData` (input)

| Field | Type | Rules |
|---|---|---|
| `name` | string | required (stored encrypted) |
| `company_name` | string | nullable |
| `email` | string | nullable, email (stored encrypted) |
| `host_employee_id` | ulid | current user's employee, or chosen with permission |
| `expected_at` | timestamp | required, future |
| `purpose` | string | nullable |

## `KioskCheckInData` (input — kiosk page)

| Field | Type | Rules |
|---|---|---|
| `visitor_lookup` | string | match today's expected visitor by name *(assumed — encrypted, in-memory match)* |
| walk-in fields | — | `name`, `company_name`, `host_employee_id`, `purpose` when no pre-registration |
| `declaration_accepted` | bool | required when the NDA toggle is enabled |

## `VisitorService::checkIn(...)`

- Assigns `badge_number`, gates on declaration, notifies host (in-app + `VisitorArrivedMail`), dispatches `GenerateVisitorBadgeJob`.

## Public / Portal Endpoints

- **Kiosk check-in** is the one guest-facing surface. Modelled as a **dedicated kiosk-role session** on a locked device *(assumed — not an open public route)*; actions are rate-limited per device/IP.
- Optionally a **public-vue** self-service check-in screen for the reception tablet (Vue + Inertia, scoped kiosk guard) — see [[features/check-in]]. No other public/portal routes.
