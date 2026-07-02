---
domain: lms
module: skills-matrix
feature: skills-heatmap
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Skills Heat-map

A visual employees × skills matrix that highlights coverage and gaps at a glance.

## Behaviour

- Rows = employees (scoped: team/department for managers, all for admins); columns = skills.
- Each cell shows the manager-assessed proficiency (0–3), colour-coded.
- Cells below the role's `required_level` are highlighted as gaps.
- The query avoids N+1 across employees × skills.

## UI

- **Kind**: custom-page  <!-- ui-strategy row #18 heat-map/matrix -->
- **Page**: "Skills Matrix" (`SkillsMatrixPage`, `/lms/skills/matrix`)
- **Layout**: sticky first column (employees) + horizontally scrollable skill columns; colour scale legend; filters (category, department); click a cell → slide-over with self-vs-manager detail + recommended courses.
- **Key interactions**: filter by category/department; click cell → detail; toggle "gaps only".
- **States**: empty (no employees/skills → "Add skills and assess your team") · loading (skeleton grid) · error (load fail → retry) · selected (cell highlighted, slide-over open).
- **Gating**: `lms.skills.view-any`; assessment edits from the slide-over need assess permissions.

## Data

- Owns / writes: nothing (read-only view; edits route through `SkillService::assess`).
- Reads: `lms_employee_skills`, `lms_role_skills`, `lms_skills`; HR employees/reporting for scope.
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: nothing (a visualisation).
- Shared entity: HR employees (read-only).

## Unknowns

- Whether the matrix should span the whole company or only a manager's reports by default — see [[../unknowns]].

## Related

- [[../_module|Skills Matrix module]] · [[skill-catalogue]] · [[gap-analysis]] · [[../architecture]]
