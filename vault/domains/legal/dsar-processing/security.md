---
domain: legal
module: dsar-processing
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# DSAR Processing — Security

## Access contract

`canAccess() = Auth::user()->can('legal.dsar.view-any') && BillingService::hasModule('legal.dsar')` per [[../../../architecture/filament-patterns]] #1. (`view-any` is the base gate; process/verify/reject are separate grants.)

## Encryption

`legal_dsar_actions.notes` is an `encrypted` cast (stored in a `text` column) — it may reference data-subject PII. See [[../../../architecture/patterns/encryption]].

## Identity gate

When this module is active, core.privacy DSAR fulfilment is blocked until `LegalDsarService::verify` records a verified action — prevents fulfilling a request for an unverified subject.

## Append-only audit

`legal_dsar_actions` is append-only compliance proof — never updated/purged ([[../../../architecture/data-lifecycle]]).

## Permissions

`legal.dsar.process` · `legal.dsar.verify` · `legal.dsar.reject`

## Data ownership

Writes only `legal_dsar_actions`. The DSAR record + erasure/export engine belong to core.privacy — read + delegate only, never write privacy tables ([[../../../security/data-ownership]]).
