---
tags: [flowflex, design, motion, animation]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Motion & Animation

Interactions should feel smooth, not sluggish. Motion guides attention — it does not entertain.

## Duration Scale

| Token | Duration | Usage |
|---|---|---|
| `duration-fast` | 100ms | Micro-interactions (hover colour change, checkbox tick) |
| `duration-base` | 150ms | Default transitions (button hover, input focus) |
| `duration-slow` | 200ms | Panel expand/collapse, dropdown open |
| `duration-slower` | 300ms | Modal enter/exit, slide-over |
| `duration-page` | 400ms | Page transitions |

## Easing Functions

| Token | Value | Usage |
|---|---|---|
| `ease-standard` | `cubic-bezier(0.4, 0, 0.2, 1)` | Most transitions |
| `ease-decelerate` | `cubic-bezier(0, 0, 0.2, 1)` | Elements entering screen |
| `ease-accelerate` | `cubic-bezier(0.4, 0, 1, 1)` | Elements leaving screen |
| `ease-spring` | `cubic-bezier(0.34, 1.56, 0.64, 1)` | Playful moments (success animations) |

## Motion Rules

- All hover state colour changes: `duration-base` `ease-standard`
- All focus rings: `duration-fast` `ease-standard`
- Modals enter: `duration-slower` `ease-decelerate` (scale from 95% + fade in)
- Modals exit: `duration-slow` `ease-accelerate` (scale to 95% + fade out)
- Slide-overs: `duration-slower` `ease-decelerate` (slide from right)
- Toast notifications: `duration-slow` `ease-decelerate` (slide down from top)
- Page transitions: `duration-page` (fade only, no slide)
- **Always respect `prefers-reduced-motion`** — disable all animation for users who request it

## Related

- [[Component Library]]
- [[Dark Mode]]
