---
type: build
category: adr-index
color: "#F97316"
---

# Architectural Decisions

Architectural Decision Records (ADRs) capture significant technical choices made during the build. Every non-trivial decision that affects multiple modules or future work gets its own ADR file using [[_meta/templates/tpl_adr]].

---

## Decision Log

| Date | Decision | Impact |
|---|---|---|
| 2026-05-13 | Use Filament 5 (not v3) | All panels use Filament 5.6.3 API — `authModel()` removed, providers in `app/Providers/Filament/` | [[adr-2026-05-13-filament-v5]] |
| 2026-05-13 | `string(26)` for permission morph/team keys | ULID-keyed models work with spatie/laravel-permission v7 | [[adr-2026-05-13-permission-ulid]] |

---

## What Qualifies as an ADR

Record a decision when you:

- Choose between two valid implementation approaches and the choice has lasting consequences
- Deviate from the pattern established in [[architecture/filament-patterns]] or [[architecture/tech-stack]]
- Make a data-model choice that affects multiple domains (e.g. shared polymorphic relationship)
- Decide on a third-party library or integration strategy
- Resolve a cross-domain dependency in a non-obvious way

Routine implementation choices (field names, UI layout, label wording) do not need an ADR.

---

## How to Add a Decision

1. Create `build/decisions/adr-{YYYY-MM-DD}-{slug}.md` using [[_meta/templates/tpl_adr]]
2. Set `status`: `decided` (in effect) or `proposed` (under discussion)
3. Document context, options considered, the decision, and consequences
4. Add a row to the Decision Log table above
5. Update any left-brain spec files affected by the decision (add a note or update a section)

---

## Related

- [[build/ACTIVATION]] — build session workflow
- [[build/gaps/INDEX]] — open gaps (sometimes gaps trigger decisions)
- [[_meta/templates/tpl_adr]] — ADR file template
- [[architecture/filament-patterns]] — canonical patterns decisions should align with
