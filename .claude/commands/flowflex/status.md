# /flowflex:status

Check current build state. Read-only — never modifies any vault file.

## Usage

```
/flowflex:status
/flowflex:status domain=hr
/flowflex:status full
/flowflex:status domain=finance full
```

## Arguments

- No args → summary table from STATUS.md
- `domain={slug}` → detailed view for one domain (all modules + status)
- `priority={v1-core|v1|p2|p3}` → only modules with that `priority:` frontmatter (e.g. `priority=v1-core` = what blocks the v1 gate)
- `full` → all domains + open gaps + recent decisions

## What This Does

### No args — Summary View

Read `vault/build/STATUS.md`. Display the Progress table:

```
## FlowFlex Build Status — {date}

| Phase | Domain          | Built | Total | Progress |
|-------|-----------------|-------|-------|----------|
| MVP   | Foundation      |   0   |   7   | 🔴 0%   |
| MVP   | Core Platform   |   0   |  15   | 🔴 0%   |
| MVP   | HR & People     |   0   |  15   | 🔴 0%   |
| MVP   | Finance         |   0   |  13   | 🔴 0%   |
| MVP   | CRM & Sales     |   0   |  15   | 🔴 0%   |
...

MVP: 0 / 65 built (0%)
```

### `domain={slug}` — Domain Detail View

1. Read `vault/domains/{slug}/_index.md` for the module list
2. Read each module file's `status:` frontmatter
3. Display per-module status:

```
## HR & People — Build Status
Panel: /hr (Violet) | 0/15 built

✅ employee-profiles     complete      v1-core
🔄 leave-management     in-progress   v1-core
📅 onboarding           planned       v1-core
📅 payroll              planned       v1-core
...

Next to build: payroll (first planned after in-progress, per BUILD-ORDER)
```

### `priority={level}` — Priority Filter

Scan module specs' `priority:` frontmatter (or `_meta/module-graph.md`). Show only matching modules, grouped by domain, with status. Combine with `domain=` to narrow further.

### `full` — Full Status View

Everything above plus:

Read `vault/build/gaps/INDEX.md` — show open gaps:
```
Open Gaps:
- gap-hr-leave-overlap (medium) — hr.leave — 2026-06-01
```

Read `vault/build/decisions/INDEX.md` — show last 5 decisions:
```
Recent Decisions:
- 2026-06-01 — Raw Stripe SDK vs Cashier (decided)
- 2026-06-01 — Salary history table (decided)
```
