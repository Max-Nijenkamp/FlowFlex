---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.matters
status: planned
color: "#4ADE80"
---

# Matter Management

Track legal matters (disputes, cases, advisory work): status, assigned counsel, related documents, deadlines, and spend.

## Core Features

- Matter record: title, type (litigation/advisory/dispute/IP), status, internal owner, external counsel
- Status machine: `open → active → on_hold → closed`
- Matter timeline: key events and deadlines
- Document association (linked from DMS)
- External counsel/law firm details
- Spend tracking per matter (links to Legal Spend module)
- Priority and risk level
- Matter notes and updates log

## Data Model

| Table | Key Columns |
|---|---|
| `legal_matters` | company_id, title, type, status, owner_id, external_counsel, priority, risk_level, opened_at, closed_at |
| `legal_matter_events` | matter_id, company_id, title, event_date, notes, created_by |

## Filament

**Nav group:** Matters

- `MatterResource` — list, create, edit, view
- Matter timeline as relation manager
- Spend summary on view page

## Related

- [[domains/legal/legal-spend]]
- [[domains/legal/legal-contracts]]
- [[domains/dms/document-library]]
