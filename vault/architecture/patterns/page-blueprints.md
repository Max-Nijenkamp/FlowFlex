---
type: architecture
category: patterns
pattern-key: page-blueprints
status: stable
last-reviewed: 2026-07-02
color: "#A78BFA"
---

# Page Blueprints — Composition per Custom Page Kind

[[custom-pages]] covers the PHP/Blade *structure* of a custom Filament page (instance `$view`, `getSlug()`, `canAccess()`, Livewire lifecycle). This file covers the *composition* — what regions a Kanban / Calendar / Dashboard / … page must contain, how it is skinned in Switchboard+ vocabulary, which UX states each region designs, its realtime default, and its canonical interaction set.

**One blueprint per interactive [[ui-strategy]] kind** — rows 3–11 and 17–19, plus the notification bell (row 10, a render hook not a page). A resource (rows 1–2) with a board/timeline sub-view cites the relevant blueprint fragment via a [[ui-strategy#Resource Tweak Taxonomy|resource tweak]].

---

## How specs cite these

Every custom page in a module's `## Filament Artifacts` table (`architecture.md`) cites both its ui-strategy row **and** its blueprint kind:

```
| `PipelineBoardPage` | #3 Kanban ([[../../../architecture/ui-strategy]]) — [[../../../architecture/patterns/page-blueprints#Kanban]] | Reverb; drag = MoveDeal |
```

The blueprint is the acceptance contract: the page must contain every mandatory region of its kind, design every listed state, and use the named realtime default (or justify a change in `security.md`). Conformance is gated at build time by [[custom-page-checklist]] item 3.

**Hard rule** — a custom page that matches **no** blueprint below is not buildable. Introducing a new page kind requires an ADR **and** a new [[ui-strategy]] row **first**, then a blueprint added here. No silent kinds. (Same rule as ui-strategy: a UI kind outside the table needs an ADR.)

Skin vocabulary referenced throughout is defined once in [[../../frontend/design-system|Switchboard+]] — paper canvas, ink surfaces, mono meta, `blueprint-cell` stat tiles, domain accent via `--primary-*` (never a hardcoded domain color). State kinds (first-use / emptied / filtered-out / error) come from [[ux-states]]; skeleton shapes from [[perceived-performance]].

---

## Kanban

Row #3. Reference: `projects.kanban`, `crm.pipeline`.

- **Regions** — *board header* (title crumb + filter bar + view-scope selector + optional presence avatars) · *column rail* (horizontal, scroll-x) · *column* = header (name + mono count pill + add affordance) over a *card stack* · *card* = title, one meta line (assignee / due / value, mono), left accent edge in the column's or domain color, drag handle.
- **Skin/tokens** — paper canvas; columns are `--color-card` with `--color-line-strong` borders; column headers ink text + mono count; card accent edge = domain accent (`--primary-500`); OFF/blocked cards at 45% opacity per Switchboard OFF-row rule.
- **States** — *board first-use*: first-use empty ("Create your first deal") centered over the rail. *Empty column*: quiet inline "Nothing in {stage}" + card-shaped add slot, never a full empty-state block. *Filtered-out*: board-level filtered-out state naming the active filter + Clear. *Error*: board-level error with Retry. *Loading*: column-and-card skeleton (3 columns × 3 card blocks, `animate-pulse`) — never a spinner.
- **Realtime default** — **Reverb broadcast** (collaborative board, sub-5s; add presence avatars). Card moves are optimistic per [[perceived-performance]] — card moves on drop, `MoveX` runs after, board re-renders from server on exception.
- **Interactions** — drag card between/within columns (reorder) · click card → view/edit (resource modal or view page) · add affordance per column. Keyboard: focus card + arrow-key move as accessible fallback. **Mobile**: column pager (one column at a time, swipe between), drag stays within the visible column.

---

## Calendar

Row #4. Package `saade/filament-fullcalendar`. Reference: `hr.leave`, `hr.shifts`, `events.events`.

- **Regions** — *toolbar* (prev/next/today + month/week/day view toggle + title) · *grid* (day cells or time-grid) · *event chip* (title + mono time, domain-accent fill or left border) · optional *legend* (event-type squares, 9–11px per design-system).
- **Skin/tokens** — paper grid, `--color-line` hairline cells, today cell tinted `--primary-50`; event chips use domain accent; mono for all times and the toolbar title.
- **States** — *first-use*: empty grid is normal — show a first-use hint chip ("No events this month — add one") rather than a blank month. *Filtered-out*: banner above grid naming the type/resource filter + Clear. *Error*: toolbar-level error strip + Retry (keep last-loaded grid at 60% opacity). *Loading*: grid skeleton (faint cell borders + 2–3 ghost chips), no spinner.
- **Realtime default** — **Polling 30s** (`wire:poll.30s` or FullCalendar refetch). Never Reverb — a shared calendar tolerates 30s staleness.
- **Interactions** — switch view (month/week/day) · click event → detail modal · click empty slot → create (where the module allows) · drag/resize event → reschedule (optimistic, only if the module owns writes). **Mobile**: default to day/agenda view; toolbar collapses to prev/next + view menu.

---

## Gantt / Timeline

Row #5. Custom page + Alpine/JS lib in the theme bundle. Reference: `projects.gantt`.

- **Regions** — *left task rail* (task/row labels, collapsible groups) · *time axis header* (mono dates, scale toggle: day/week/month) · *bar canvas* (one bar per row, dependency links, today marker) · *bar* = label + mono date range, domain-accent fill, drag/resize handles.
- **Skin/tokens** — paper canvas, mono axis labels, `--color-line` vertical week gridlines, today marker in `--color-accent`; bars in domain accent; dependency links thin ink lines.
- **States** — *first-use*: empty canvas + "Add the first task to see the timeline". *Filtered-out*: rail-level filtered-out note + Clear. *Error*: canvas error overlay + Retry. *Loading*: rail-and-bar skeleton (row labels + staggered ghost bars), no spinner.
- **Realtime default** — **Polling 60s** (schedules change slowly; longer interval than calendar).
- **Interactions** — drag bar (move dates) · drag handle (resize duration) · draw/remove dependency link · collapse/expand group · zoom scale toggle. Bar edits are optimistic. **Mobile**: read-only horizontal scroll; editing disabled below tablet width (state the fallback in `security.md`).

---

## Dashboard

Row #6. Filament Dashboard page + widgets. Package `leandrocfe/filament-apex-charts`. Reference: `analytics.dashboards`, finance dashboard.

- **Regions** — *header* (title + optional date-range / scope filter) · *stats row* (StatsOverview — 4 `blueprint-cell` tiles) · *chart row* (≥1 ChartWidget, 12-month, PHP date grouping per [[filament-resource-checklist]] #9) · optional *table widget* (one actionable queue). Every widget `canView()`-guarded.
- **Skin/tokens** — paper canvas; stat tiles are `blueprint-cell` (white cell, 14px accent corner tick, mono 44px number) per design-system; chart accent = domain `--primary-*`; mono axis + legend labels.
- **States** — *first-use*: seeded demo data means dashboards are never blank in dev; in a fresh tenant each widget shows its own first-use tile ("No revenue yet") — an empty dashboard reads as broken ([[filament-resource-checklist]] #10). *Filtered-out*: per-widget "no data for this range". *Error*: per-widget error card + Retry (one widget failing never blanks the page). *Loading*: lazy widgets (`$isLazy = true`) render skeleton tiles/chart placeholders via `placeholder()`, never the default spinner.
- **Realtime default** — **Widget polling 30–60s** (`$pollingInterval`). No Reverb.
- **Interactions** — change date range/scope → all widgets refetch · click a stat/segment → drill to the filtered resource list · table-widget row actions. **Mobile**: tiles and widgets stack single-column in source order.

---

## Wizard

Row #7. Custom page hosting a Filament Wizard form. Reference: `core.setup`.

- **Regions** — *step header* (numbered/labeled steps, current highlighted) · *step body* (that step's sectioned fields) · *footer nav* (Back / Next / Finish) · optional *summary/review* final step.
- **Skin/tokens** — paper canvas; step numbers mono, active step in panel color (`.fi-sc-wizard-header-*`); fields in Sections per [[filament-resource-checklist]] #2; step labels are topics ("Contract", "Compensation") not bare numbers, per [[ux-states]] §4.
- **States** — *per-step validation*: each step validates on Next (Filament native) — never collect all fields then dump errors at the end. *Error* (final submit fails): stay on the review step, human message + Retry, entered data preserved. *Loading* (async submit): footer button shows inline progress, not a full-page spinner; optimistic only for trivially reversible steps.
- **Realtime default** — **None** (single-user flow).
- **Interactions** — Next (validates) · Back (no re-validate) · jump to a completed step via header · Finish. **Mobile**: step header collapses to "Step 2 of 5 — Compensation" + progress bar; one field column.

---

## Inbox / Chat / Conversation

Row #8. Custom page + Livewire. Reference: `comms.shared-inbox`, `comms.whatsapp`.

- **Regions** — *conversation list* (left rail — sender, snippet, mono timestamp, unread dot, filter/assign controls) · *thread pane* (center — message bubbles, inbound left / outbound right, mono timestamps, day dividers) · *composer* (bottom — text, attach, send) · optional *context panel* (right — contact/ticket detail).
- **Skin/tokens** — paper canvas; list rows zebra + selected = 10% primary tint + 2px primary left edge ([[ux-states]] §3); outbound bubbles domain-accent tint, inbound `--color-card`; mono timestamps; unread dot in accent.
- **States** — *first-use*: empty list "No conversations yet — you'll see messages here as they arrive." *No thread selected*: thread pane shows a quiet "Select a conversation" placeholder (not an error). *Filtered-out*: list-level filtered-out naming the assignment/status filter + Clear. *Error*: thread-level error + Retry, keep composer usable. *Loading*: list skeleton (rows) + thread skeleton (alternating bubble blocks), no spinner.
- **Realtime default** — **Reverb broadcast** (sub-5s expectation; new messages append live, unread counts update). Outbound send is optimistic — bubble appears immediately, reconciles on delivery; the send action names a `panel-action` rate limiter (comms category, [[../security]]).
- **Interactions** — select conversation → load thread · send message (optimistic append) · assign/resolve/snooze · attach file. **Mobile**: single-column master→detail (list, tap into thread, back button returns to list); context panel becomes a sheet.

---

## Report Builder / Query UI

Row #9. Custom page + form components. Reference: `analytics.reports`.

- **Regions** — *builder rail* (source/entity picker + dimension & measure selectors + filter rows + date range) · *run/preview bar* (Run, Save, Export) · *result pane* (table and/or chart of the built query) · optional *saved-reports list*.
- **Skin/tokens** — paper canvas; builder fields in Sections; mono column headers and numeric cells in the result table; chart accent = domain `--primary-*`.
- **States** — *initial* (nothing built yet): instructional empty pane "Pick a source and measures, then Run" — a teaching first-use state, not "No records". *Empty result*: filtered-out state naming the query's filters + a "widen filters" hint. *Error* (query failed): human message + Retry, keep the builder populated so the user can adjust. *Loading* (run in progress): result-pane skeleton (table rows or chart placeholder), progress bar that starts fast per [[perceived-performance]].
- **Realtime default** — **None** (user runs queries on demand).
- **Interactions** — add/remove dimension·measure·filter · Run → render result · Save report · Export (Export action names its rate limiter, [[../security]]). **Mobile**: builder collapses into an accordion above a horizontally scrollable result table.

---

## Org Chart / Tree

Row #11. Custom page + `codewithdennis/filament-select-tree` or a JS tree lib. Reference: `hr.org`.

- **Regions** — *canvas/toolbar* (zoom, expand-all/collapse-all, search-to-focus) · *node* (avatar/initial + name + mono role/title, domain-accent edge) · *connectors* (manager→report lines) · optional *side detail* panel on node select.
- **Skin/tokens** — paper canvas, `--color-card` nodes with `--color-line-strong` borders, mono role labels, connector lines in `--color-line-strong`; selected node = 2px primary edge.
- **States** — *first-use*: "No structure yet — set a manager on employees to build the chart." *Filtered-out* (search focus with no match): "No one named '…'." + Clear. *Error*: canvas error overlay + Retry. *Loading*: skeleton of 1 root + 3 child node blocks, no spinner.
- **Realtime default** — **None** (org structure changes rarely).
- **Interactions** — expand/collapse a branch · pan/zoom canvas · click node → detail (or navigate to the employee resource) · search-to-focus. Reparenting via drag is optional and, if present, optimistic + permission-gated. **Mobile**: vertical indented-list fallback instead of a 2-D canvas (state it explicitly).

---

## Gallery / Directory Grid

Row #17. Custom page + Blade grid + Livewire filters. Reference: `lms.mentoring`.

- **Regions** — *filter/search bar* (top — search + facet filters + sort) · *card grid* (responsive columns) · *card* = avatar/thumbnail + name/title + short meta line + one primary action · *pagination* (30px squares per skin).
- **Skin/tokens** — paper canvas; cards `--color-card` with `--color-line-strong` border and card shadow; hover = light 5% primary wash (preview), selected = 10% tint per [[ux-states]] §3; mono meta line.
- **States** — *first-use*: "No mentors listed yet" + primary add/invite action. *Filtered-out*: filtered-out state naming the facet/search + Clear — never a bare "No results". *Error*: grid-level error + Retry. *Loading*: card-grid skeleton (6–8 card placeholders with avatar circle + text bars), no spinner.
- **Realtime default** — **None**.
- **Interactions** — search / facet filter / sort → grid re-filters (optimistic where cheap) · card primary action (e.g. Request mentor) — action rate-limited if it sends comms · click card → detail. **Mobile**: grid collapses to single/two-column; filter bar becomes a filter sheet.

---

## Heat-map / Matrix

Row #18. Custom page + Blade/CSS grid (+ apexcharts heatmap if charted). Reference: `lms.skills-matrix`.

- **Regions** — *axis headers* (row labels left, column labels top — both mono) · *cell grid* (color-coded cells) · *legend/scale* (value→color ramp) · optional *cell tooltip/detail* on hover/click.
- **Skin/tokens** — paper canvas; mono axis labels; cell color ramp = sequential tint of the domain accent (light→saturated) — follow [[../../frontend/design-system|design-system]] domain color, never an arbitrary rainbow; `--color-line` cell hairlines; hover cell = subtle lift/outline.
- **States** — *first-use*: "No data to compare yet — add skills and ratings to build the matrix." *Filtered-out*: axis-level filtered-out note + Clear. *Error*: grid error overlay + Retry. *Loading*: grid skeleton (uniform faint cells), no spinner.
- **Realtime default** — **None**.
- **Interactions** — hover cell → value tooltip (hover is a preview per [[ux-states]] §3) · click cell → detail/edit (where writable) · filter axes · toggle scale. **Mobile**: horizontal scroll with a sticky left axis column; pinch-zoom optional.

---

## Spatial / Floor Map

Row #19. Custom page + Alpine — absolute-positioned hotspots over a floor image. Reference: `workplace.desk-booking`.

- **Regions** — *map canvas* (floor image + positioned hotspot divs) · *hotspot* (desk/room marker — state color: free / booked / mine) · *toolbar* (floor/zone selector, date/time picker, zoom) · optional *side detail* panel on hotspot select.
- **Skin/tokens** — floor image on paper canvas; hotspot states use semantic tints (free = domain accent soft, booked = ink-faint, mine = domain accent solid); mono labels in the toolbar and detail panel; selected hotspot = 2px primary ring.
- **States** — *first-use / unconfigured*: "No floor plan uploaded — add one to enable desk booking." *Filtered-out* (no availability for the chosen slot): "Nothing free at this time" + change-time hint. *Error*: canvas error overlay + Retry. *Loading*: image placeholder skeleton + ghost hotspot dots, no spinner.
- **Realtime default** — **Polling 30s** (live occupancy — desks book/free while viewing).
- **Interactions** — pick date/time → hotspot states refresh · click free hotspot → book (optimistic, names a `panel-action` limiter; reconciles against capacity via pessimistic decrement server-side per [[../../decisions/decision-2026-07-02-optimistic-locking-standard|concurrency]]) · click own booking → release · pan/zoom. **Mobile**: pinch-zoom + tap; toolbar collapses to date/time + zone menu.

---

## Notification Bell (render hook, not a page)

Row #10. Not a custom page — a Livewire component mounted via the panel `GLOBAL_SEARCH_BEFORE`/topbar render hook on **every** panel (see [[filament-panel-chrome]]).

- **Composition** — bell icon + ping dot (unread) → dropdown/slide-over list of notification rows (icon + text + mono relative time + read/unread state) + "mark all read" + link to full notifications page.
- **Skin/tokens** — 30px ringed control in the warm topbar; ping dot in accent; mono timestamps; unread row = 10% primary tint.
- **States** — *empty*: "You're all caught up." (emptied tone, not an error). *Error*: quiet "Couldn't load notifications" inline + Retry. *Loading*: 3-row skeleton in the dropdown.
- **Realtime default** — **Reverb broadcast** on `company.{id}.notifications` (see [[../websockets]]) — the one always-on realtime surface.
- **Interactions** — click bell → open list · click row → navigate to source + mark read (optimistic) · mark all read (optimistic). Because it's a shared render hook, its `canAccess` equivalent is the panel gate itself; the component still scopes queries to the tenant.

---

## Related

- [[ui-strategy]] — the row each blueprint maps to; Resource Tweak Taxonomy for board/timeline sub-views inside resources
- [[custom-pages]] — PHP/Blade structure and pitfalls (this file is the composition layer on top)
- [[custom-page-checklist]] — the DoD gate that verifies blueprint conformance
- [[ux-states]] · [[perceived-performance]] · [[../../frontend/design-system|Switchboard+]]
- [[../../decisions/decision-2026-07-02-browser-test-convention]] — per-kind Playwright interaction assertions come from these blueprints
- [[../../decisions/decision-2026-07-02-spec-template-v3-exploded-format]] — mandates the blueprint citation in `## Filament Artifacts`
