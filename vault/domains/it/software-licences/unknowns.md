---
domain: it
module: software-licences
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Software Licences — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR. Design-affecting items should be resolved before implementation begins.

---

## Open Questions

> [!warning] UNVERIFIED
> **finance.expenses integration is report-only.**
> The spec marks the finance.expenses spend-visibility link as `*(assumed)*` — a read-only report link with no event and no write into the finance domain. Confirm whether finance actually needs a licence-spend feed (event-driven) before v1, or whether the read-only report suffices.

> [!warning] UNVERIFIED
> **Renewal alert lead time is 30 days.**
> `LicenceRenewalAlertCommand` is assumed to alert 30 days before `renewal_date`. Confirm the lead-time window (and whether it should be per-company configurable) before wiring the command.

> [!warning] UNVERIFIED
> **Widget horizon is 60 days.**
> `LicenceRenewalWidget` is assumed to show renewals in the next 60 days. Confirm the horizon and whether flagged-seat count belongs in the same widget or a separate one.

> [!warning] UNVERIFIED
> **Reclaim flag is advisory, not auto-revoke.**
> On `EmployeeOffboarded`, seats are *flagged* (`reclaim_flagged_at`) rather than auto-revoked, leaving the actual revoke to an admin. Confirm whether offboarding should auto-revoke instead of flag.

---

## Assumed Items (verbatim from spec, unverified)

- `*(assumed)*` — finance.expenses soft dependency is a spend-visibility **report link only** (read-only; no write, no event).
- `*(assumed)*` — default `billing_cycle` set is `monthly` / `annual`; other cadences (quarterly, per-user metered) not modelled.
- `*(assumed)*` — `renewal_alerted_at` cleared on any `renewal_date` change is the sole cycle-reset trigger.
