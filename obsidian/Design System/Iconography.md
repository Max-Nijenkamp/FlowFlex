---
tags: [flowflex, design, icons, heroicons]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Iconography

Heroicons as the primary library. Consistent sizing, styling, and usage rules.

Updated for 2026: Heroicons v2 as definitive standard, custom icon guidance for domain-specific needs, SVG sprite delivery, two new domain icons for AI & Automation and Community & Social.

## Icon Libraries

**Primary:** Heroicons v2 — consistent with the Filament/Tailwind ecosystem. All 292 icons available in outline and solid variants.

**Secondary:** Phosphor Icons — for richer or more expressive moments where Heroicons doesn't have a suitable icon. Use sparingly (target: > 95% Heroicons coverage).

**Custom icons:** For domain-specific concepts with no suitable library icon (e.g., FlowFlex logo mark, specific module concepts). See Custom Icon Guidelines below.

**Never use:** Emoji as icons in the application UI. Never use Font Awesome — it conflicts with the Heroicons visual language.

## Style Rules

- **Outline style** is the default for all icons
- **Solid style** is used exclusively for: active/selected nav items, filled badge icons, interactive toggle states
- **Mixed styles** on the same screen are prohibited. If one nav item is solid (active), all others are outline (consistent)
- **Stroke width:** Heroicons v2 uses a 1.5px stroke. Never scale icons in a way that distorts the stroke.

## Icon Sizes

| Token | Pixels | CSS | Usage |
|---|---|---|---|
| `icon-xs` | 12px | `width: 12px; height: 12px` | Inline with micro text, compact badges |
| `icon-sm` | 16px | `width: 16px; height: 16px` | Inline with body text, button icons |
| `icon-md` | 20px | `width: 20px; height: 20px` | Navigation items, form field icons |
| `icon-lg` | 24px | `width: 24px; height: 24px` | Section headers, card icons |
| `icon-xl` | 32px | `width: 32px; height: 32px` | Feature icons, empty states |
| `icon-2xl` | 48px | `width: 48px; height: 48px` | Hero feature moments, large empty states |

**Never use:** Non-standard sizes like 18px or 22px. Always snap to this scale.

## Icon + Text Spacing

| Icon size | Gap to text |
|---|---|
| `icon-xs` (12px) | 4px |
| `icon-sm` (16px) | 6px |
| `icon-md` (20px) | 6px |
| `icon-lg` (24px) | 8px |
| `icon-xl`+ | 10px |

## Icon Colour

Icons inherit `currentColor` by default — their colour matches the surrounding text colour. Exceptions:

- **Domain icons** in module headers: use the domain's primary colour (e.g., HR icons are `violet-500`)
- **Semantic icons** (success, warning, danger, info): always use the semantic colour, never muted
- **Navigation icons** (active): white or `ocean-300` against the dark sidebar
- **AI sparkle icon**: always `ocean-400` — do not recolour for context

## Domain Icons

Each domain has a designated Heroicons v2 icon. Used consistently across sidebar, module cards, domain headers, and breadcrumbs.

| Domain | Icon (Heroicons v2) | Colour |
|---|---|---|
| Core Platform | `cog-6-tooth` | `ocean-500` |
| HR & People | `users` | `violet-500` |
| Projects & Work | `rectangle-stack` | `indigo-500` |
| Finance & Accounting | `banknotes` | `emerald-500` |
| CRM & Sales | `building-office-2` | `blue-500` |
| Marketing & Content | `megaphone` | `pink-500` |
| Operations & Field | `wrench-screwdriver` | `amber-500` |
| Analytics & BI | `chart-bar` | `purple-500` |
| IT & Security | `shield-check` | `slate-500` |
| Legal & Compliance | `scale` | `red-500` |
| E-commerce | `shopping-bag` | `teal-500` |
| Communications | `chat-bubble-left-right` | `sky-500` |
| Learning & Dev | `academic-cap` | `orange-500` |
| AI & Automation | `sparkles` | `cyan-500` |
| Community & Social | `user-group` | `rose-500` |

## Common UI Icons

Frequently used icons and their correct Heroicons v2 names:

| Action | Icon name | Notes |
|---|---|---|
| Create / Add | `plus` or `plus-circle` | Use `plus-circle` for primary empty-state CTAs |
| Edit | `pencil-square` | Not `pencil` |
| Delete | `trash` | Always danger-coloured |
| Search | `magnifying-glass` | Not `search` (Heroicons v1 name) |
| Filter | `funnel` | |
| Sort | `arrows-up-down` | |
| Close / Dismiss | `x-mark` | Not `x` |
| Expand / Open | `chevron-right` | Inline expansion |
| Collapse | `chevron-down` | Open state |
| Settings | `cog-8-tooth` | For user-facing settings |
| System config | `cog-6-tooth` | For admin/system config |
| Notification | `bell` | |
| Notification (unread) | `bell-alert` | Solid, with unread indicator |
| Download | `arrow-down-tray` | |
| Upload | `arrow-up-tray` | |
| Export | `arrow-up-on-square` | |
| Import | `arrow-down-on-square` | |
| Link / External | `arrow-top-right-on-square` | Opens in new tab |
| Copy | `clipboard` | Before copy; `clipboard-document-check` after |
| Check / Success | `check-circle` | Solid, success-500 |
| Warning | `exclamation-triangle` | Solid, tide-500 |
| Error | `x-circle` | Solid, danger-500 |
| Info | `information-circle` | Solid, ocean-500 |
| Loading / Spinner | Custom — see below | Never use an icon for loading |
| Drag handle | `bars-2` or `ellipsis-vertical` (×2) | |
| AI / Assistant | `sparkles` | ocean-400, never repurposed |
| Lock / Secure | `lock-closed` | |
| Unlock | `lock-open` | |
| Visibility on | `eye` | |
| Visibility off | `eye-slash` | |

## SVG Delivery

All icons should be delivered as inline SVG for maximum styling control and no HTTP requests.

**In Blade/Filament:**
```blade
{{-- Heroicons via the heroicons/blade-heroicons package --}}
<x-heroicon-o-users class="w-5 h-5 text-violet-500" />  {{-- outline --}}
<x-heroicon-s-users class="w-5 h-5 text-violet-500" />  {{-- solid --}}
```

**SVG Sprite (for non-Blade contexts):**
```html
<svg aria-hidden="true" focusable="false">
  <use href="/icons/heroicons-sprite.svg#users"></use>
</svg>
```

**Never use:** `<img src="icon.svg">` — this prevents CSS colour control.

## Custom Icon Guidelines

When Heroicons and Phosphor Icons don't have a suitable icon:

1. Design in a 24×24px canvas with 1.5px stroke weight to match Heroicons v2
2. Use `currentColor` for all strokes and fills
3. Export as optimised SVG (run through SVGO)
4. Store in `resources/svg/icons/custom/`
5. Register in the Blade `<x-icon>` component
6. Document the icon name and use case here in a custom icon table

**Target:** Keep custom icons to fewer than 20. If you need more than 20 custom icons, evaluate whether a different library would be more suitable.

## Accessibility

- All meaningful icons require either visible text label OR `aria-label` / `title` on the SVG
- Decorative icons (purely visual, no unique meaning): `aria-hidden="true"` on the SVG element
- Icon-only buttons: `aria-label="[action]"` on the `<button>` element, not the SVG
- Touch targets for icon buttons: minimum 44×44px (can be larger than the visible 20px icon)

## Related

- [[Component Library]]
- [[Colour System]]
- [[Filament Implementation]]
