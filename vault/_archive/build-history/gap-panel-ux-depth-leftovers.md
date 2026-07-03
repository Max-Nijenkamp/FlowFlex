---
type: gap
severity: medium
category: feature
status: open
domain: All
color: "#F97316"
discovered: 2026-06-12
discovered-in: crm.pipeline
---

# Panel UX depth — remaining leftovers after the 2026-06-12 sweep

## Context

Founder browser review found empty dashboards, empty create modals, missing create actions and pale custom pages across HR/Finance/CRM. The sweep fixed most; these remain:

## Remaining

- **Finance**: cash-flow page restyle (sections/table treatment); OverdueInvoicesWidget (table widget) not yet built
- **CRM**: ✅ RESOLVED 2026-06-12 — Booking, Contract, MeetingType, Segment, Product, Referral, Sequence got real Section forms + CreateAction modals; DealRoom = flow-owned (canCreate false + explanatory empty state)
- **HR**: ✅ PayrollRun + Timesheet got real create forms + CreateActions 2026-06-12. Remaining: My Profile page styling pass
- **Spotlight**: ✅ DELIVERED 2026-06-12 — custom panel-scoped palette (`App\Livewire\Spotlight`, ⌘K/Ctrl+K, navigation + quick-create + record search via global search provider; filament-patterns item 14). Remaining sliver: most resources still lack `getGloballySearchableAttributes`, so record results are thin until resources opt in — add per domain as they mature
- **Attachments**: only Deal/Contact/Account have them; consider employees (documents), invoices (PDFs) next

## Proposed Solution

One follow-up session per domain against this list; spotlight gets its own mini-spec first.
