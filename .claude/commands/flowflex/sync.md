# /flowflex:sync

Sync the vault after any build work. Updates module status, STATUS.md progress, and logs gaps and decisions. Run at end of every build session.

## Usage

```
/flowflex:sync hr.leave status=in-progress
/flowflex:sync finance.invoicing status=complete
/flowflex:sync hr.leave status=in-progress "Added leave request model and migration"
```

## Arguments

- First arg: module key (e.g. `hr.leave`)
- `status=` → `in-progress` | `complete`
- Optional description string → summary note for Recent Sessions table

If module key not provided, infer from conversation context. If unclear, ask: "Which module key and status?"

## What This Does

### Step 1 — Update module spec frontmatter

Find the module file:
- Parse domain and module from key: `hr.leave` → `vault/domains/hr/leave-management.md`
- If filename ambiguous, scan `vault/domains/{domain}/` for file with matching `module-key:` frontmatter

Read the file. Update only the `status:` field:
```yaml
status: in-progress   # or: complete
```

Write the file back. No other frontmatter fields change.

### Step 2 — Update STATUS.md

Read `vault/build/STATUS.md`.

**If status = `complete`:** increment Built by 1, recalculate Progress %.

Update emoji: 🔴 = 0%, 🟡 = 1–99%, ✅ = 100%.

Add a row to Recent Sessions:
```
| {YYYY-MM-DD} | {domain} | {module-key} | {✅ or 🔄} | {description} |
```

Write STATUS.md back.

### Step 3 — Create gap files (only if bugs/spec gaps found)

For each gap discovered, create `vault/build/gaps/gap-{slug}.md`:
```yaml
---
type: gap
severity: high | medium | low
category: spec | architecture | feature | bug | data-model
status: open
color: "#F97316"
discovered: {YYYY-MM-DD}
discovered-in: {module-key}
---
```

Body: Problem, Impact, Proposed Solution, Related links.

Read `vault/build/gaps/INDEX.md`, add row, write back.

### Step 4 — Create ADR files (only if architectural decisions made)

Create `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md`:
```yaml
---
type: adr
date: {YYYY-MM-DD}
status: decided | proposed
color: "#F97316"
---
```

Body: Context, Options Considered, Decision, Consequences, Related links.

Read `vault/build/decisions/INDEX.md`, add row, write back.

### Step 5 — Output

```
## Sync: {module-key}
Status: {in-progress | ✅ complete}
Spec updated: vault/domains/{domain}/{module}.md
STATUS.md: {domain} {Built}/{Total} ({%}%)
Gaps: {n} | Decisions: {n}
```
