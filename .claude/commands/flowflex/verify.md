# /flowflex:verify

**Live HTTP smoke check against the docker stack. Run after every session that touched panels, auth, middleware, or migrations.**

The Pest suite has stayed green through multiple browser-breaking bugs (empty permission caches, non-persistent Livewire middleware, pgsql-only crashes). This command verifies what the suite cannot: a real authenticated browser-equivalent session.

**Hard lesson (2026-06-14): GET-200 + green Pest ≠ correct UI.** A whole run of founder-reported bugs — empty marketplace, dead-looking drag-drop, doubled logo, blank dashboard, sidebar that didn't fit, profile that silently didn't save — all returned 200 and passed every test. The ONLY reliable catch was a rendered screenshot. **After any panel/skin/layout/widget change, take a Playwright screenshot and actually look at it** (step 7 below). Tests prove it loads; screenshots prove it's right.

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
7b. **Visual screenshot (panel/skin/layout/widget changes — do not skip).** `npx playwright install chromium` once, then a Node script (run from `app/`): login via the public `/login` form (`#email`/`#password`, submit, `waitForURL(/\/app\/?$/)`), `goto` each changed page, `waitForTimeout(2500)` so Livewire widgets paint, `screenshot()`. **Read the PNG** — check the actual complaint, not just that the page rendered. Login script gotcha: `waitForURL` with a loose `**/app**` pattern matches `/app/login` too; anchor it (`/\/app\/?$/`). Screenshots also surface duplicate chrome, stale CSS (rebuild + `optimize:clear` if the theme hash is old), and broken grids that no assertion covers.

7c. **Resource create-audit (any session touching panel resources).** Run a tinker script over every panel resource and assert each has a sane create posture — this caught 12 resources shipped with empty forms / no create button:
   ```php
   foreach (Filament::getPanels() as $panel) {
       Filament::setCurrentPanel($panel);
       foreach ($panel->getResources() as $r) {
           // flag: canCreate() true BUT form has 0 components (broken create),
           // or a List page with a create page but no CreateAction header.
           $fields = count($r::form(\Filament\Schemas\Schema::make())->getComponents(withHidden: true));
           echo "{$r}|create:".($r::canCreate()?'Y':'N')."|fields:{$fields}".PHP_EOL;
       }
   }
   ```
   `canCreate:Y` + `fields:0` = a create button that opens an empty modal. Either give it a real form or `canCreate(false)` + an explanatory empty state (ux-states.md). Auth via a logged-in owner with company context set.

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
| **Playwright screenshot** | **everything GET-200 + Pest miss: empty/blank UI, doubled chrome, broken layout, stale CSS, dead-looking interactions** |
| Resource create-audit | empty create modals, missing create buttons (ux-states) |
| migrate on pgsql | sqlite-tolerated DDL (self-FKs, text-vs-jsonb) |
