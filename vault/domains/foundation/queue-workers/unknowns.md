---
domain: foundation
module: queue-workers
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Queue Workers — Unknowns

Parent: [[_module]]. Authoritative config: [[../../../infrastructure/queue-horizon]].

| # | Item | State |
|---|---|---|
| 1 | `HorizonServiceProvider` gate — exact admin roles allowed | UNVERIFIED |
| 2 | Arch test enforcing `WithCompanyContext` on all tenant jobs | *(assumed)* — not cited |
| 3 | `hr`/`finance` queues left declared but empty — cleanup vs. keep decision | open (kept to avoid supervisor churn) |
| 4 | Failure alerting (Slack/webhook on failed jobs / long waits) | not present — see [[../../_opportunities]] queue-observability |
| 5 | Per-supervisor worker counts / balance strategy in prod | UNVERIFIED — see infra note |

## Related

- [[_module]] · [[security]] · [[../../../infrastructure/queue-horizon]] · [[../../_opportunities]]
