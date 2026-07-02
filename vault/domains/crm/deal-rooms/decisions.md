---
domain: crm
module: deal-rooms
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deal Rooms — Decisions

## ADR: Tokenised, expiring link for external access

**Status**: decided (from spec). External buyers reach a room via a unique `access_token` (uuid) with an `expires_at` and a `revoked_at` kill switch, resolved on the guest guard — no external buyer accounts.

**Consequences**: no external user provisioning; access is revocable and time-boxed; company context is derived from the token, so the public route must never touch the app guard.

## ADR: Buyers cannot upload in v1

**Status**: decided (assumed). Buyer-side writes are limited to toggling action-item status and logging document views *(assumed)*. Buyer document upload is deferred.

**Consequences**: smaller external attack surface for v1; sellers curate all shared documents.

## ADR: Q&A via action items in v1

**Status**: decided (from spec). The Q&A thread is modelled as action items with comments for v1; a dedicated Q&A thread is a later enhancement *(assumed)*.

**Consequences**: one shared list serves both the mutual action plan and light Q&A; a richer threaded model may follow.
