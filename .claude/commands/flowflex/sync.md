# /flowflex:sync

Sync the FlowFlex right brain after building or modifying a module. Updates STATUS_Dashboard, creates/updates builder log, links left↔right brain, and reports any bugs or decisions.

## What this command does

Run through ALL of these steps in order. Do not skip steps.

---

## Step 1 — Determine what was built

From the current conversation context, identify:
- Module name (e.g. `leave-management`)
- Domain (e.g. `HR & People`)
- Domain folder (e.g. `02_hr`)
- Panel slug (e.g. `hr`)
- Status: `in-progress` OR `complete`
- Any bugs/gaps found during the session
- Any architectural decisions made

If args were passed to this command, parse them:
- `/flowflex:sync module=leave-management status=complete`
- `/flowflex:sync module=auth-rbac status=in-progress bugs="JWT refresh broken"`

If unclear, ask one question: "Which module and domain, and is it complete or in-progress?"

---

## Step 2 — Update left-brain spec frontmatter

Read the left-brain spec file: `flowflex-vault/left-brain/domains/{DD_domain}/{module-name}.md`

Update the frontmatter:
- `status:` → set to `in-progress` or `complete`
- `last_updated:` → today's date (YYYY-MM-DD)
- Add or update: `right_brain_log: "[[builder-log-{module-name}]]"`

Example updated frontmatter:
```yaml
---
type: module
domain: HR & People
panel: hr
phase: 2
status: complete
migration_range: 100000–109999
last_updated: 2026-05-09
right_brain_log: "[[builder-log-leave-management]]"
---
```

---

## Step 3 — Create or update builder log

Check if `flowflex-vault/right-brain/builder-logs/{module-name}.md` exists.

**If it does NOT exist:** Create it using the template from `flowflex-vault/_core/_templates/tpl_builder-log.md`. Populate:
- `module:` → module slug
- `domain:` → domain name
- `panel:` → panel slug
- `started:` → today
- `status:` → in-progress or complete
- `left_brain_source:` → link to left-brain spec

Add a session entry under `## Sessions` for today's date with:
- What was built (migrations, models, services, resources)
- Decisions made during this session
- Problems encountered and how solved
- Any gaps discovered

**If it DOES exist:** Read it, then add a new session entry for today.

---

## Step 4 — Update STATUS_Dashboard

Read `flowflex-vault/right-brain/STATUS_Dashboard.md`.

Find the row for the relevant domain. Update the `Built` column:
- If status is `in-progress`: add 🔄 emoji next to module count if not already there
- If status is `complete`: increment the Built number by 1, recalculate Progress %

Also update the pie chart mermaid block to reflect new completion.

Format: `| Domain | Phase | Built | Total | Progress |`
- Progress % = (Built / Total) * 100, rounded to nearest integer
- Emoji: 📅 = 0%, 🔄 = in-progress, ✅ = 100%

---

## Step 5 — Handle bugs / gaps (if any)

For each bug or gap identified during the session:

1. Create `flowflex-vault/right-brain/gaps/gap_{short-name}.md` using `tpl_gap` frontmatter:
```yaml
---
type: gap
severity: high | medium | low
category: spec | architecture | feature | bug | data-model
status: open
discovered: YYYY-MM-DD
discovered_in: {module-name}
last_updated: YYYY-MM-DD
---
```

2. Document: what's wrong, what breaks, options to fix

3. Link the gap from the builder log under `## Gaps Discovered`

4. Update `flowflex-vault/right-brain/gaps/MOC_Gaps.md` — add row to the gap index table

---

## Step 6 — Handle architectural decisions (if any)

For each significant architectural decision made:

1. Create `flowflex-vault/right-brain/evolution/decision-{YYYY-MM-DD}-{short-name}.md` using `tpl_adr` frontmatter

2. Document: context, options, decision, consequences

3. Update `flowflex-vault/right-brain/evolution/MOC_Evolution.md` — add row to decision log table:
```markdown
| 2026-05-09 | Short decision title | Impact summary | [[decision-file]] |
```

---

## Step 7 — Output sync report

After all steps complete, output a brief report:

```
## FlowFlex Right Brain Sync Complete

**Module:** leave-management (HR & People)
**Status:** ✅ complete

**Updated:**
- Left-brain spec: status → complete, right_brain_log linked
- Builder log: created/updated at right-brain/builder-logs/leave-management.md
- STATUS_Dashboard: HR Built = 1/19 (5%)

**Gaps logged:** 1
- gap_hr-leave-overlap-calculation.md (severity: medium)

**Decisions logged:** 0
```
