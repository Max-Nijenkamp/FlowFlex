---
tags: [flowflex, design, filament, tailwind, implementation]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Filament Implementation

How the FlowFlex design system is implemented in Filament 5 and Tailwind CSS v4.

Updated for 2026: Tailwind CSS v4 config format, Filament 5 dark mode handling, CSS `@layer` architecture, per-panel theme override pattern, and two new panels for AI & Automation and Community & Social.

## Filament Theme Config

FlowFlex extends the Filament default theme. Theme override lives at `resources/css/filament/admin/theme.css`.

### CSS Custom Properties (Filament 5 Format)

Filament 5 reads CSS custom properties from `:root` directly. The `--primary-*` scale maps to the Filament internal colour system.

```css
/* resources/css/filament/admin/theme.css */
@import '/vendor/filament/filament/resources/css/theme.css';

@config '../../../../tailwind.config.js';

@layer tokens {
  :root {
    /* FlowFlex Ocean primary scale */
    --primary-50:  235 248 253;   /* #EBF8FD */
    --primary-100: 212 240 250;   /* #D4F0FA */
    --primary-200: 170 223 243;   /* #AADFF3 */
    --primary-300: 127 204 233;   /* #7FCCE9 */
    --primary-400: 75  179 220;   /* #4BB3DC */
    --primary-500: 33  153 200;   /* #2199C8 — primary action */
    --primary-600: 26  127 168;   /* #1A7FA8 */
    --primary-700: 19  95  127;   /* #135F7F */
    --primary-800: 15  61  86;    /* #0F3D56 */
    --primary-900: 13  45  63;    /* #0D2D3F */
    --primary-950: 6   24  32;    /* #061820 */

    /* Sidebar — ocean dark scale */
    --sidebar-bg:           13 45 63;   /* ocean-900 */
    --sidebar-item-active:  19 95 127;  /* ocean-700 */
    --sidebar-item-hover:   15 61 86;   /* ocean-800 */
  }
}
```

**Note on Filament 5 colour format:** Filament 5 uses RGB channel values (without `rgb()` wrapper) as CSS custom property values, not hex. This is intentional — Filament constructs colours with opacity modifiers (e.g., `rgb(var(--primary-500) / 0.2)`). Always provide values in `R G B` format.

## Panel Colours

Each Filament panel uses its domain colour as the primary colour override. Set this in the panel's `PanelProvider`:

```php
// App\Providers\Filament\HrPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('hr')
        ->path('hr')
        ->colors([
            'primary' => Color::hex('#7C3AED'),  // violet
        ])
        ->darkMode(DarkModeToggle::class);
}
```

Filament 5's `Color::hex()` helper automatically generates the full 50–950 scale from a single hex value.

### Domain → Panel Colour Mapping

| Domain | Panel ID | Primary Hex | Colour name |
|---|---|---|---|
| Core / Workspace | `workspace` | `#2199C8` | Ocean |
| HR & People | `hr` | `#7C3AED` | Violet |
| Projects & Work | `projects` | `#4F46E5` | Indigo |
| Finance & Accounting | `finance` | `#059669` | Emerald |
| CRM & Sales | `crm` | `#2563EB` | Blue |
| Marketing & Content | `marketing` | `#DB2777` | Pink |
| Operations | `operations` | `#D97706` | Amber |
| Analytics & BI | `analytics` | `#9333EA` | Purple |
| IT & Security | `it` | `#475569` | Slate |
| Legal & Compliance | `legal` | `#DC2626` | Red |
| E-commerce | `ecommerce` | `#0D9488` | Teal |
| Communications | `communications` | `#0284C7` | Sky |
| Learning & Dev | `lms` | `#EA580C` | Orange |
| AI & Automation | `ai` | `#0891B2` | Cyan |
| Community & Social | `community` | `#E11D48` | Rose |

## Tailwind CSS v4 Configuration

