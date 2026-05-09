# /flowflex:decision

Log an architectural decision record (ADR) to the right brain evolution log.

## Usage

```
/flowflex:decision
/flowflex:decision "Use ULID over UUID for all primary keys"
/flowflex:decision "Switch from Sanctum to Passport for API auth" status=proposed
```

## Arguments

- First string arg → decision title
- `status=` → proposed | decided | superseded (default: decided)

## What this does

1. **Determines details** — from args or asks:
   - "What was decided?"
   - "What options were considered?"
   - "What was rejected and why?"
   - "What Left Brain notes need updating?"

2. **Generates slug** — `decision-{YYYY-MM-DD}-{kebab-title}`

3. **Creates ADR file** at `flowflex-vault/right-brain/evolution/decision-{YYYY-MM-DD}-{slug}.md`:

```yaml
---
type: adr
date: {today}
status: {decided|proposed|superseded}
last_updated: {today}
---

# Decision: {Title}

> {one-sentence summary}

## Context
{what forced this decision}

## Options Considered

### Option A — {Name}
Pros: / Cons:

### Option B — {Name}
Pros: / Cons:

## Decision
Chosen: Option X
Rationale: {why}

## Consequences
Easier: / Harder: / Changed:

## Related Left Brain
- [[note-updated]]

## Related
- [[MOC_Evolution]]
```

4. **Updates MOC_Evolution** — reads `right-brain/evolution/MOC_Evolution.md`, adds row to decision log:
```
| {today} | {title} | {impact summary} | [[decision-{slug}]] |
```

5. **Updates left-brain notes** — if related left-brain notes need changing (spec diverged from decision), propose edits

6. **Output:**
```
📋 Decision logged: decision-{date}-{slug}.md
Status: {decided|proposed}
→ right-brain/evolution/decision-{slug}.md
→ MOC_Evolution updated
```
