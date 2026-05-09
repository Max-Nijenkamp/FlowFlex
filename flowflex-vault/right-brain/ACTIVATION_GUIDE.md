---
type: right-brain
section: meta
color: "#F97316"
last_updated: 2026-05-09
---

# Activation Guide

How to activate the Right Brain for a new module. Run through this checklist at the start of every build session.

---

## Pre-Build Checklist

Before writing any code for a module:

### 1. Read the Left Brain spec

- [ ] Open the domain MOC (e.g. [[MOC_HR]])
- [ ] Read the module note (e.g. `left-brain/domains/02_hr/leave-management.md`)
- [ ] Read all linked entity notes
- [ ] Read relevant concept notes

### 2. Check dependencies

- [ ] List which other modules this depends on
- [ ] Verify those modules are built (check [[STATUS_Dashboard]])
- [ ] Check cross-domain events this module emits — are listeners ready?

### 3. Create the builder log

Copy the template below into `right-brain/builder-logs/{module-name}.md`.

### 4. Update STATUS_Dashboard

Mark the module as `🔄 In progress`.

---

## Builder Log Template

```markdown
---
type: builder-log
module: {{module-name}}
domain: {{domain}}
panel: {{panel-slug}}
phase: {{phase-number}}
started: YYYY-MM-DD
status: in-progress
color: "#F97316"
left_brain_source: "[[{{module-note-slug}}]]"
last_updated: YYYY-MM-DD
---

# Builder Log: {{Module Name}}

Left Brain source: [[{{module-note-slug}}]]

## Sessions

### Session YYYY-MM-DD

**Goal:** what I want to complete today

**Built:**
- Migration: `database/migrations/100xxx_create_xxx_table.php`
- Model: `app/Models/HR/LeaveRequest.php`
- Service: `app/Services/HR/LeaveService.php`
- etc.

**Decisions made:**
- Decision 1 — rationale
- Decision 2 — rationale

**Problems hit:**
- Problem + how I solved it

**Patterns found:**
- New pattern worth generalising?

## Gaps Discovered

- [ ] [[gap_xxx]] — short description

## Lessons

- What would I do differently?
- What should be in the Left Brain spec that wasn't?
```

---

## Post-Build Checklist

After a module is complete:

- [ ] All migrations run cleanly
- [ ] All tests pass
- [ ] Filament resource renders correctly
- [ ] All events fire correctly
- [ ] Permissions registered and tested
- [ ] Left Brain spec matches what was actually built (update if diverged)
- [ ] Update [[STATUS_Dashboard]] — mark module ✅
- [ ] Move builder log to `right-brain/builder-logs/archive/`

---

## Discovered a Gap?

If you find something missing or wrong in the spec:

1. Create `right-brain/gaps/gap_{short-name}.md`
2. Link it from the builder log
3. Tag it: `#gap/architecture`, `#gap/spec`, `#gap/feature`, `#gap/bug`
4. If spec change needed, update the Left Brain note directly

---

## Related

- [[STATUS_Dashboard]]
- [[00_MOC_LeftBrain]]
- [[MOC_Roadmap]]
