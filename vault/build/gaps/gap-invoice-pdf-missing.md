---
type: gap
severity: medium
category: feature
status: resolved
domain: core
color: "#F97316"
discovered: 2026-07-04
discovered-in: core.billing-engine
---

# Invoice PDF generation + download action not built

> **RESOLVED 2026-07-04** — dompdf render (`RenderInvoicePdfAction` + `pdf/invoice.blade.php`, ADR [[../../decisions/decision-2026-07-04-dompdf-for-invoice-pdfs|dompdf-for-invoice-pdfs]]), download actions on /app + /admin lists + company Invoices tab, attached to InvoiceMail at send time. Not stored via media library (rendered on demand — invoices are derivable, storage adds a stale-copy risk).

## Context
core.billing-engine/monthly-invoicing spec includes "recurring invoice PDF generation + email delivery" and a download-PDF action on the invoice list. The 2026-07-04 phase-1 sweep shipped invoice generation, lines, the mail and the read-only list — but no PDF.

## Problem
No spatie/laravel-pdf template, no download action on BillingInvoiceResource, InvoiceMail carries totals but attaches nothing.

## Impact
Customers cannot download a formal invoice document; NL businesses need one for their books.

## Proposed Solution
Blade invoice template (Switchboard receipt styling per the design system), spatie/laravel-pdf render on invoice open, stored via core.file-storage under companies/{id}/invoices/, download action on the /app and /admin lists, attached to InvoiceMail.
