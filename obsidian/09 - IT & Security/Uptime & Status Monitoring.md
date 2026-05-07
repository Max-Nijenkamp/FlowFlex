---
tags: [flowflex, domain/it, monitoring, uptime, phase/6]
domain: IT & Security
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-07
---

# Uptime & Status Monitoring

Monitor internal and client-facing services, publish a public status page, and manage incidents with a structured workflow — from first detection through postmortem.

**Who uses it:** IT team, DevOps, operations managers
**Filament Panel:** `it`
**Depends on:** Core, [[Notifications & Alerts]]
**Phase:** 6
**Build complexity:** High — 4 resources, 1 page, 4 tables

---

## Features

- **Service monitoring** — register services with URL, check type (HTTP/ping/TCP), check interval, and expected response; checks run via scheduled queue job
- **HTTP checks** — verify response code matches `expected_status_code`; record response time in milliseconds
- **Ping and TCP checks** — for non-HTTP services (databases, SMTP relays, internal tools)
- **Status dashboard** — real-time status of all monitored services: up (green), degraded (amber), down (red); uptime % over last 30/90 days
- **`ServiceDown` alert** — fires immediately when a check fails; notifies IT team via email and in-app notification
- **`ServiceRecovered` event** — fires when a previously down service returns to up; notifies IT team that the incident is auto-resolved
- **Incident management** — when a service goes down, create a `status_incidents` record; track status through investigating → identified → monitoring → resolved
- **Public status page** — each `status_pages` record generates a publicly accessible page showing service statuses and active incidents; configurable `company_message`
- **Custom status page domain** — host the status page at `status.yourcompany.com` via CNAME; no FlowFlex branding
- **Incident timeline** — status incident feed shows all updates with timestamps; customers can subscribe to incident updates via email
- **Postmortem templates** — after resolving a critical incident, create a structured postmortem document linked to the `status_incidents` record
- **Alert history** — log of every `ServiceDown` and `ServiceRecovered` event for the past 90 days; used for SLA reporting to customers

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `monitored_services`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `url` | string | URL or hostname |
| `check_type` | enum | `http`, `ping`, `tcp` |
| `check_interval_seconds` | integer default 60 | |
| `timeout_ms` | integer default 5000 | |
| `is_active` | boolean default true | |
| `expected_status_code` | integer nullable | for HTTP checks |
| `tcp_port` | integer nullable | for TCP checks |
| `current_status` | enum | `up`, `down`, `degraded`, `unknown` |
| `last_checked_at` | timestamp nullable | |
| `uptime_30d` | decimal(5,2) nullable | % |

### `status_checks`
| Column | Type | Notes |
|---|---|---|
| `monitored_service_id` | ulid FK | → monitored_services |
| `status` | enum | `up`, `down`, `degraded` |
| `response_time_ms` | integer nullable | |
| `status_code` | integer nullable | HTTP response code |
| `checked_at` | timestamp | |
| `error_message` | string nullable | |

### `status_incidents`
| Column | Type | Notes |
|---|---|---|
| `monitored_service_id` | ulid FK | → monitored_services |
| `title` | string | |
| `body` | text nullable | current update |
| `status` | enum | `investigating`, `identified`, `monitoring`, `resolved` |
| `started_at` | timestamp | |
| `resolved_at` | timestamp nullable | |
| `created_by` | ulid FK nullable | → tenants |
| `postmortem` | text nullable | filled after resolution |

### `status_pages`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string unique per company | |
| `is_public` | boolean default true | |
| `company_message` | text nullable | shown at top of public page |
| `custom_domain` | string nullable | e.g. "status.yourcompany.com" |
| `service_ids` | json | array of monitored_service IDs to display |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `ServiceDown` | `monitored_service_id`, `status` | Immediate notification to IT team |
| `ServiceRecovered` | `monitored_service_id` | Notification to IT team; auto-update open incident to monitoring |

---

## Events Consumed

None — monitoring is purely scheduled-poll driven.

---

## Permissions

```
it.monitored-services.view
it.monitored-services.create
it.monitored-services.edit
it.monitored-services.delete
it.status-checks.view
it.status-incidents.view
it.status-incidents.create
it.status-incidents.update
it.status-incidents.resolve
it.status-pages.view
it.status-pages.create
it.status-pages.edit
```

---

## Related

- [[IT Overview]]
- [[Notifications & Alerts]]
- [[Security & Compliance]]
