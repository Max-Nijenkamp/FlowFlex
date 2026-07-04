# /flowflex:screenshot

**Playwright login + screenshot/measure helper for the docker stack. The canonical way to visually verify panel/skin work — GET-200 + green Pest ≠ correct UI.**

## Usage

```
/flowflex:screenshot                          # /app dashboard, light, 1440×960
/flowflex:screenshot url=/app/profile dark mobile
/flowflex:screenshot measure=.ff-user-menu-panel
```

## Known-good recipe (verified 2026-07-04)

- **Stack**: nginx on **http://localhost:8080** (not :80). Public `/login` is 404 until the Vue site ships — use the panel logins.
- **Logins**: `/app/login` → test@test.nl / test1234 (guard `web`) · `/admin/login` → admin@flowflex.nl / password (guard `admin`).
- **Playwright**: installed in `app/node_modules` — from a scratch script `require('C:/Users/maxni/Documents/projects/FlowFlex/app/node_modules/playwright')` (bare `require('playwright')` fails outside `app/`).
- **Login selectors**: `input[type="email"]`, `input[type="password"]`, `button[type="submit"]` (Filament login page — there is no `#email`).
- **Wait**: `waitForURL(/\/app\/?$/)` (anchored — a loose `**/app**` matches `/app/login`); redirect can race, so prefer `waitForTimeout(3000)` + assert on page content.

## Template

```js
const { chromium } = require('C:/Users/maxni/Documents/projects/FlowFlex/app/node_modules/playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1440, height: 960 } }); // mobile: 390×844; dark: colorScheme: 'dark'
  await page.goto('http://localhost:8080/app/login', { waitUntil: 'networkidle' });
  await page.fill('input[type="email"]', 'test@test.nl');
  await page.fill('input[type="password"]', 'test1234');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(3000);
  await page.goto('http://localhost:8080' + TARGET, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000); // let Livewire widgets paint
  await page.screenshot({ path: OUT, fullPage: true }); // clip: {x,y,width,height} for detail shots
  await browser.close();
})();
```

## Measure mode — debug CSS by numbers, not eyeballs

When a skin rule "looks off", dump geometry + computed styles up the ancestor chain instead of iterating blind:

```js
const info = await page.evaluate(() => {
  const el = document.querySelector(SELECTOR);
  const chain = [];
  let n = el;
  for (let i = 0; i < 5 && n; i++) {
    const cs = getComputedStyle(n);
    const b = n.getBoundingClientRect();
    chain.push({ cls: String(n.className).slice(0, 60), x: b.x, w: b.width, h: b.height,
      mt: cs.marginTop, pl: cs.paddingLeft, pr: cs.paddingRight, display: cs.display,
      gap: cs.rowGap, sg: cs.scrollbarGutter });
    n = n.parentElement;
  }
  return chain;
});
```

This is how the collapsed-rail off-center bug (vendor `scrollbar-gutter: stable`) and the checklist row-gap bug were found — see `architecture/patterns/filament-panel-chrome.md` §6 for the catalogue of gotchas.

## After any theme/skin change

1. `cd app && npm run build` (run from `app/` — the script errors from repo root)
2. `docker compose exec -T app php artisan view:clear` when blade chrome changed
3. Screenshot and **actually look at the PNG** against the complaint
4. States that regress silently: collapsed icon rail, dark mode, mobile 390px, user-card popover open

## Gotchas

- **Root-owned compiled views 500 the panels** (`touch(): Utime failed`): every root-run `docker compose exec app php artisan ...` or test run compiles Blade views owned by root that php-fpm (www-data) cannot overwrite. After ANY container artisan/test session, finish with:
  `docker compose exec -T app sh -c 'rm -f storage/framework/views/*.php; chmod -R 777 storage/framework'`

- Success-notification carryover: `body.innerText.includes('Saved')` right after an action can match the PREVIOUS action's toast. Assert on unique text or fresh navigation.
- Interacting across `browser.newContext()` = separate sessions — log in per context.
- Sidebar collapse state persists in localStorage per context — reset by clicking the toggle before screenshotting the expanded state.
