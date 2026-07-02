---
domain: legal
module: dsar-processing
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# DSAR Processing — Decisions

- **Workflow layer, not an engine.** The DSAR record + erasure/export engine stay in core.privacy; legal.dsar adds verification, per-domain action tracking, and rejection documentation. No duplicate erasure logic.
- **v1 tables/events dropped.** The v1 `legal_dsar_requests` table + `DSARErasureRequested` event were dropped — this works on `dsar_requests` directly and delegates erasure to PersonalDataRegistry jobs *(assumed)*.
- **Identity verification gates fulfilment.** When active, core.privacy processing is blocked until verified.
- **Action log is append-only + encrypted notes.** Compliance proof; notes may hold PII → encrypted.

## Related

- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../core/data-privacy/_module|core.privacy]]
