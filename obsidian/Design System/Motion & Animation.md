---
tags: [flowflex, design, motion, animation]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Motion & Animation

Interactions should feel smooth, not sluggish. Motion guides attention — it does not entertain.

Updated for 2026: Spring physics specification, reduced motion standards (WCAG 2.2 SC 2.3.3), micro-interactions catalogue, AI-specific motion patterns, and View Transitions API guidance.

## Core Principle

Every animation must answer "yes" to both:
1. Does this help the user understand what just happened?
2. Would removing it make the interface feel broken or confusing?

If the answer to either is "no", the animation does not belong.

## Duration Scale

| Token | CSS Custom Property | Duration | Usage |
|---|---|---|---|
| `duration-instant` | `--duration-instant` | 0ms | State changes that should feel immediate (toggle on/off) |
| `duration-fast` | `--duration-fast` | 100ms | Micro-interactions (hover colour change, checkbox tick) |
| `duration-base` | `--duration-base` | 150ms | Default transitions (button hover, input focus ring) |
| `duration-slow` | `--duration-slow` | 200ms | Panel expand/collapse, dropdown open |
| `duration-slower` | `--duration-slower` | 300ms | Modal enter/exit, slide-over |
| `duration-page` | `--duration-page` | 350ms | Page transitions |
| `duration-emphasis` | `--duration-emphasis` | 500ms | Success animations, onboarding reveals |

## Easing Functions

### CSS Cubic Bezier (for opacity, colour, size changes)

| Token | CSS Custom Property | Value | Usage |
|---|---|---|---|
| `ease-standard` | `--ease-standard` | `cubic-bezier(0.4, 0, 0.2, 1)` | Most transitions — symmetric, neutral |
| `ease-decelerate` | `--ease-decelerate` | `cubic-bezier(0, 0, 0.2, 1)` | Elements entering screen — starts fast, slows to rest |
| `ease-accelerate` | `--ease-accelerate` | `cubic-bezier(0.4, 0, 1, 1)` | Elements leaving screen — starts slow, exits fast |
| `ease-sharp` | `--ease-sharp` | `cubic-bezier(0.4, 0, 0.6, 1)` | Feedback animations — snappy, precise |

### Spring Physics (for spatial/transform animations)

Spring physics produces more natural-feeling motion than cubic bezier for elements that move through space (modals, drawers, drag-and-drop). Spring is defined by stiffness and damping, not duration.

**Standard spring presets:**

| Token | Stiffness | Damping | Use case |
|---|---|---|---|
| `spring-gentle` | 120 | 14 | Large elements — modals, full panels |
| `spring-default` | 180 | 12 | Standard spatial animations — dropdowns, cards |
| `spring-bouncy` | 300 | 10 | Playful moments — success states, brand animations |
| `spring-stiff` | 400 | 20 | Precise, fast feedback — button press, toggle |

**CSS `linear()` function for spring approximation** (supported in all modern browsers, 2024+):

```css
/* spring-default approximation */
--spring-default: linear(
  0, 0.006, 0.025 2.8%, 0.101 6.1%, 0.539 17.9%, 0.721 24.3%,
  0.849 31%, 0.937 39.3%, 0.968, 1.002 55.1%, 1.018, 1.021 65.1%,
  1.017, 1.008 76.4%, 1.003 86.4%, 1.001 92.4%, 1
);

/* Usage */
.modal { transition: transform 500ms var(--spring-default); }
```

**Using Framer Motion / Motion One** (for complex sequences):

```js
// Spring animation on modal entry
animate(modal, { scale: [0.95, 1], opacity: [0, 1] }, {
  type: 'spring',
  stiffness: 180,
  damping: 12,
  duration: 0.4
});
```

## Motion Rules Catalogue

### Navigation & Page

| Action | Properties | Duration | Easing |
|---|---|---|---|
| Page transition | opacity 0→1, translateX 8px→0 | 350ms | `ease-decelerate` |
| Sidebar expand | width 64px→256px | 200ms | `ease-standard` |
| Sidebar collapse | width 256px→64px | 200ms | `ease-accelerate` |
| Active nav item | background, border-left | 150ms | `ease-standard` |

### Overlays

| Action | Properties | Duration | Easing |
|---|---|---|---|
| Modal enter | scale 0.95→1.0, opacity 0→1 | 300ms | `spring-gentle` |
| Modal exit | scale 1.0→0.95, opacity 1→0 | 200ms | `ease-accelerate` |
| Slide-over enter | translateX 100%→0 | 300ms | `spring-gentle` |
| Slide-over exit | translateX 0→100% | 250ms | `ease-accelerate` |
| Backdrop enter | opacity 0→0.5 | 200ms | `ease-standard` |
| Backdrop exit | opacity 0.5→0 | 200ms | `ease-accelerate` |
| Dropdown open | scaleY 0.95→1.0, opacity 0→1 | 150ms | `ease-decelerate` |
| Dropdown close | opacity 1→0 | 100ms | `ease-accelerate` |

