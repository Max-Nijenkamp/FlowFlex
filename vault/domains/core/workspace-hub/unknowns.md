---
domain: core
module: workspace-hub
type: unknown
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Workspace Hub — Unknowns

> [!warning] UNVERIFIED — needs confirmation
> - **Routing shape**: single tenant panel with domain sections vs multi-panel with the hub linking out to
>   per-domain panels ([[architecture#Routing target — open question]]). Drives a lot of the build.
> - **Favourites / recency** ordering is `*(assumed)*` — confirm whether the launcher personalises order
>   or is a fixed grid.
> - **Domain vs module granularity**: does a tile represent a whole domain, or should heavily-used modules
>   get their own tiles? (Assumed: domain-level tiles.)
> - **Module-key** `core.hub` and permission `core.hub.view` are proposed, not from a prior spec.

## Related

- [[_module]] · [[../../../decisions/decision-2026-06-20-workspace-hub-and-login-model]]
