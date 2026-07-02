---
domain: crm
module: contracts
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contracts — Unknowns & Open Questions

## Assumptions

- `billing_interval` set is one-off / monthly / yearly *(assumed)* — drives the recurring-revenue calculation.
- Renewal alert cadence is 90 / 30 days before expiry *(assumed)*.
- DocuSign / native e-sign is deferred to a later ADR *(assumed)*; v1 is manual signed-PDF upload.

## Open Questions

- Which billing intervals must the recurring-revenue calculation normalise (quarterly? weekly?)
- Should renewal alert cadence be per-company configurable rather than a fixed 90/30?
- What is the max PDF upload size cap for signed contracts?
- When Legal Contracts (P3) ships, how does a CRM contract hand off / link to a full legal record?