Tailwind v4 uses a CSS-first config format. The `tailwind.config.js` is replaced by a CSS `@theme` block.

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
  /* Ocean colour scale */
  --color-ocean-50:  #EBF8FD;
  --color-ocean-100: #D4F0FA;
  --color-ocean-200: #AADFF3;
  --color-ocean-300: #7FCCE9;
  --color-ocean-400: #4BB3DC;
  --color-ocean-500: #2199C8;
  --color-ocean-600: #1A7FA8;
  --color-ocean-700: #135F7F;
  --color-ocean-800: #0F3D56;
  --color-ocean-900: #0D2D3F;
  --color-ocean-950: #061820;

  /* Tide (amber/warning) */
  --color-tide-50:  #FFFBEB;
  --color-tide-100: #FEF3C7;
  --color-tide-200: #FDE68A;
  --color-tide-400: #F59E0B;
  --color-tide-500: #D97706;
  --color-tide-600: #B45309;

  /* Typography */
  --font-sans: 'Inter', system-ui, sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', monospace;

  /* Border radius */
  --radius-sm:   4px;
  --radius-md:   6px;
  --radius-lg:   8px;
  --radius-xl:   12px;
  --radius-2xl:  16px;
  --radius-full: 9999px;

  /* Spacing (4px base scale) */
  --spacing-1:  4px;
  --spacing-2:  8px;
  --spacing-3:  12px;
  --spacing-4:  16px;
  --spacing-5:  20px;
  --spacing-6:  24px;
  --spacing-8:  32px;
  --spacing-10: 40px;
  --spacing-12: 48px;
  --spacing-16: 64px;
  --spacing-20: 80px;
  --spacing-24: 96px;
}
```

**Backward compatibility:** If still using Tailwind v3 syntax in Filament components, keep `tailwind.config.js` alongside. Migrate to v4 format incrementally — both formats can coexist during the transition.

## CSS @layer Architecture

```css
/* Recommended layer ordering */
@layer tokens, base, components, utilities, overrides;

@layer tokens {
  /* Colour, typography, spacing tokens */
}

@layer base {
  /* HTML element defaults, font loading, reset overrides */
}

@layer components {
  /* FlowFlex component classes (.btn-primary, .card, etc.) */
}

@layer utilities {
  /* Tailwind utility classes */
}

@layer overrides {
  /* Filament component overrides, per-panel overrides */
}
```

## Filament Component Overrides

### Tables

```php
// All tables use this configuration
Tables\Components\Table::configureUsing(function (Table $table) {
    $table
        ->striped()                    // Alternating slate-50 rows
        ->paginated([25, 50, 100])
        ->defaultPaginationPageOption(25)
        ->persistSortInSession()
        ->persistSearchInSession()
        ->emptyStateHeading('No records yet')
        ->emptyStateIcon('heroicon-o-inbox');
});
```

### Forms

```php
// Global form field defaults
Forms\Components\TextInput::configureUsing(function (TextInput $field) {
    $field->maxLength(255);
});

Forms\Components\Select::configureUsing(function (Select $field) {
    $field->native(false);  // Custom styled select, not browser default
});

Forms\Components\DatePicker::configureUsing(function (DatePicker $field) {
    $field->native(false)->displayFormat('d M Y');
});
```

### Notifications

Filament notifications are mapped to the FlowFlex toast design:

```php
// In ServiceProvider or panel config
Notifications\Notification::configureUsing(function (Notification $notification) {
    $notification->duration(5000);  // 5s auto-dismiss
});
```

```css
/* In theme.css — override Filament notification positioning */
.fi-notifications {
  top: var(--space-4);
  right: var(--space-4);
  max-width: 360px;
}

.fi-no-notification {
  border-inline-start: 4px solid;
  border-radius: var(--radius-lg);
}
```

### Navigation / Sidebar

```css
/* resources/css/filament/admin/theme.css */
.fi-sidebar {
  background-color: rgb(var(--sidebar-bg));
  width: 256px;
}

.fi-sidebar-item-button[aria-current="page"] {
  background-color: rgb(var(--sidebar-item-active) / 0.5);
  border-inline-start: 3px solid rgb(var(--primary-400));
}

.fi-sidebar-group-label {
  font-size: 0.6875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgb(var(--primary-300));
}
```

## Dark Mode in Filament 5

Filament 5 supports dark mode natively. Integrate with FlowFlex's `data-theme` attribute system:

```php
// In panel provider
->darkMode(DarkModeToggle::class)
```

```js
// resources/js/theme.js — sync Filament dark mode with FlowFlex data-theme
document.addEventListener('DOMContentLoaded', () => {
    const observer = new MutationObserver(() => {
        const isDark = document.documentElement.classList.contains('dark');
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
```

## Asset Checklist

When producing any UI screen in Filament:

- [ ] All colours from the FlowFlex palette via CSS custom properties — no arbitrary hex values
- [ ] Font sizes from the type scale — no arbitrary `text-[15px]`
- [ ] Spacing from the spacing system — no `p-[17px]`
- [ ] Correct border-radius token for the component type
- [ ] Correct icon size for the context (Heroicons v2)
- [ ] All interactive states defined (hover, focus, disabled, loading)
- [ ] Dark mode verified — all text meets APCA Lc targets
- [ ] Empty state designed for every Filament Table
- [ ] Correct domain panel colour (not ocean in the HR panel)
- [ ] Domain icon and colour badge used in page header
- [ ] `Container` component used on all page layouts for max-width cap
- [ ] Skeleton loading state on all async widgets
- [ ] `prefers-reduced-motion` respected for all custom animations
- [ ] ARIA labels on all icon-only actions

## Related

- [[Colour System]]
- [[Typography]]
- [[Component Library]]
- [[Dark Mode]]
- [[Tech Stack]]
- [[Panel Map]]
