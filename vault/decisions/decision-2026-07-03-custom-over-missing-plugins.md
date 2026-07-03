---
type: adr
date: 2026-07-03
status: decided
domain: All
color: "#F97316"
---

# Custom-build over waiting for missing Filament plugins

## Context

[[../build/gaps/gap-filament5-plugins-unavailable|Open gap]]: 2 of 4 planned Filament plugins still lack a Filament v5 release (`saade/filament-fullcalendar` → calendar pages row #4, `awcodes/filament-tiptap-editor` → rich-text fields). Shield and activitylog were already replaced with custom resources (core.rbac / core.audit). The same situation can recur for any plugin between now and its consuming phase.

## Options Considered

1. Wait for upstream v5 releases — rejected: blocks phase-2+ modules on third-party timelines.
2. Downgrade Filament — rejected: v5 is the platform baseline.
3. **Build custom when no in-stack alternative exists — chosen (product owner, 2026-07-03).**

## Decision

At build time, when a planned Filament plugin has no compatible release:

1. **First** check the already-chosen stack for a covering package (CLAUDE.md Tech Stack is the allowed universe).
2. **If none covers it, build custom** — a custom Filament component/page/field owned by the consuming module, styled per [[../frontend/design-system|Switchboard+]] and citing its ui-strategy row like any custom page.
3. Record each substitution as a one-line entry in the consuming module's `decisions.md` + a row appended to the gap file — no waiting, no new third-party packages without an ADR.

Known applications now: calendar pages (#4) → custom Filament page over a JS calendar in the theme bundle *(build-time detail)*; rich-text editor → custom field over Tiptap JS directly (the JS library itself is fine; only the Filament wrapper plugin is missing) + `ezyang/htmlpurifier` sanitisation as already specced.

## Consequences

- No module blocks on plugin availability; the roadmap's phase order holds.
- Slightly more owned UI code; each custom substitute must pass [[../architecture/patterns/custom-page-checklist|the custom checklist]] like any custom artifact.
- If upstream ships later, swapping back is optional, decided per module.

## Related

- [[../build/gaps/gap-filament5-plugins-unavailable]] · [[../architecture/ui-strategy]] · [[decision-2026-06-11-static-analysis-without-larastan]] (same pattern: own it when upstream fails)
