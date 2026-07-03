---
domain: hr
module: employee-self-service
type: data-model
color: "#4ADE80"
updated: 2026-07-03
---

# Employee Self Service — Data Model

**Owns no tables.** None — aggregating portal; owns no tables, reads sibling HR modules scoped to `Auth::user()->employee`

All persistent state this module touches is owned elsewhere — see the hub's Tables line and `architecture.md` for the exact read/write surfaces. Per [[../../../_meta/spec-template|spec-template v3]], this file exists so every module folder carries the full spec set; there are no `### {table}` sections because there is nothing to migrate here.

## Related

- [[_module|Hub]] · [[architecture]] · [[security]]
