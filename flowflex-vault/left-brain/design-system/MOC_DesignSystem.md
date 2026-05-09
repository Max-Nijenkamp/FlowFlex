---
type: moc
section: left-brain/design-system
last_updated: 2026-05-09
---

# Design System — Map of Content

Navigation hub for the FlowFlex design system. This file points to where things live — it does not store the values itself.

---

## Single source of truth

[[brand-foundation]] is the master branding document. Everything in this section either derives from or links back to it. If any value here conflicts with `brand-foundation`, `brand-foundation` wins.

---

## Module registry

| File | What it covers |
|---|---|
| [[brand-foundation]] | Identity, brand values, logo, full colour palette (platform + all 32 domains), typography, spacing, iconography, motion, voice and tone, Filament implementation, white-label rules |
| [[colour-system]] | Colour token quick-reference, CSS variable declarations, Tailwind config extension, dark mode notes |
| [[typography]] | Type scale reference, Bunny Fonts loading, Filament font setup, Tailwind config, prose typography |

---

## Domain colour palette

FlowFlex has 32 domain colours (Foundation/Admin + 31 business domains). Each colour is a fixed identifier used in panel navigation, domain badges, and data visualisation.

For the full authoritative table with hex values, light-background variants, and Tailwind names, see [[brand-foundation#5. Platform Colour Palette]].

**Platform primary**: `#4F46E5` Indigo-600 — used for the FlowFlex brand itself, not for any domain.

---

## Filament implementation note

Each domain `PanelProvider` registers its domain colour via:

```php
->colors(['primary' => Color::hex('#7C3AED')])
->font('Inter')
->brandLogo(fn () => view('filament.brand.logo'))
->sidebarCollapsibleOnDesktop()
->darkMode(Feature::Enabled)
```

The full pattern with all panel options is documented in [[brand-foundation#11. Filament Panel Implementation]].

---

## Dark mode

All components support dark mode via Tailwind's `dark:` prefix. Filament 5 includes a built-in dark mode toggle. User preference is stored in `users.theme` (`light` | `dark` | `system`). Domain colours do not change value in dark mode — only their usage context (text vs background) adapts.

---

## Component library

Core Vue + Tailwind UI components referenced throughout domain specs:

| Component | Usage |
|---|---|
| `<FlowCard>` | Content container |
| `<FlowBadge>` | Status and domain category badges |
| `<FlowAvatar>` | User and company avatars |
| `<FlowTable>` | Sortable, filterable data tables |
| `<FlowModal>` | Modal dialogs |
| `<FlowDropdown>` | Context menus and select dropdowns |
| `<FlowToast>` | Notification toasts |
| `<FlowProgress>` | Progress bars (onboarding, project completion) |
| `<FlowChart>` | Chart.js wrapper |
| `<FlowRichText>` | Tiptap rich text editor |

---

## Related

- [[00_MOC_LeftBrain]]
- [[tech-stack]] — Tailwind CSS v4, Vue 3, Filament 5
- [[MOC_Domains]] — authoritative domain registry (colours sourced here)
