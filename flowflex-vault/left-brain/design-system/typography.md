---
type: design
section: design-system
last_updated: 2026-05-09
---

# Typography

Quick-reference for font loading and type scale. For the rationale behind typeface choices and the full type system context, see [[brand-foundation]].

---

## Font loading

Inter is loaded via Bunny Fonts — GDPR-friendly, EU-hosted CDN. Do not use Google Fonts directly.

Add to `resources/views/layouts/app.blade.php`:

```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link
  href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap"
  rel="stylesheet"
>
```

JetBrains Mono is only needed in views that render code blocks or terminal output. Load it conditionally rather than globally:

```html
<link
  href="https://fonts.bunny.net/css?family=jetbrains-mono:400&display=swap"
  rel="stylesheet"
>
```

---

## Type scale

| Role | Size | Weight | Line height | Tailwind classes |
|---|---|---|---|---|
| Display | 48–72px | 800 | 1.1 | `text-6xl font-extrabold` |
| H1 | 36px | 700 | 1.2 | `text-4xl font-bold` |
| H2 | 30px | 600 | 1.3 | `text-3xl font-semibold` |
| H3 | 24px | 600 | 1.4 | `text-2xl font-semibold` |
| H4 | 20px | 600 | 1.4 | `text-xl font-semibold` |
| Body | 16px | 400 | 1.6 | `text-base` |
| Body small | 14px | 400 | 1.5 | `text-sm` |
| Label / UI | 12px | 500 | 1.4 | `text-xs font-medium` |
| Caption | 11px | 400 | 1.4 | `text-[11px]` |
| Code | 14px | 400 | 1.6 | `font-mono text-sm` |

Display is marketing/hero only. H1 is the largest heading in application screens.

---

## Filament font setup

Register Inter in each domain's `PanelProvider` so Filament's own components use it:

```php
->font('Inter')
```

Filament will resolve Inter from the Bunny Fonts CDN automatically when this is set. No separate `<link>` tag is needed inside Filament panel views.

---

## Vue and Tailwind usage

Set Inter as the default sans-serif in `tailwind.config.js`:

```js
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
        mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
      },
    },
  },
}
```

With this in place, every element using `font-sans` (Tailwind's default) will resolve to Inter. Use standard Tailwind text utilities throughout — `text-sm`, `text-base`, `font-semibold`, etc. — rather than inline styles or custom classes.

---

## Content typography

For rich-text areas (module descriptions, wiki pages, email template previews, document content), use the `@tailwindcss/typography` plugin:

```html
<div class="prose prose-indigo prose-sm max-w-none dark:prose-invert">
  <!-- server-rendered HTML from Tiptap or similar -->
</div>
```

`prose-indigo` applies the brand primary colour to links and headings inside prose content. `prose-sm` is appropriate for compact module descriptions; use `prose` (base) for full-page wiki content.

---

## Related

[[brand-foundation]], [[tech-stack]]
