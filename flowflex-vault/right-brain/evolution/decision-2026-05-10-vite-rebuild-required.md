---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Filament theme CSS requires `npm run build` after adding new Tailwind classes

## Context

After redesigning `setup-wizard.blade.php` with new Tailwind utility classes (`ring-2`, `ring-offset-2`, `bg-gradient-to-br`, `from-primary-50`, `shadow-primary-200`, etc.), the page rendered completely unstyled in the browser despite the `@source` glob in `theme.css` covering the view path.

## Why This Happens

`resources/css/filament/app/theme.css` uses `@import 'tailwindcss' source(none)` — this disables Tailwind's automatic file scanning. Classes are only included if found in the explicit `@source` globs at **Vite build time**. If you add new classes to a view file and the Vite build hasn't run since, those classes are absent from the compiled CSS bundle.

## Options Considered

1. **Rely on Vite HMR (dev mode)** — HMR would catch file changes and recompile. But Docker setup runs `npm run build` once on startup; no persistent Vite dev server.
2. **Run `npm run build` after editing views** — Simple, reliable, takes ~3 seconds.
3. **Add `source(./resources/views/**/*.blade.php)` to theme.css** — Would re-enable scanning but conflicts with Filament's intentional `source(none)` approach.
4. **Use only inline styles / style attributes** — Avoids JIT but defeats Tailwind entirely.

## Decision

Run `npm run build` inside Docker after any edit that introduces new Tailwind classes to a blade view:

```bash
docker exec flowflex_app bash -c "npm run build"
```

Then hard-refresh the browser (Cmd+Shift+R / Ctrl+Shift+R).

In development, consider running `npm run dev` in a persistent terminal inside Docker to get HMR.

## Consequences

- All new Tailwind classes in views require a rebuild before they appear
- Forgetting to rebuild produces pages that look completely unstyled (not partially — Filament's base CSS is separate from the compiled theme)
- This is expected behaviour for Filament 5 with Tailwind v4 `source(none)` configuration

## Related Left Brain

N/A — this is a developer workflow finding.
