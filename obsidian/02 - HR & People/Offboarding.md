---
tags: [flowflex, domain/hr, offboarding, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# Offboarding

Controlled, consistent exit process that protects the company and respects the departing employee.

**Who uses it:** HR team, IT team, managers
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]]
**Phase:** 8
**Build complexity:** Medium — 2 resources, 2 tables

## Events Fired

- `OffboardingStarted`
- `OffboardingCompleted` → consumed by:
  - IT/Access module (revokes all access)
  - [[Payroll]] (runs final payroll)
  - [[Asset Management]] (recalls assigned assets)

## Features

- **Offboarding trigger** — resignation, termination, redundancy, retirement (each has different checklist templates)
- **Last day configuration** — sets the date for all automated downstream actions
- **Exit interview form** — built-in form or link to external tool
- **Knowledge handover tasks** — document critical knowledge, reassign responsibilities to named colleagues
- **Asset return checklist** — linked to asset register if [[Asset Management]] module active
- **Final payroll trigger** — sends `OffboardingCompleted` event to [[Payroll]] module
- **Access revocation checklist** — sends `OffboardingCompleted` event to IT module
- **Reference letter generation** — template with merge fields
- **Employment end confirmation letter** — formal confirmation document

## Offboarding Types

| Type | Checklist Differences |
|---|---|
| Resignation | Standard exit process, garden leave rules |
| Termination | Immediate access revocation, no reference letter by default |
| Redundancy | Statutory notice + redundancy payment trigger in [[Payroll]] |
| Retirement | Extended handover period, pension confirmation |

## Cross-module Integrations

When both modules are active:
- **IT Access module** — receives `OffboardingCompleted`, revokes all system access
- **[[Payroll]]** — receives `OffboardingCompleted`, creates final pay run item
- **[[Asset Management]]** — receives `OffboardingCompleted`, creates recall tasks for all assigned assets
- **[[Document Management]]** — handover documents stored against employee profile

## Database Tables (2)

1. `offboarding_flows` — one per employee exit (linked to employee record)
2. `offboarding_tasks` — active task instances per exit flow

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Onboarding]]
- [[Payroll]]
- [[Asset Management]]
- [[IT Asset Management]]
