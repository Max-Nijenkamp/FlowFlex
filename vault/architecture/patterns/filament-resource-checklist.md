---
type: architecture
category: pattern
color: "#A78BFA"
---

# Filament Resource Checklist

27 resources shipped with **empty form schemas and no create path** before the 2026-06-12 sweep — the suite stayed green because nothing tests "a human can actually use this screen". This checklist is the missing gate. Every resource must pass it before its module is `complete`.

---

## Per-resource checklist

1. **Form is real** — `form()` schema covers the model's user-editable `$fillable`. If the resource is intentionally read-only or flow-owned, say so in code: `canCreate(): false` + a comment naming the owning service/flow. An empty `->components([])` with create enabled is always a bug.
2. **Sections** — every form wrapped in `Filament\Schemas\Components\Section` blocks (founder rule; bare fields float pale on the canvas — the theme CSS fallback cards them, but Sections are the standard).
3. **Create path exists** — List-only resources get `CreateAction::make()` in `getHeaderActions()` (modal uses the resource form); full resources get a Create page. EditAction in `recordActions`.
4. **State fields**: spatie model-states columns are NOT raw form fields — creation relies on the default state; transitions go through explicit actions calling the service.
5. **Money**: cents integers. Either label `(cents)` or convert at the form boundary (`formatStateUsing` /100 + `dehydrateStateUsing` *100) — never floats in storage.
6. **canAccess()** — permission + `hasModule()`. Never removed, never weakened (ADR 2026-06-11).
7. **Table quality** — status columns as badges with colors, money via `->money('EUR', divideBy: 100)`, relevant filters, `deferLoading()`, searchable key columns.
8. **Global search** — customer-facing entities (employees, contacts, deals, invoices, organisations…) define `getGloballySearchableAttributes()` so ⌘K finds them.
9. **Stale-record guard** — edit forms carry the optimistic-lock guard (hidden `_loaded_at` + `ChecksStaleRecords`) per [[architecture/patterns/optimistic-locking]]. Skip for read-only / `canCreate(): false` flow-owned resources.

## Per-panel checklist (first module that opens a panel)

10. **Dashboard widgets** — minimum trio: StatsOverview (4 stats), one ChartWidget (12-month, **PHP date grouping — never SQL date functions**, sqlite + pgsql must both run it), optionally one TableWidget (actionable queue). All with `canView()` permission guards.
11. **Demo seeder section** — LocalDevSeeder gets a realistic block for the FlowFlex Demo company: enough rows that every widget shows a curve and every table has content. Empty dashboards read as broken.

## Verification

`/flowflex:verify` (live smoke) after every panel-touching session: login as test@test.nl, GET the panel pages, fire one scripted Livewire POST (see [[architecture/patterns/tenant-context-pitfalls]]).

## Related

- [[architecture/filament-patterns]] — base Filament conventions
- [[architecture/way-of-working]] — DoD items 11–13 reference this file
