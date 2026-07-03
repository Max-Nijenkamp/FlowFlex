---
type: gap
severity: medium
category: feature
status: accepted
domain: dms
color: "#F97316"
discovered: 2026-07-03
discovered-in: dms.templates
---

# Gap: Generate-from-template is single-document only — no batch mail-merge

## Context

[[../../domains/dms/templates/features/generate-from-template|generate-from-template]] is a wizard that
produces **one** document per run: pick a template, pick one merge source (`employee` / `contact` / `manual`),
pick one target folder, generate. There is no path to run one template across a **set** of records to produce
many documents in a single operation.

## Problem

Bulk mail-merge — one template × many rows → many PDFs — is a standard, repeatedly-requested workflow: HR
issuing offer letters to a cohort, sales generating contracts for a list, ops printing certificates. Doing
this one-at-a-time through the wizard is impractical at any real volume, and it is exactly the job teams
otherwise reach for Word mail-merge, Juro, or Zoho Writer to do.

## Impact

Leaves a common document-automation job unserved in [[../../domains/dms/templates/_module|dms.templates]] and
weakens the "everything in one tool" pitch for HR/sales/ops document runs. Package-fit — the single-document
path already uses `spatie/laravel-pdf` via `DocumentService::upload`; batching adds a record-set source and a
queued fan-out, no new dependency.

## Proposed Solution

Add a batch mode to `TemplateService::generate`: accept a record-set (a CRM segment, an HR employee filter,
or an uploaded CSV via `maatwebsite/laravel-excel`) instead of a single source; validate field-completeness
per row; fan out one queued `spatie/laravel-pdf` render per record through the existing
`DocumentService::upload` path into the chosen folder (respecting `accessibleFoldersFor` and the per-company
rate limit). Surface a "N generated, M skipped (missing fields)" summary. Heavy batches run on
`foundation.queues` with a progress state.

## Sources

- [Bulk-generate contracts/letters from a CSV/Excel source (Juro — mail merge for contracts)](https://juro.com/learn/mail-merge-excel-letters-contracts) (accessed 2026-07-03)
- [Send offer letters in bulk via mail merge from a data source (Zoho Writer)](https://www.zoho.com/writer/journals/send-offer-letters-in-bulk-hr-automation.html) (accessed 2026-07-03)
