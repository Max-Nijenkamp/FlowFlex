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

| # | Domain | Colour | CSS Accent | cssclass |
|---|---|---|---|---|
| 01 | Core Platform | Slate | `#94A3B8` | `domain-admin` |
| 02 | HR & People | Purple | `#A78BFA` | `domain-hr` |
| 03 | Projects & Work | Indigo | `#818CF8` | `domain-projects` |
| 04 | Finance & Accounting | Emerald | `#34D399` | `domain-finance` |
| 05 | CRM & Sales | Rose | `#FB7185` | `domain-crm` |
| 06 | Marketing & Content | Pink | `#F472B6` | `domain-marketing` |
| 07 | Operations | Amber | `#FCD34D` | `domain-operations` |
| 08 | Analytics & BI | Sky | `#38BDF8` | `domain-analytics` |
| 09 | IT & Security | Gray | `#9CA3AF` | `domain-it` |
| 10 | Legal & Compliance | Amber-warm | `#D97706` | `domain-legal` |
| 11 | E-commerce | Cyan | `#22D3EE` | `domain-ecommerce` |
| 12 | Communications | Light-cyan | `#67E8F9` | `domain-comms` |
| 13 | Learning & Dev | Green | `#4ADE80` | `domain-lms` |
| 14 | AI & Automation | Violet | `#C084FC` | `domain-ai` |
| 15 | Community & Social | Amber-gold | `#FDE68A` | `domain-community` |
| 16 | Workplace & Facility | Teal | `#2DD4BF` | `domain-workplace` |
| 17 | PSA | Fuchsia | `#D946EF` | `domain-psa` |
| 18 | Product-Led Growth | Blue | `#60A5FA` | `domain-plg` |
| 19 | Business Travel | Blue-500 | `#3B82F6` | `domain-travel` |
| 20 | ESG & Sustainability | Green-300 | `#86EFAC` | `domain-esg` |
| 21 | Real Estate & Property | Stone | `#D6D3D1` | `domain-realestate` |
| 22 | Customer Success | Sky | `#38BDF8` | `domain-cs` |
| 23 | Subscription Billing | Emerald-500 | `#10B981` | `domain-subscriptions` |
| 24 | Procurement | Orange | `#F97316` | `domain-procurement` |
| 25 | FP&A | Indigo-400 | `#818CF8` | `domain-fpa` |
| 26 | Events Management | Pink-500 | `#EC4899` | `domain-events` |
| 27 | Document Management | Violet | `#8B5CF6` | `domain-dms` |
| 28 | Whistleblowing & Ethics | Violet-700 | `#6D28D9` | `domain-whistleblowing` |
| 29 | Field Service Management | Orange-600 | `#EA580C` | `domain-fsm` |
| 30 | Pricing Management | Teal-600 | `#0D9488` | `domain-pricing` |
| 31 | Enterprise Risk Management | Red-700 | `#B91C1C` | `domain-risk` |

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
