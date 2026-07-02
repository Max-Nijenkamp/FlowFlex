---
domain: crm
module: contracts
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contracts — Decisions

## ADR: Manual signed-PDF upload for v1 e-signature

**Status**: decided (from spec). E-signature in v1 is a manual signed-PDF upload plus a `signed_at` flag. DocuSign / native e-sign is deferred to a later ADR *(assumed)*.

**Consequences**: no third-party e-sign integration cost or dependency for v1; signature provenance is operator-attested, not cryptographically verified.

## ADR: Sales contracts distinct from Legal contracts

**Status**: decided (from spec). `crm.contracts` covers the sales-focused lifecycle (value, renewal, recurring revenue). Full legal contract management lives in [[../../legal/legal-contracts/_module|Legal Contracts]] (P3 soft dependency).

**Consequences**: two contract surfaces; CRM contract keeps a lean model and defers deep clause/redline management to Legal.

## ADR: 90/30-day renewal alerts with once-guards

**Status**: decided (assumed cadence). Renewal alerts fire at 90 and 30 days before expiry, tracked via `alerted_levels` jsonb so each level fires once.

**Consequences**: idempotent alerting under a daily lifecycle command; cadence is *(assumed)* and overridable via ADR.
