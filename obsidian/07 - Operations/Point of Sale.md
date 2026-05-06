---
tags: [flowflex, domain/operations, pos, retail, phase/5]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-06
---

# Point of Sale (POS)

Tablet and web-based POS for retail and hospitality. Real-time inventory sync on every sale.

**Who uses it:** Retail/hospitality staff, managers
**Filament Panel:** `operations`
**Depends on:** [[Inventory Management]], [[Product Catalogue]] (if active)
**Phase:** 5

## Events Consumed

- `ClockIn` (from [[Scheduling & Shifts]]) → starts shift tracking for the till operator

## Features

- **Tablet / web-based POS** — runs on any browser, optimised for tablet
- **Product catalogue** — pulls from [[Product Catalogue]] if Ecommerce module active
- **Cash and card payment processing**
- **Email or print receipts**
- **Real-time inventory sync** — every sale deducts stock immediately
- **End-of-day Z-reports** — cash reconciliation and daily sales summary
- **Split payments** — customer pays with multiple payment methods

## Related

- [[Operations Overview]]
- [[Inventory Management]]
- [[Product Catalogue]]
- [[Scheduling & Shifts]]
