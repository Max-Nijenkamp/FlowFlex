---
domain: hr
module: employee-profiles
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Decisions — Employee Profiles

> Module-local design choices captured from the spec. Module-wide strip is ADR [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

## Notable Local Choices

- **Department UI as simple list in v1** — `DepartmentResource` modelled as a tree via `parent_department_id`, but rendered as a simple list for v1 *(assumed: simple list v1)*. Tree rendering deferred.
- **Sequential employee numbers via advisory lock** — `hire()` assigns the next per-company `employee_number` under an advisory lock per company *(assumed)*, to keep numbers sequential + unique under concurrent creates.
- **Manual `on_leave` transition in v1** — `active → on_leave` is operator-driven; auto-transition from approved long leave is deferred *(assumed: manual v1)*.
- **Suspension disables portal login** — `active → suspended` disables portal login *(assumed)*.
- **`view-sensitive` permission gates encrypted display** — a dedicated permission *(assumed)* gates display of `national_id` / `date_of_birth` rather than reusing `view`.
- **`birth_year` derived column** — a non-encrypted `birth_year` smallint *(assumed)* supports age-range queries without decrypting `date_of_birth`.

See [[unknowns]] for the full list of assumptions to confirm.
