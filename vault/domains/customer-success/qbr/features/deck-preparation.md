---
domain: customer-success
module: qbr
feature: deck-preparation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deck Preparation

Auto-assemble a data-backed review deck by snapshotting active-source metrics into the QBR, so the CSM walks in with current numbers.

## Behaviour

- `prepareDeck(qbrId)` pulls health trend (`cs.health`), support summary (`support.tickets`), and deal/contract overview into `deck_data` as a point-in-time snapshot.
- Sections whose soft-dep module is **inactive** are omitted entirely (not zeroed) — the deck only shows what the company actually has.
- The snapshot is stored so the deck is stable at review time even if underlying data changes afterwards.
- A pre-QBR checklist helps the CSM confirm the deck is ready.

## UI

- **Kind**: custom-page — the deck is a bespoke read-mostly view on the QBR record (infolist + charts). (Prep itself is a `QbrResource` action; the assembled deck renders as a custom page/infolist.)
- **Page**: QBR deck view under `QbrResource` at `/crm/qbrs/{qbr}` → "Deck" tab.
- **Layout**: sections stacked — health-trend chart, support-summary stats, deal/contract overview; each section hidden when its source module is inactive; a "prepared at" timestamp + re-prepare button.
- **Key interactions**: **Prepare deck** (snapshot → `deck_data`) · re-prepare (refresh snapshot) · pre-QBR checklist toggles.
- **States**: empty (deck not yet prepared → "Prepare deck" CTA) · loading (snapshot in progress) · error (a source read fails → that section shows a soft error, others still render) · selected (a section expanded).
- **Gating**: `cs.qbr.view-any` to view; `cs.qbr.manage` to prepare/re-prepare.

## Data

- Owns / writes: `cs_qbrs.deck_data` (own snapshot).
- Reads: health trend (`cs.health`), support summary (`support.tickets`), deal/contract overview (crm) — all via read APIs, never their tables.
- Cross-domain writes: none — the deck is a self-owned snapshot ([[../../../../security/data-ownership]]).

## Relations

- Consumes: read-only signals from `cs.health`, `support.tickets`, crm.
- Feeds: nothing downstream (the deck is presentational).
- Shared entity: `crm_accounts` (read-only) + the source metrics.

## Unknowns

- PDF export of the deck + customer-facing view are opportunities, not v1 — [[../unknowns]], [[../../_opportunities]].

## Related

- [[../_module|QBR]] · [[./qbr-scheduling|QBR Scheduling]] · [[./action-items|Action Items]]
- [[../../health-scores/_module|cs.health]] · [[../../../../security/data-ownership]]
