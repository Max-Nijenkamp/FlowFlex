---
type: moc
section: right-brain/evolution
color: "#F97316"
last_updated: 2026-05-09
---

# Evolution — Architectural Decisions & Pivots

Major decisions made during the build. When the spec changes from the original Left Brain design, log it here.

---

## Decision Log

| Date | Decision | Impact | File |
|---|---|---|---|
| 2026-05-09 | Filament 5 upgrade (v5.6.2) | Upgraded from Filament 4 to Filament 5 before Phase 1 begins. No code changes required — Filament 5 retained `Schema` API. Both panels boot clean. | [[decision-2026-05-09-filament-5-upgrade]] |
| 2026-05-09 | Phase 0 used Filament 4 (superseded) | Initial Phase 0 build used Filament 4 because Filament 5 appeared unavailable. Superseded by upgrade above. | [[decision-2026-05-09-filament-4-instead-of-5]] |

---

## How to Log a Decision

When a major architectural decision is made or changed:

1. Create `right-brain/evolution/decision-YYYY-MM-DD-{short-name}.md`
2. Document: what changed, why, what was tried first, what the trade-off is
3. Update relevant Left Brain notes to match
4. Add entry to this index

---

## Template

```markdown
---
type: adr
date: YYYY-MM-DD
status: decided | superseded | proposed
---

# Decision: {{title}}

## Context
What situation forced this decision?

## Options Considered
1. Option A — pros/cons
2. Option B — pros/cons

## Decision
What was chosen and why?

## Consequences
What changes? What becomes easier? What becomes harder?

## Related Left Brain
- [[note-updated]]
```

---

## Related

- [[STATUS_Dashboard]]
- [[ACTIVATION_GUIDE]]
