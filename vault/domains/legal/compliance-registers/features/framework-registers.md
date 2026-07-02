---
domain: legal
module: compliance-registers
feature: framework-registers
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Framework Registers

Named compliance frameworks (GDPR, ISO 27001, SOC 2, custom) that group controls.

## Behaviour

- Framework = name + description; owns a set of controls.
- GDPR framework + control set seeded on activation *(assumed)*.
- Custom frameworks can be created for industry-specific regs.

## UI

- **Kind**: simple-resource
- **Page**: `FrameworkResource` — list + create/edit at `/legal/compliance/frameworks`.
- **Layout**: table (name, control count, readiness %); form = name + description; controls as a relation tab.
- **Key interactions**: create framework; open controls tab; readiness badge per row.
- **States**: empty ("Add a framework (GDPR seeded on activation)") · loading (skeleton) · error (validation) · selected (row → controls).
- **Gating**: view `legal.compliance.view-any`; manage `legal.compliance.manage-frameworks`.

## Data

- Owns / writes: `legal_frameworks`.
- Reads: own `legal_controls` for counts/readiness.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: frameworks group controls consumed by the [[./audit-readiness-dashboard|dashboard]].
- Shared entity: none.

## Unknowns

- `*(assumed)*` GDPR seed content — [[../unknowns]].

## Related

- [[../_module|Compliance Registers]] · [[./control-management]] · [[./audit-readiness-dashboard]]
