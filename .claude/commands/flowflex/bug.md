# /flowflex:bug

Log a bug, spec gap, or missing implementation detail to the vault.

## Usage

```
/flowflex:bug "Leave overlap detection missing for multi-type requests" module=hr.leave severity=medium
/flowflex:bug "VAT reverse charge not in tax-management spec" module=finance.tax severity=low category=spec
/flowflex:bug "Invoice PDF missing company logo" module=finance.invoicing severity=high
```

## Arguments

- First string → bug description (required)
- `module=` → module key (e.g. `hr.leave`)
- `severity=` → `high` | `medium` | `low` (default: `medium`)
- `category=` → `bug` | `spec` | `architecture` | `data-model` | `feature` (default: `bug`)

If description not provided, ask: "What is the issue? Which module? Severity?"

## What This Does

### Step 1 — Generate slug

Kebab-case from description. Max 5 words. Example:
- "Leave overlap detection missing" → `hr-leave-overlap-detection`

### Step 2 — Create gap file

Create `vault/build/gaps/gap-{slug}.md`:

```yaml
---
type: gap
severity: {high | medium | low}
category: {bug | spec | architecture | data-model | feature}
status: open
color: "#F97316"
discovered: {YYYY-MM-DD}
discovered-in: {module-key}
---

# Gap: {Description}

## Problem
{what is wrong, missing, or inconsistent}

## Impact
{what breaks or cannot be built correctly without this being resolved}

## Proposed Solution
{how to fix — options if multiple exist}

## Related
- [[domains/{domain}/{module}]]
- [[build/gaps/INDEX]]
```

### Step 3 — Update INDEX.md

Read `vault/build/gaps/INDEX.md`. Add row to Open Gaps table:
```
| gap-{slug} | {severity} | {module-key} | {one-line description} | {date} |
```
Write back.

### Step 4 — Output

```
Gap logged: vault/build/gaps/gap-{slug}.md
Severity: {level} | Module: {module-key} | Category: {category}
INDEX.md updated.
```
