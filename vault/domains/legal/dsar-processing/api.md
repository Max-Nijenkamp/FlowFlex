---
domain: legal
module: dsar-processing
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# DSAR Processing — Service API

## DTOs

- `RecordDsarActionData` — dsar_request_id, action (in set), domain?, notes (required_if rejected,rectified).
- `VerifyIdentityData` — dsar_request_id, method (in: email-challenge, document, in-person *(assumed)*), notes.

## Methods

| Method | Purpose | Writes |
|---|---|---|
| `LegalDsarService::verify(VerifyIdentityData)` | verify subject; unblock core.privacy processing | `legal_dsar_actions` |
| `LegalDsarService::discovery(requestId): array` | registry tables for subject | (read core.privacy) |
| `RecordDsarActionAction(RecordDsarActionData)` | append action | `legal_dsar_actions` |

## Events

- Consumes `DSARRequestSubmitted` (core.privacy) → `CreateLegalReviewListener` (queued, WithCompanyContext).
- Fires: none. Fulfilment delegates to core.privacy jobs.

## Read surface

- Reads `dsar_requests` + `PersonalDataRegistry` (core.privacy), read-only.
