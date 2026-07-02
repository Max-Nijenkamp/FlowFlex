---
type: architecture
category: pattern
pattern-key: error-pages
status: stable
last-reviewed: 2026-07-02
color: "#A78BFA"
---

# Error Pages — Full-Page + Livewire Crash UX

Designed error surfaces for HTTP failures and in-panel component crashes. Blade files are named in [[architecture/error-handling]]; this doc specifies their layout, copy and actions. Every error surface follows the human-voice rule from [[architecture/patterns/ux-states]] §2 — *what happened + what it means + what to do*, never exception text or a stack trace.

---

## Scope

1. **Full-page HTTP errors** — `403`, `404`, `419`, `429`, `500`, `503`, rendered by `resources/views/errors/*.blade.php`. Shown when the request never reaches a panel (unauthenticated, throttled, server down).
2. **Livewire component crash recovery** — a widget or custom-page region throws *inside* an already-rendered panel. The panel chrome survives; only the failed region is replaced by an inline error card with a **Retry**.

---

## Shared Layout Spec

One Blade layout component, `errors::layout`, styled in Switchboard+ ([[../../frontend/design-system]] tokens):

- **Paper canvas** background (`--color-paper`, warm, never pure white), **ink** text (`--color-ink`).
- **Mono kicker line** carrying the status code — `JetBrains Mono`, 11–12px, uppercase, ink-faint (e.g. `ERROR / 404`). This is the only place the raw code appears.
- **Domain-neutral accent** — the platform indigo (`--color-accent`), never a domain color; the user may not be inside any panel.
- **Centered card**, max-width ~28rem, white (`--color-card`), 14–16px radius, elevated shadow, with a **blueprint corner tick** (14px accent) echoing `BlueprintCell`.
- **No panel chrome** — no sidebar, no topbar. The user may be unauthenticated, so nothing that assumes a session or a tenant renders.
- Heading in `--font-display`; body in `--font-sans`, ink-soft; actions are `BaseButton` equivalents (primary indigo, secondary outline).
- All decorative motion gated behind `prefers-reduced-motion`.

Copy is sentence case, no exclamation marks, "you/your", active voice — the design-system copy rules. Never surface the exception message, class name or trace.

---

## Per-Page Copy + Actions

| Code | Heading | Body | Primary action | Secondary action |
|---|---|---|---|---|
| **403** | You don't have access to this | Your account doesn't have permission for this area, or the module isn't switched on for your company. | Back to dashboard | **Request access** — notifies the company owner *(assumed: `mailto:` / in-app notification, no module name leaked)* |
| **404** | This page doesn't exist | The link may be broken, or the page may have moved. | Back | Open search (⌘K Spotlight) — only if authed |
| **419** | Your session expired | You were away long enough that we signed you out to keep your account safe. | Log in again | — |
| **429** | Slow down a moment | You've made a lot of requests in a short time. Give it a few seconds and try again. | Retry — disabled with a countdown from `Retry-After` | — |
| **500** | Something went wrong | It's us, not you. The error has been reported and we're looking into it. | **Reload page** | Back to dashboard |
| **503** | We're doing a quick tune-up | FlowFlex is briefly down for maintenance. This page refreshes itself. | — (auto) | — |

Details:
- **403** never names the missing permission or module — that leaks the tenant's configuration. The "Request access" route is *(assumed)* and should be confirmed against the notifications module when built.
- **404** secondary opens Spotlight via the `ff-spotlight-open` window event ([[../../frontend/design-system]]) — only rendered when a session exists.
- **429** surfaces `Retry-After` as a friendly wait ("try again in 12s"), and the Retry button stays disabled on a live countdown rather than letting the user hammer a throttled endpoint ([[architecture/security]] rate limits).
- **500** shows the **Sentry event id** as a small mono reference code (`ref: a1b2c3`) so support can correlate — this is a reference, not the exception text.
- **503** adds `<meta http-equiv="refresh" content="30">` so the page reloads every 30s and returns automatically when maintenance ends.

---

## Livewire Crash Recovery

When a Livewire component throws inside a live panel, do not let it blank the page. The failed region is swapped for a bordered inline error card in the same voice:

- Bordered card (`--color-line-strong`), ink-soft copy: heading "This didn't load", body "Something went wrong showing this. Your other data is fine."
- **Retry button** re-triggering `$refresh` on the component — recovers without a full page reload.
- **Wrap fragile regions.** On custom pages that host several widgets, wrap each fragile widget region so one widget's crash replaces only that card, not the whole page. A dashboard with one broken chart still shows its other stats.
- This is the *crashed* state. The *slow* state is different: the stale-content-at-60%-opacity timeout rule from [[architecture/patterns/ux-states]] §2 still governs pending (not failed) loads — no spinner walls.

```blade
{{-- Wrap a fragile custom-page region --}}
<div>
    @try
        <livewire:crm.pipeline-velocity-widget />
    @catch (\Throwable $e)
        <x-errors::inline-crash :component="'crm.pipeline-velocity-widget'" />
    @endtry
</div>
```

*(The `@try/@catch` above is illustrative; in practice a small wrapper component or a Livewire error boundary renders `x-errors::inline-crash`, whose Retry action calls `$refresh`.)*

---

## Blade Contract

One shared layout + per-code views. Foundation-owned *(assumed: `foundation.panels` / core module)*:

```
resources/views/errors/
├── layout.blade.php          ← errors::layout — shared Switchboard+ shell (paper, mono kicker, corner-tick card)
├── 403.blade.php
├── 404.blade.php
├── 419.blade.php
├── 429.blade.php             ← reads Retry-After, renders countdown
├── 500.blade.php             ← shows Sentry event id ref
└── 503.blade.php             ← 30s auto-refresh meta

resources/views/components/errors/
└── inline-crash.blade.php    ← x-errors::inline-crash — Livewire crash card + Retry ($refresh)
```

These files belong in the Build Manifest of the foundation panels / core module *(assumed — confirm the owning module when its spec is written)*.

---

## Related

- [[architecture/error-handling]] — exception classes, global handler, HTTP status mapping
- [[architecture/patterns/ux-states]] — §2 error-copy voice, 60%-opacity slow-state rule
- [[../../frontend/design-system]] — Switchboard+ tokens, blueprint corner tick, Spotlight event
- [[architecture/security]] — rate limiting behind the 429 page
