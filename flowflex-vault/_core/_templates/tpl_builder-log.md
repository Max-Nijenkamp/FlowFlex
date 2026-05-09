---
type: builder-log
module: {{module-name}}
domain: {{domain}}
panel: {{panel}}
started: {{DATE}}
status: in-progress | complete | blocked
left_brain_source: "[[module-note]]"
last_updated: {{DATE}}
---

# Builder Log: {{Module Name}}

Left Brain source: [[module-note-link]]

---

## Sessions

### Session {{DATE}}

**Goal:** What I want to complete today

**Built:**
- Migration: `database/migrations/{{range}}_create_{{table}}_table.php`
- Model: `app/Models/{{Domain}}/{{Model}}.php`
- Service: `app/Services/{{Domain}}/{{Service}}.php`
- Interface: `app/Interfaces/{{Domain}}/{{Interface}}.php`
- Filament Resource: `app/Filament/{{Panel}}/Resources/{{Resource}}Resource.php`

**Decisions made:**
- Decision 1 — rationale
- Decision 2 — rationale

**Problems hit:**
- Problem description + how it was solved

**Patterns found:**
- New pattern worth adding to concepts?

---

## Gaps Discovered

- [ ] [[gap_xxx]] — short description

---

## Lessons

- What would I do differently?
- What should be in the Left Brain spec that wasn't?

---

## Post-Build Checklist

- [ ] All migrations run cleanly (`php artisan migrate`)
- [ ] All tests pass (`php artisan test --filter={{Module}}`)
- [ ] Filament resource renders correctly
- [ ] All events fire correctly (check logs)
- [ ] Permissions registered and tested
- [ ] Left Brain spec matches what was built (update if diverged)
- [ ] [[STATUS_Dashboard]] updated — mark module ✅
- [ ] Move this log to `right-brain/builder-logs/archive/`

---

## Related

- [[ACTIVATION_GUIDE]]
- [[STATUS_Dashboard]]
