---
domain: dms
module: templates
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Templates — Unknowns

## Assumed Items

- Sensitive fields (salary, national ID) are excluded from every merge whitelist *(assumed)* — source states it but names no exhaustive list.
- `(company_id, category)` index on `dms_templates` *(assumed)*.
- PDF renders inline via `spatie/laravel-pdf` in the generate request; no queued render job v1 *(assumed)*.
- No cross-domain events fired v1 *(assumed)*.
- No column encryption — sensitive data never reaches a template body *(assumed)*.

## Open Questions

- Which exact HR / CRM fields each provider whitelists — the concrete field list is not enumerated in the source.
- Should PDF rendering move to a queued job (with a "generating…" state) if it proves heavy under the rate limiter?
- Are system templates seeded once at company activation, or re-synced on module upgrade? Copy-on-edit implies safe re-sync, but the trigger is unspecified.
- Do generated documents record provenance (which template + source) on the `dms_documents` row, or is that lost after upload?
- Should `manual` merge sources support saving reusable value sets, or is every generation one-off?
