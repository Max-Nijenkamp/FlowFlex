---
type: design
section: design-system
last_updated: 2026-05-09
---

# Colour System

Quick-reference lookup for all FlowFlex colour tokens. For the rationale, naming decisions, and domain colour rules, see [[brand-foundation]].

---

## Platform semantic colours

| Token | Hex value | Tailwind reference | Usage |
|---|---|---|---|
| `--color-brand-primary` | `#4F46E5` | Indigo-600 | Primary CTA buttons, active nav indicator, links, focus rings |
| `--color-brand-primary-hover` | `#4338CA` | Indigo-700 | Hover state on primary elements |
| `--color-brand-primary-light` | `#EEF2FF` | Indigo-50 | Backgrounds behind primary-coloured elements, badges |
| `--color-brand-accent` | `#7C3AED` | Violet-600 | AI features, premium tier highlights, marketing accent |
| `--color-brand-accent-light` | `#F5F3FF` | Violet-50 | Accent backgrounds |
| `--color-neutral-900` | `#111827` | Gray-900 | Primary text, headings |
| `--color-neutral-700` | `#374151` | Gray-700 | Secondary text, form labels |
| `--color-neutral-400` | `#9CA3AF` | Gray-400 | Placeholder text, disabled states |
| `--color-neutral-200` | `#E5E7EB` | Gray-200 | Dividers, input borders |
| `--color-neutral-100` | `#F3F4F6` | Gray-100 | Subtle backgrounds, table rows |
| `--color-neutral-50` | `#F9FAFB` | Gray-50 | Page background |
| `--color-success` | `#059669` | Emerald-600 | Success states, approved indicators |
| `--color-success-light` | `#D1FAE5` | Emerald-100 | Success message backgrounds |
| `--color-warning` | `#D97706` | Amber-600 | Warnings, pending states |
| `--color-warning-light` | `#FEF3C7` | Amber-100 | Warning message backgrounds |
| `--color-danger` | `#DC2626` | Red-600 | Errors, destructive actions |
| `--color-danger-light` | `#FEE2E2` | Red-100 | Error message backgrounds |
| `--color-info` | `#0284C7` | Sky-600 | Informational states, neutral notifications |
| `--color-info-light` | `#E0F2FE` | Sky-100 | Info message backgrounds |

---

## Domain colour palette

| # | Domain | Primary | Light bg | Tailwind name |
|---|---|---|---|---|
| 00 | Foundation / Admin | `#111827` | `#F9FAFB` | Gray-900 |
| 01 | Core Platform | `#111827` | `#F9FAFB` | Gray-900 |
| 02 | HR & People | `#7C3AED` | `#EDE9FE` | Violet-600 |
| 03 | Projects & Work | `#4F46E5` | `#EEF2FF` | Indigo-600 |
| 04 | Finance & Accounting | `#059669` | `#D1FAE5` | Emerald-600 |
| 05 | CRM & Sales | `#DC2626` | `#FEE2E2` | Red-600 |
| 06 | Marketing & Content | `#DB2777` | `#FCE7F3` | Pink-600 |
| 07 | Operations | `#D97706` | `#FEF3C7` | Amber-600 |
| 08 | Analytics & BI | `#0284C7` | `#E0F2FE` | Sky-600 |
| 09 | IT & Security | `#6B7280` | `#F3F4F6` | Gray-500 |
| 10 | Legal & Compliance | `#92400E` | `#FFFBEB` | Amber-800 |
| 11 | E-commerce | `#0891B2` | `#CFFAFE` | Cyan-600 |
| 12 | Communications | `#7C3AED` | `#EDE9FE` | Violet-600 |
| 13 | Learning & Development | `#16A34A` | `#DCFCE7` | Green-600 |
| 14 | AI & Automation | `#6366F1` | `#EEF2FF` | Indigo-500 |
| 15 | Community & Social | `#F59E0B` | `#FEF3C7` | Amber-400 |
| 16 | Workplace & Facility | `#0F766E` | `#CCFBF1` | Teal-700 |
| 17 | Professional Services (PSA) | `#7E22CE` | `#F5F3FF` | Purple-700 |
| 18 | Product-Led Growth | `#0369A1` | `#E0F2FE` | Sky-700 |
| 19 | Business Travel | `#1D4ED8` | `#DBEAFE` | Blue-700 |
| 20 | ESG & Sustainability | `#15803D` | `#DCFCE7` | Green-700 |
| 21 | Real Estate & Property | `#57534E` | `#F5F5F4` | Stone-600 |
| 22 | Customer Success | `#0EA5E9` | `#E0F2FE` | Sky-500 |
| 23 | Subscription Billing | `#10B981` | `#D1FAE5` | Emerald-500 |
| 24 | Procurement | `#F97316` | `#FFEDD5` | Orange-500 |
| 25 | FP&A | `#6366F1` | `#EEF2FF` | Indigo-500 |
| 26 | Events Management | `#EC4899` | `#FCE7F3` | Pink-500 |
| 27 | Document Management | `#8B5CF6` | `#EDE9FE` | Violet-500 |
| 28 | Whistleblowing & Ethics | `#6D28D9` | `#EDE9FE` | Violet-700 |
| 29 | Field Service Management | `#EA580C` | `#FFEDD5` | Orange-600 |
| 30 | Pricing Management | `#0D9488` | `#CCFBF1` | Teal-600 |
| 31 | Enterprise Risk Management | `#B91C1C` | `#FEE2E2` | Red-700 |

