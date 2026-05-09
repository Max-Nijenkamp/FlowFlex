# /flowflex:bug

Quickly log a bug, gap, or missing spec as a gap note in the right brain. Links it to the relevant module automatically.

## Usage

```
/flowflex:bug
/flowflex:bug "JWT refresh token not invalidated on logout"
/flowflex:bug "Missing soft delete on company_users table" module=core-platform severity=high
```

## Arguments (all optional)

- First string arg → bug description
- `module=` → which left-brain module this came from
- `severity=` → high | medium | low (default: medium)
- `category=` → bug | spec | architecture | data-model | feature (default: bug)

## What this does

1. **Determines details** — from args or asks: "What's the bug? Which module? Severity?"

2. **Generates slug** — kebab-case short name from description (e.g. `jwt-refresh-not-invalidated`)

3. **Creates gap file** at `flowflex-vault/right-brain/gaps/gap_{slug}.md`:

```yaml
---
type: gap
severity: {high|medium|low}
category: {bug|spec|architecture|data-model|feature}
status: open
discovered: {today}
discovered_in: {module}
last_updated: {today}
---

# Gap: {Description}

> {one-line summary}

## Problem

{detailed description}

## Impact

- Module(s) affected: [[module-name]]
- What breaks:

## Options

### Option A — (recommended)
...

## Related

- [[MOC_Gaps]]
- [[{module-name}]]
```

4. **Updates MOC_Gaps** — reads `flowflex-vault/right-brain/gaps/MOC_Gaps.md`, adds row to the open gaps table:
```
| gap_{slug} | {severity} | {module} | {today} | open |
```

5. **Links from builder log** — if an active builder log exists for this module, appends to its `## Gaps Discovered` section

6. **Output:**
```
🐛 Gap logged: gap_{slug}.md
Severity: {level} | Module: {module}
→ right-brain/gaps/gap_{slug}.md
→ MOC_Gaps updated
```
