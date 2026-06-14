# /flowflex:verify

**Live HTTP smoke check against the docker stack. Run after every session that touched panels, auth, middleware, or migrations.**

The Pest suite has stayed green through multiple browser-breaking bugs (empty permission caches, non-persistent Livewire middleware, pgsql-only crashes). This command verifies what the suite cannot: a real authenticated browser-equivalent session.

## Usage

```
/flowflex:verify
/flowflex:verify panels=hr,finance
```

## Steps

1. **Stack up** — `docker compose ps`; start anything down; if migrations changed this session: `docker compose exec -T app php artisan migrate --force` (or the full pgsql gate `migrate:fresh --seed --force` per way-of-working).
2. **Login session** (curl, cookie jar): GET `/login` for the XSRF cookie, POST `/login` JSON `{email: test@test.nl, password: test1234}` with `X-XSRF-TOKEN` + `X-Inertia: true` headers → expect **409** (Inertia::location into the panel — that IS success).
3. **Page sweep** — with the session cookies, GET and expect 200:
   - `/app`, `/app/data-imports`, `/app/api-clients`, `/app/module-marketplace-page`
   - `/hr`, `/hr/employees`, `/hr/org-chart-page`
   - `/finance`, `/crm`, `/crm/pipeline-board-page`, `/crm/accounts`, `/crm/pipelines`
   - (restrict to `panels=` arg when given; extend the list as new pages ship)
4. **Livewire POST probe** (the check that catches the null-team 403 family — see `vault/architecture/patterns/tenant-context-pitfalls.md`): GET a resource list page, extract from the HTML: the `/livewire-{hash}/update` URL, `data-csrf`, and a component `wire:snapshot` (html-unescape it). POST to the update URL: JSON `{_token: csrf, components: [{snapshot, updates: {}, calls: [{path: '', method: '$refresh', params: []}]}]}` with headers `X-Livewire: 1`, `Referer: <page url>`, session cookies → expect **200**. A 403 here = tenant context missing on Livewire requests; 419 = wrong csrf source (use the page's `data-csrf`, not the cookie).
5. **Admin sweep** — Filament logins CAN be curl-posted (proven 2026-06-12): GET `/admin/login`, extract `wire:snapshot` (html-unescape) + the `csrf-token` meta, POST to `/livewire-{hash}/update` with JSON `{_token, components: [{snapshot, updates: {"data.email": "admin@flowflex.nl", "data.password": "password", "data.remember": false}, calls: [{path: '', method: 'authenticate', params: []}]}]}` + `X-Livewire: 1` → response contains `"redirect": ".../admin"`. Then GET `/admin` → 200. Same technique works for any panel login and for fetching authed panel HTML.
6. **Design/skin probe** (after any theme/skin change): on an authed panel page, confirm the rendered HTML contains `ff-spotlight-overlay` (Spotlight injected) and the served `theme-*.css` is the freshly built hash (`ls public/build/assets | grep theme` vs the page's link tag). Stale = `docker compose exec app php artisan optimize:clear` + hard refresh. CSS selectors that match nothing fail silently — when a skin rule "doesn't work", dump the page HTML and grep the actual `fi-*` class names (filament-patterns item 16).
7. **Public sweep** — GET 200: `/`, `/pricing`, `/modules`, `/switch-over`, `/trust`, `/changelog`, `/patchwork`, `/customers/veldkamp`, `/status`, `/help`; GET `/definitely-not-a-page` → 404 with the branded page.
8. **Report** — table of URL → status; any non-expected status is a finding: file `/flowflex:bug` and do NOT mark the session's module done.

## Why each probe exists

| Probe | Bug class it catches |
|---|---|
| POST /login → 409 | Inertia redirect-to-panel regressions |
| Page GETs 200 | route/canAccess/middleware/pgsql-only crashes |
| Livewire $refresh 200 | null-team family: non-persistent middleware, eager share() permission reads |
| Livewire login POST → redirect | panel auth/guard regressions (admin + customer, cross-guard) |
| ff-spotlight in authed HTML | render-hook regressions, Spotlight missing |
| theme hash fresh | stale build/caches masquerading as "design not applied" |
| Public sweep + 404 | marketing route/controller regressions, branded 404 wiring |
| migrate on pgsql | sqlite-tolerated DDL (self-FKs, text-vs-jsonb) |
