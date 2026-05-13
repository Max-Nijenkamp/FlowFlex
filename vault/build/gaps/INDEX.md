---
type: build
category: gap-index
color: "#F97316"
---

# Open Gaps

Gaps are spec ambiguities, bugs discovered during builds, missing data-model decisions, or feature gaps found between a left-brain spec and what can actually be implemented. Every gap gets its own file using [[_meta/templates/tpl_gap]].

---

## Open Gaps

| ID | Module | Severity | Category | Status |
|---|---|---|---|---|

*No open gaps.*

---

## Closed Gaps

| ID | Module | Severity | Resolution |
|---|---|---|---|

*No closed gaps.*

---

## How to Add a Gap

1. Create `build/gaps/gap-{slug}.md` using [[_meta/templates/tpl_gap]]
2. Set `severity`: `high` (blocks build), `medium` (workaround exists), `low` (cosmetic or future)
3. Set `category`: `spec` | `bug` | `data-model` | `architecture` | `feature`
4. Add a row to the Open Gaps table above
5. Link the gap from the active build log under its Gaps Discovered section

## How to Close a Gap

1. Update the gap file: set `status: closed`, add a `## Resolution` section describing what was done
2. Move the row from Open Gaps to Closed Gaps in this index
3. If the resolution required a code change, reference the commit or PR

---

## Related

- [[build/ACTIVATION]] — build session workflow
- [[build/STATUS]] — domain progress
- [[_meta/templates/tpl_gap]] — gap file template
