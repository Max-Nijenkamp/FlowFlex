---
type: gap
severity: low
category: spec
status: open
color: "#F97316"
discovered: 2026-05-11
discovered_in: crm-phase3
last_updated: 2026-05-11
---

# Gap: crm_tickets missing ticket number column

## Context

During the Phase 3 CRM build, the `customer-support-helpdesk.md` spec documents a `number` column on `crm_tickets` with format `TKT-2026-00001`. The Phase 3 migration (`250007`) does not include this column.

## The Problem

The spec calls for an auto-incremented ticket number visible to agents and customers (e.g. TKT-2026-00001). The migration omits this column because sequential number generation requires either a database sequence or a service-layer counter — neither was implemented in Phase 3.

## Impact

- Tickets have no human-readable reference number
- Agents cannot reference tickets by a short ID in conversation
- Low severity because tickets are still accessible by ULID and title

## Proposed Solution

1. Add a `number` string column to `crm_tickets` (nullable, unique per company)
2. Create a `CrmTicketService::generateNumber(string $companyId): string` method using year-scoped sequence: `TKT-{YEAR}-{ZEROPADDED_SEQ}`
3. Auto-assign in `CreateCrmTicket::mutateFormDataBeforeCreate` and via a model `creating` event as fallback

## Links

- Source builder log: [[builder-log-crm-phase3]]
- Related spec: [[customer-support-helpdesk]]
