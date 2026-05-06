---
tags: [flowflex, domain/it, assets, licences, phase/5]
domain: IT & Security Management
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-06
---

# IT Asset Management (ITAM)

Full hardware and software lifecycle. Know what you have, what licences you're paying for, and when things expire.

**Who uses it:** IT team
**Filament Panel:** `it`
**Depends on:** [[Asset Management]] (for physical asset data)
**Phase:** 5

## Events Consumed

- `EmployeeHired` → provisions hardware and software access per onboarding template
- `OffboardingCompleted` → triggers deprovisioning workflow

## Features

- **Full hardware and software lifecycle tracking**
- **Licence compliance** — are we over or under-licensed?
- **Renewal alerts** — software subscription renewals
- **Warranty management** — hardware warranty expiry
- **Cost per asset** — total cost of ownership
- **Compliance dashboard** — licence compliance across all tools
- **Integrates with Onboarding/Offboarding flows**

## Related

- [[IT Overview]]
- [[Asset Management]]
- [[SaaS Spend Management]]
- [[Onboarding]]
- [[Offboarding]]
