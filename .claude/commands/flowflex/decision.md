# /flowflex:decision

Log an architectural decision record (ADR) to the vault.

## Usage

```
/flowflex:decision "Use Actions not Service for leave approval" status=decided
/flowflex:decision "Evaluate cursor pagination for activity feeds" status=proposed
/flowflex:decision "WhatsApp provider: 360dialog over Twilio" status=decided
```

## Arguments

- First string → decision title (required)
- `status=` → `decided` | `proposed` (default: `decided`)

If title not provided, ask: "What was decided? What options were considered?"

## What This Does

### Step 1 — Gather decision details

From conversation context, extract or ask for:
- Context: what forced this decision?
- Options considered (minimum 2): pros and cons of each
- The chosen option and rationale
- Consequences: what becomes easier, harder, or changed?
- Related domain or module files

### Step 2 — Generate slug

Kebab-case from title. Max 6 words. Example:
- "Use Actions not Service for leave approval" → `actions-not-service-leave-approval`

### Step 3 — Create ADR file

Create `vault/build/decisions/decision-{YYYY-MM-DD}-{slug}.md`:

```yaml
---
type: adr
date: {YYYY-MM-DD}
status: decided | proposed
domain: {domain display name, or "All"}
color: "#F97316"
---

# Decision: {Title}

## Context
{what situation or constraint forced this decision}

## Options Considered

### Option A — {Name}
**Pros:** {list}
**Cons:** {list}

### Option B — {Name}
**Pros:** {list}
**Cons:** {list}

## Decision
Chosen: Option {X}

{rationale — why this option over the alternatives}

## Consequences
- Easier: {what becomes simpler}
- Harder: {what becomes more complex}
- Changed: {what needs updating in the codebase or vault}

## Related
- [[domains/{domain}/{module}]]
- [[architecture/{relevant-pattern}]]
```

### Step 4 — Update INDEX.md

Read `vault/build/decisions/INDEX.md`. Add row to Decision Log table:
```
| {YYYY-MM-DD} | [[build/decisions/decision-{slug}\|{Title}]] | {status} | {domain or "All"} |
```
Write back.

### Step 5 — Update affected specs (if needed)

If the decision changes how a domain spec should be built, note which spec file(s) need updating and propose the change.

### Step 6 — Output

```
Decision logged: vault/build/decisions/decision-{date}-{slug}.md
Status: {decided | proposed}
INDEX.md updated.

{if spec updates needed:}
Suggested spec updates:
- vault/domains/{domain}/{module}.md — {what to update}
```
