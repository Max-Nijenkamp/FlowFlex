---
type: moc
section: left-brain/design-system
last_updated: 2026-05-08
---

# Design System — Map of Content

Brand foundation, colour system, typography, spacing, components, motion, iconography, dark mode, data visualisation, and Filament implementation.

---

## Domain Colour Palette

Each business domain has its own colour. Used for panel navigation, domain badges, and dashboard widgets.

| Domain | Colour | Hex | Tailwind Class |
|---|---|---|---|
| Core Platform | Gray | `#111827` | `gray-900` |
| HR & People | Violet | `#7C3AED` | `violet-600` |
| Projects & Work | Indigo | `#4F46E5` | `indigo-600` |
| Finance & Accounting | Emerald | `#059669` | `emerald-600` |
| CRM & Sales | Red | `#DC2626` | `red-600` |
| Marketing & Content | Pink | `#DB2777` | `pink-600` |
| Operations | Amber | `#D97706` | `amber-600` |
| Analytics & BI | Sky | `#0284C7` | `sky-600` |
| IT & Security | Gray-500 | `#6B7280` | `gray-500` |
| Legal & Compliance | Amber-800 | `#92400E` | `amber-800` |
| E-commerce | Cyan | `#0891B2` | `cyan-600` |
| Communications | Violet-700 | `#6D28D9` | `violet-700` |
| Learning & Dev | Green | `#16A34A` | `green-600` |
| AI & Automation | Indigo-500 | `#6366F1` | `indigo-500` |
| Community | Amber-400 | `#F59E0B` | `amber-400` |

---

## Brand Foundation

FlowFlex brand:
- **Primary**: Indigo `#4F46E5` (platform-level CTAs, links, focus rings)
- **Neutral**: Gray-900 `#111827` (text), Gray-50 `#F9FAFB` (background)
- **Accent**: Violet `#7C3AED` (marketing highlights, AI features)
- **Success**: Emerald `#059669`
- **Warning**: Amber `#D97706`
- **Danger**: Red `#DC2626`

---

## Typography

- **Display/Hero**: Inter or Geist — 48–72px, weight 700–900
- **Heading**: Inter — 24–36px, weight 600
- **Body**: Inter — 16px, weight 400
- **Code**: JetBrains Mono — 14px
- **UI Labels**: Inter — 12–14px, weight 500

---

## Iconography

All icons from **Heroicons** (outline variant by default, solid for active states):

```php
// Filament resource
protected static string $navigationIcon = 'heroicon-o-users';

// In Blade/Vue
<x-heroicon-o-users class="w-5 h-5" />
```

Custom icons (FlowFlex-specific modules not covered by Heroicons) live in `resources/js/icons/`.

---

## Component Library

Core UI components (Vue + Tailwind):

| Component | Usage |
|---|---|
| `<FlowCard>` | Content container |
| `<FlowBadge>` | Status and category badges |
| `<FlowAvatar>` | User/company avatars |
| `<FlowTable>` | Sortable, filterable data tables |
| `<FlowModal>` | Modal dialogs |
| `<FlowDropdown>` | Context menus and select dropdowns |
| `<FlowToast>` | Notification toasts |
| `<FlowProgress>` | Progress bars (projects, onboarding) |
| `<FlowChart>` | Chart.js wrapper |
| `<FlowRichText>` | Tiptap rich text editor |

---

## Filament Implementation

Filament panels use domain colour for navigation accent:

```php
// In each Panel provider
->colors([
    'primary' => Color::hex('#7C3AED'), // HR violet
])
->brandLogo(fn () => view('filament.logos.hr'))
->sidebarCollapsibleOnDesktop()
->navigationGroups([...])
```

---

## Dark Mode

All components support dark mode via Tailwind `dark:` prefix.  
Filament 5 has built-in dark mode toggle.  
User preference stored in `users.theme` column.

---

## Related

- [[00_MOC_LeftBrain]]
- [[tech-stack]] — Tailwind CSS v4
