---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.dsar
status: planned
color: "#4ADE80"
---

# DSAR Processing

Data Subject Access Request handling for GDPR compliance. Receive, track, and fulfil data access and erasure requests across all domains. Deepens the Core Data Privacy module.

## Core Features

- DSAR record: subject (name/email), request type (access/erasure/rectification/portability), status, deadline
- 30-day deadline countdown per request (GDPR requirement)
- Request status machine: `received → verifying → in_progress → completed | rejected`
- Identity verification step before processing
- Data discovery: locate all records about the subject across active domains
- Fulfilment: generate data export (access) or trigger anonymisation (erasure)
- Audit trail of every DSAR action
- Rejection with documented reason (e.g. legal hold exemption)

## Data Model

| Table | Key Columns |
|---|---|
| `legal_dsar_requests` | company_id, subject_name, subject_email, request_type, status, received_at, due_at, completed_at, assigned_to |
| `legal_dsar_actions` | dsar_id, company_id, action, domain, notes, performed_by, performed_at |

## Filament

**Nav group:** Compliance

- `DsarRequestResource` — list (deadline-sorted), create, process workflow
- `DsarFulfilmentPage` (custom page) — data discovery + export/erasure across domains

## Cross-Domain / Events

- Fires `DSARErasureRequested` → all domains anonymise matching records
- Coordinates with [[domains/core/data-privacy]] and [[architecture/patterns/encryption]] (erase encrypted fields)

## Related

- [[domains/core/data-privacy]]
- [[architecture/patterns/encryption]]
- [[architecture/event-bus]]
