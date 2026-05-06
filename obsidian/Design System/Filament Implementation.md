---
tags: [flowflex, design, filament, tailwind, implementation]
domain: Design System
status: built
last_updated: 2026-05-06
---

# Filament Implementation

How the FlowFlex design system is implemented in Filament 5 and Tailwind CSS.

## Filament Theme Config

FlowFlex extends the Filament default theme. Theme override lives at `resources/css/filament/admin/theme.css`.

### CSS Custom Properties

```css
:root {
  --primary-50:  #EBF8FD;
  --primary-100: #D4F0FA;
  --primary-200: #AADFF3;
  --primary-300: #7FCCE9;
  --primary-400: #4BB3DC;
  --primary-500: #2199C8;  /* ocean-500 — primary action */
  --primary-600: #1A7FA8;
  --primary-700: #135F7F;
  --primary-800: #0F3D56;
  --primary-900: #0D2D3F;
  --primary-950: #061820;
}
```

## Panel Colours

Each Filament panel uses its domain colour as the primary colour override:

```php
// Example: HR Panel (violet)
FilamentColor::register([
    'primary' => [
        50  => '245, 243, 255',
        100 => '237, 233, 254',
        200 => '221, 214, 254',
        300 => '196, 181, 253',
        400 => '167, 139, 250',
        500 => '124, 58, 237',   // #7C3AED
        600 => '109, 40, 217',
        700 => '91, 33, 182',
        800 => '76, 29, 149',
        900 => '60, 21, 118',
        950 => '46, 16, 101',
    ],
]);
```

### Domain → Panel Colour Mapping

| Domain | Panel ID | Primary Hex |
|---|---|---|
| Core / Workspace | `workspace` | `#2199C8` (ocean) |
| HR & People | `hr` | `#7C3AED` (violet) |
| Projects & Work | `projects` | `#4F46E5` (indigo) |
| Finance & Accounting | `finance` | `#059669` (emerald) |
| CRM & Sales | `crm` | `#2563EB` (blue) |
| Marketing & Content | `marketing` | `#DB2777` (pink) |
| Operations | `operations` | `#D97706` (amber) |
| Analytics & BI | `analytics` | `#9333EA` (purple) |
| IT & Security | `it` | `#475569` (slate) |
| Legal & Compliance | `legal` | `#DC2626` (red) |
| E-commerce | `ecommerce` | `#0D9488` (teal) |
| Communications | `communications` | `#0284C7` (sky) |
| Learning & Dev | `lms` | `#EA580C` (orange) |

## Filament Component Overrides

- **Tables:** custom `striped()` style using `slate-50` alternating rows
- **Forms:** all inputs use FlowFlex input style via Tailwind config
- **Navigation:** sidebar colours overridden to use `ocean-900`/`ocean-800` scale
- **Widgets:** stats overview widgets use FlowFlex metric card style
- **Notifications:** mapped to FlowFlex toast design (left-border variant)

## Tailwind Config Extensions

```js
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        ocean: {
          50:  '#EBF8FD',
          100: '#D4F0FA',
          200: '#AADFF3',
          300: '#7FCCE9',
          400: '#4BB3DC',
          500: '#2199C8',
          600: '#1A7FA8',
          700: '#135F7F',
          800: '#0F3D56',
          900: '#0D2D3F',
          950: '#061820',
        },
        tide: {
          50:  '#FFFBEB',
          100: '#FEF3C7',
          200: '#FDE68A',
          400: '#F59E0B',
          500: '#D97706',
          600: '#B45309',
        },
        // success, danger, slate already match Tailwind defaults
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
      },
      borderRadius: {
        'sm':  '4px',
        'md':  '6px',
        'lg':  '8px',
        'xl':  '12px',
        '2xl': '16px',
      },
    },
  },
}
```

## Asset Checklist

When producing any UI screen:

- [ ] All colours from the FlowFlex palette — no arbitrary hex values
- [ ] Font sizes from the type scale — no arbitrary `text-[15px]`
- [ ] Spacing from the spacing system — no arbitrary `p-[17px]`
- [ ] Correct border-radius for the component type
- [ ] Correct icon size for the context
- [ ] All interactive states defined (hover, focus, disabled, loading)
- [ ] Dark mode colours correct
- [ ] Empty state designed for every data list
- [ ] WCAG AA contrast verified for all text
- [ ] Correct domain colour used (not ocean-500 in the HR module)
- [ ] Module badge/icon uses the correct domain icon and colour

## Related

- [[Colour System]]
- [[Typography]]
- [[Component Library]]
- [[Dark Mode]]
- [[Tech Stack]]
- [[Panel Map]]