### Feedback

| Action | Properties | Duration | Easing |
|---|---|---|---|
| Button hover | background colour | 100ms | `ease-standard` |
| Button press | scale 1.0→0.97 | 80ms | `ease-sharp` |
| Input focus | box-shadow expand | 150ms | `ease-decelerate` |
| Success pulse | scale 1.0→1.03→1.0 | 200ms | `spring-bouncy` |
| Error shake | translateX ±4px, 2 oscillations | 300ms | `ease-sharp` |
| Checkbox tick | scale 0→1 + path draw | 150ms | `spring-stiff` |
| Toggle switch | translateX, background | 200ms | `spring-default` |

### Content Loading

| Action | Properties | Duration | Easing |
|---|---|---|---|
| Skeleton shimmer | backgroundPosition | 1500ms | `linear` (infinite) |
| Content reveal (after load) | opacity 0→1 | 200ms | `ease-decelerate` |
| Row insert (after create) | height 0→auto, opacity 0→1 | 250ms | `spring-default` |
| Row remove (after delete) | height auto→0, opacity 1→0 | 200ms | `ease-accelerate` |

### AI-Specific Motion

| Action | Properties | Duration | Easing |
|---|---|---|---|
| AI thinking dots | wave translateY -4px→0 | 600ms each | `ease-standard` staggered 150ms |
| Streaming text reveal | per-character or per-word fade | 30ms/word | `ease-decelerate` |
| AI suggestion appear | opacity 0→1, translateY 4px→0 | 200ms | `ease-decelerate` |
| AI suggestion dismiss | opacity 1→0, scale 1→0.95 | 150ms | `ease-accelerate` |
| AI draft accept (highlight) | background tide-100 → transparent | 500ms | `ease-standard` |

## Reduced Motion

### WCAG 2.2 SC 2.3.3 (Level AAA) — Compliance Target

FlowFlex targets WCAG 2.2 AA as a minimum, but specifically implements SC 2.3.3 (Animation from Interactions) which recommends respecting `prefers-reduced-motion`.

### Implementation

```css
@layer base {
  /* Global reduced motion override */
  @media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
      animation-duration: 0.01ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.01ms !important;
      scroll-behavior: auto !important;
    }
  }
}
```

**What to preserve at `prefers-reduced-motion: reduce`:**
- Colour transitions (hover, focus) — these are not vestibular-triggering
- Opacity fades for shows/hides — OK if < 100ms
- Loading skeleton shimmer — replace with static slate-100 fill

**What to disable entirely:**
- All translate/scale transforms
- Spring physics
- Scroll animations
- Auto-playing animations
- The AI thinking dots wave (replace with static dots)

### User Preference Toggle

Provide an in-app "Reduce motion" toggle in the user profile settings under Accessibility. This sets a `data-reduced-motion="true"` attribute on `<html>` and respects the same CSS rules, independently of the OS setting.

## View Transitions API

For supported browsers (Chromium, Safari 18+), use the View Transitions API for page-level transitions. This provides native-feeling navigation without JavaScript animation libraries.

```php
// Livewire 3 page component — add to layout
<meta name="view-transition" content="same-origin">
```

```css
/* Shared element transition for the page header */
.page-header {
  view-transition-name: page-header;
}

::view-transition-old(page-header) {
  animation: 200ms ease-out fade-out;
}

::view-transition-new(page-header) {
  animation: 300ms ease-in fade-in;
}
```

**Progressive enhancement:** The View Transitions API is an enhancement — pages must work without it. Wrap in `if (document.startViewTransition)` checks.

## Micro-Interactions Catalogue

### Form Interactions

- **Label float:** On input focus, label translates up 4px over 150ms `ease-standard`
- **Validation check:** On valid input blur, small success-500 checkmark fades in right of input
- **Character counter:** Appears when user has typed 80% of max-length, fades in over 150ms
- **Password strength meter:** Bar width animates as user types, colour transitions between danger → tide → success

### Data Actions

- **Row selection:** Row background transitions to ocean-50 over 100ms
- **Bulk select all:** Each row selection staggers by 20ms (max 10 rows animated, rest instant)
- **Row reorder:** Spring physics on drop, gap closes on surrounding rows over 200ms
- **Column sort:** Sort arrow rotates 180deg over 150ms on direction change

### Notifications

- **Toast entry:** Slides down from top-right, translateY -16px→0 + opacity 0→1, 250ms `ease-decelerate`
- **Toast exit:** Slides right off-screen, translateX 0→360px, 200ms `ease-accelerate`
- **Toast stack:** Existing toasts shift down smoothly when new one arrives, 200ms `spring-gentle`
- **Toast progress bar:** Animates from 100% to 0% over the auto-dismiss duration

## Related

- [[Component Library]]
- [[Dark Mode]]
- [[Brand Foundation]]
