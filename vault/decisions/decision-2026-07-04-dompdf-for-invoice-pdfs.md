---
type: adr
date: 2026-07-04
status: decided
domain: core
color: "#F97316"
---

# Dompdf for invoice PDFs (deviation from spatie/laravel-pdf)

## Context

`architecture/packages.md` names spatie/laravel-pdf for PDF generation. That package drives Browsershot → puppeteer → headless Chrome. The app container ships neither node nor Chromium; adding them means a materially bigger image, slower builds, and a chrome-sandbox story inside docker. The immediate need (gap-invoice-pdf-missing) is a billing invoice: one page, tables, no JS-driven layout.

## Options Considered

1. **Add node + Chromium to the container, use spatie/laravel-pdf** — pixel-perfect CSS, but heavyweight image change for one document type; runtime Chrome crashes become a billing-path failure mode.
2. **barryvdh/laravel-dompdf** — pure PHP, zero image changes, CSS 2.1-level support; enough for tabular documents. DejaVu Sans covers €.
3. **External PDF API** — new vendor + PII (billing data) leaves the platform. Rejected.

## Decision

barryvdh/laravel-dompdf for invoice PDFs (`RenderInvoicePdfAction` + `resources/views/pdf/invoice.blade.php`). Templates stay dompdf-safe: tables, inline CSS, DejaVu Sans.

Scope: this decides the **invoice** path only. If a later module needs rich, design-system-faithful PDFs (proposals, DMS batch merge), revisit spatie/laravel-pdf with a dedicated Chrome sidecar container — do not grow dompdf beyond tabular documents.

## Consequences

- Invoice PDF works in every environment the app runs, tests included (pure PHP).
- Switchboard fidelity limited: no flexbox/grid in templates, approximated styling.
- Two PDF stacks possible later; the ADR boundary (tabular = dompdf, rich = spatie) keeps that intentional.

## Related

- [[../build/gaps/gap-invoice-pdf-missing|gap-invoice-pdf-missing]] (resolved by this build)
- `architecture/packages.md` PDF section
