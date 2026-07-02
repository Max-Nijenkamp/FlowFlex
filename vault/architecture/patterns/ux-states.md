---
type: architecture
category: pattern
pattern-key: ux-states
status: stable
last-reviewed: 2026-07-02
color: "#A78BFA"
---

# UX States — empty, error, hover, steps

Every screen state is designed, never default. A blank screen tells nothing; a state should teach, reassure or move the user forward. These rules apply to every Filament resource, page and widget, and to the public Vue surface.

Platform defaults live in `AppServiceProvider` (`Table::configureUsing`) and `resources/css/filament/flowflex-skin.css` — resources only override when they have something better to say.

---

## 1. Empty States — the first impression

**Rule one: a blank screen tells nothing. A tiny illustration + one human line + one action gives everything.**

There are **four kinds of empty** — design each one differently, never reuse one message for all four:

| Kind | When | Heading tone | Action |
|---|---|---|---|
| **First-use** | Brand new workspace, feature never used | Teach what the feature does — empty state is the best onboarding | Primary: create the first record (e.g. "Add your first employee") |
| **Emptied** | Had records, now zero | Confirm, don't alarm ("All invoices are paid — nothing open.") | Contextual: create new / view archive |
| **Filtered out** | Records exist, filters/search hide all | Say WHY it's empty ("No results for 'jansen' with status Active") | Clear filters / widen search — never just "No records found" |
| **Error** | Load failed, timeout | Human, not a log file ("Couldn't load this list. Your connection or our server hiccuped.") | Retry + what to do if it persists — a retry button, not a manual refresh |

Hard rules:
- **Every empty state has an action.** "Refresh the page" is not an action.
- **First-use empty states are onboarding** — one sentence on what the feature does, then the button that starts it.
- Platform default (set centrally): heading "Nothing here yet", description "The moment you add your first record, it shows up here." Resources override with `->emptyStateHeading()/->emptyStateDescription()/->emptyStateActions()` whenever a specific message teaches more.
- Skin renders the icon container with the panel tint + blueprint corner tick (`.fi-ta-empty-state-icon-bg`).

## 2. Error States — sound human, not like a log file

- Never surface exception text, codes or "An unexpected error has occurred."
- Pattern: *what happened* + *what it means* + *what to do*. "Couldn't save. Check your connection and try again." (brand.md voice table is the source of truth.)
- Timeouts: optimistic UI keeps stale content visible at 60% opacity (`wire:loading.delay`) — no spinner walls; if an action times out, say so and offer retry.
- Validation errors name the field in plain words: "Email address is missing." — never "Invalid input."
- Full-page HTTP errors (403/404/419/429/500/503) and in-panel Livewire component crashes have their own designed surfaces — copy, actions and layout in [[error-pages]]. Same voice: what happened + what it means + what to do, never a stack trace.
- Concurrent-edit conflicts surface the "This record was changed by someone else" notification with a **Reload record** action — never a silent merge. Mechanism in [[optimistic-locking]].

## 3. Hover, Selected, Pressed — three different states

- **Hover ≠ selected.** Hover = light 5% primary wash (preview). Selected = 10% primary tint + 2px primary left edge (`.fi-ta-row.fi-selected`). A user must always be able to tell them apart at a glance.
- **Hover previews fill the UI**: rating stars fill on hover before the click; toggle-rows tint before selection. Hovering is a preview of the click.
- **Press = pop**: every button acknowledges the tap instantly — `active:scale-[0.98]` (Vue) / `.fi-btn:active { transform: scale(0.97) }` (panels). The animation IS the feedback; never wait for the round-trip.

## 4. Long Forms — chunk into steps

- **12 fields in one column is annoying. Chunk.** Forms with more than ~8 fields or more than 2 distinct topics become a Filament **Wizard** (steps) or sectioned form.
- **Steps validate themselves** — each step validates on "Next" (Filament Wizard does this natively). Never collect 12 fields and dump all errors at the end.
- Progress: linear bar, numbered steps or labeled steps are all fine — what matters is the chunking and that the user always sees where they are.
- Step labels are topics ("Contract", "Compensation"), not numbers alone; descriptions optional (12px faint).
- Skin styles step numbers mono, active step in panel color (`.fi-sc-wizard-header-*`).

---

## Where this is enforced

| Layer | Mechanism |
|---|---|
| Table empty states | `Table::configureUsing()` default in `AppServiceProvider` + per-resource overrides |
| Empty-state visuals | `flowflex-skin.css` `.fi-ta-empty-state*` |
| Selected vs hover | `flowflex-skin.css` `.fi-ta-row.fi-selected` |
| Press pop | `flowflex-skin.css` `.fi-btn:active`, Vue `active:scale-[0.98]` |
| Wizard steps | Filament `Wizard` component + `.fi-sc-wizard-*` skin |
| Error copy | [[../../product/brand|brand.md]] voice table |

## Related

- [[perceived-performance]] — skeletons, optimistic UI, motion timing
- [[custom-pages]] — custom page layouts still follow these states
- [[../../product/brand|Brand]] · [[../../product/ux-principles|UX principles]]
