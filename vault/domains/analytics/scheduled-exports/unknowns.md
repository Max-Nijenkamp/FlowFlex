---
domain: analytics
module: scheduled-exports
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Scheduled Exports — Unknowns & Assumptions

All items unverified — authoritative defaults at build time, overridable via ADR.

---

## Open Questions

1. **Attach-vs-link threshold.** 10 MB *(assumed)*. Confirm the size and the signed-link TTL.
2. **External recipients.** Company users only in v1 *(assumed)*. Will external email recipients be allowed later (needs consent/validation)?
3. **Log retention.** 90 days *(assumed)*. Confirm the prune window.
4. **Run cadence.** Every 15 min tick *(assumed)*. Fine enough for `send_at` precision?
5. **Failure notification.** A failed generation is logged — is the schedule owner also notified? Unconfirmed.
6. **Financial source.** `finance.reporting` as a source is a soft dep *(assumed available)* — confirm its read path.

---

## Assumed Items (unverified)

- `*(assumed)*` — 10 MB attach-vs-signed-link threshold.
- `*(assumed)*` — no external email recipients in v1.
- `*(assumed)*` — 90-day log retention.
- `*(assumed)*` — 15-min run cadence.
- `*(assumed)*` — `time`/`jsonb` column types.

> [!warning] UNVERIFIED
> No codebase exists (stripped to app/admin shell — [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]). Every threshold, cadence, and column type is spec-derived.