---

## CSS variable setup

Declare all tokens in `resources/css/app.css` under the base layer:

```css
@layer base {
  :root {
    --color-brand-primary:       #4F46E5;
    --color-brand-primary-hover: #4338CA;
    --color-brand-primary-light: #EEF2FF;
    --color-brand-accent:        #7C3AED;
    --color-brand-accent-light:  #F5F3FF;

    --color-neutral-900: #111827;
    --color-neutral-700: #374151;
    --color-neutral-400: #9CA3AF;
    --color-neutral-200: #E5E7EB;
    --color-neutral-100: #F3F4F6;
    --color-neutral-50:  #F9FAFB;

    --color-success:       #059669;
    --color-success-light: #D1FAE5;
    --color-warning:       #D97706;
    --color-warning-light: #FEF3C7;
    --color-danger:        #DC2626;
    --color-danger-light:  #FEE2E2;
    --color-info:          #0284C7;
    --color-info-light:    #E0F2FE;
  }
}
```

---

## Tailwind config

Extend `tailwind.config.js` to expose the CSS variables as Tailwind utilities:

```js
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
  theme: {
    extend: {
      colors: {
        brand: {
          primary:      'var(--color-brand-primary)',
          'primary-hover': 'var(--color-brand-primary-hover)',
          'primary-light': 'var(--color-brand-primary-light)',
          accent:       'var(--color-brand-accent)',
          'accent-light':  'var(--color-brand-accent-light)',
        },
        neutral: {
          900: 'var(--color-neutral-900)',
          700: 'var(--color-neutral-700)',
          400: 'var(--color-neutral-400)',
          200: 'var(--color-neutral-200)',
          100: 'var(--color-neutral-100)',
          50:  'var(--color-neutral-50)',
        },
        success: {
          DEFAULT: 'var(--color-success)',
          light:   'var(--color-success-light)',
        },
        warning: {
          DEFAULT: 'var(--color-warning)',
          light:   'var(--color-warning-light)',
        },
        danger: {
          DEFAULT: 'var(--color-danger)',
          light:   'var(--color-danger-light)',
        },
        info: {
          DEFAULT: 'var(--color-info)',
          light:   'var(--color-info-light)',
        },
      },
    },
  },
}
```

This lets you write `bg-brand-primary`, `text-danger`, `border-warning`, etc. throughout the codebase instead of raw hex values.

---

## Dark mode

All platform semantic colours have dark mode equivalents applied via Tailwind's `dark:` prefix. For example: `text-neutral-900 dark:text-white`, `bg-neutral-50 dark:bg-neutral-900`.

Domain colours do **not** change value in dark mode — only their usage context flips. A domain badge that uses a light background in light mode uses the domain primary with reduced opacity in dark mode rather than the light-bg variant. See each domain panel's component spec for the exact dark-mode pattern.

Filament 5 handles dark mode toggling for admin panels automatically. User preference is stored in `users.theme` (`light` | `dark` | `system`).

---

## Related

[[brand-foundation]], [[tech-stack]]
