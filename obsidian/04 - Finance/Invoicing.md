---
tags: [flowflex, domain/finance, invoicing, phase/3]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-06
---

# Invoicing

Create, send, and track invoices. Auto-generate from approved time entries or project milestones. Handles all billing complexity without needing a separate invoicing tool.

**Who uses it:** Finance team, account managers
**Filament Panel:** `finance`
**Depends on:** Core
**Phase:** 3
**Build complexity:** Very High — 3 resources, 2 pages, 6 tables

## Events Fired

- `InvoiceCreated`
- `InvoiceSent`
- `InvoicePaid` → consumed by [[Bank Reconciliation]] (auto-match)
- `InvoiceOverdue` → consumed by CRM (create follow-up task), [[Notifications & Alerts]]
- `CreditNoteIssued`

## Events Consumed

- `ProjectMilestoneReached` (from [[Project Planning]]) → auto-create milestone invoice
- `TimeEntryApproved` (from [[Time Tracking]]) → marks time as unbilled, available for invoicing
- `FieldJobCompleted` (from [[Field Service Management]]) → creates invoice for job

## Features

- **Invoice builder** — line items, quantities, rates, tax codes, discounts
- **Auto-generate from approved time entries** — one click: all unbilled time → invoice
- **Auto-generate from project milestones** — milestone hit → invoice triggered
- **Recurring invoice setup** — weekly, monthly, quarterly — auto-sent
- **Invoice numbering** — configurable format: `INV-2025-0001`
- **Multi-currency** — set invoice currency per client
- **Tax codes per line item** — VAT, GST, sales tax
- **Discount** — percentage or fixed amount, per line or invoice total
- **Invoice PDF generation** — branded, compliant layout (queued job)
- **Email delivery** — to client with PDF attached, tracked open
- **Payment link embed** in invoice email
- **Partial payments** — record multiple payments against one invoice
- **Late payment fee automation** — apply after N days overdue
- **Credit notes** — reduce or cancel an invoice

## Invoice Status Workflow

```
Draft → Sent → Partially Paid → Paid → Overdue → Written Off
```

## Database Tables (6)

1. `invoices` — invoice headers
2. `invoice_lines` — line items per invoice
3. `invoice_payments` — payment records against invoices
4. `credit_notes` — credit note records
5. `invoice_email_events` — open/click tracking on sent emails
6. `recurring_invoices` — recurring invoice schedule configurations

## Related

- [[Finance Overview]]
- [[Time Tracking]]
- [[Client Billing & Retainers]]
- [[Project Planning]]
- [[Bank Reconciliation]]
- [[Tax & VAT Compliance]]
- [[Contact & Company Management]]
