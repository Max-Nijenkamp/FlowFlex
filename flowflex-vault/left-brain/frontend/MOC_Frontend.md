---
type: moc
section: left-brain/frontend
last_updated: 2026-05-08
---

# Frontend — Map of Content

All public-facing Vue 3 + Inertia.js pages. These are **not** Filament panels — they run outside the authenticated admin workspace and are visible to the public, customers, or external users.

---

## What Goes Here

```mermaid
graph TD
    subgraph Public["Public (no auth required)"]
        MS["Marketing Site\n(landing pages, pricing, blog)"]
        DM["Demo Request Flow"]
    end

    subgraph Checkout["Commerce (partial auth)"]
        SF["Storefront & Checkout"]
        BP["Booking & Appointments"]
    end

    subgraph Customer["Customer-Authenticated Portals"]
        CP["Client Portal\n(invoices, projects, tickets)"]
        LP["Learner Portal\n(courses, certificates)"]
        CB["Community\n(forums, member directory)"]
    end

    subgraph Shared["Shared UI"]
        VIO["Vue+Inertia Overview\n(architecture, components)"]
    end
```

---

## Pages & Sections

### Marketing Site (Public)
- [[marketing-site]] — overview of all marketing site pages
  - Homepage — hero, features grid, pricing preview, social proof
  - Pricing page — plan comparison, monthly/annual toggle, FAQs
  - Features pages — per-domain feature marketing pages
  - Blog — content marketing, SEO articles
  - About / Company — team, mission, culture
  - Demo request flow — multi-step lead capture
  - Legal pages — Terms, Privacy, GDPR, Cookie policy

### Public Commerce Pages
- [[public-pages#storefront]] — product listing + product detail + cart
- [[public-pages#checkout]] — multi-step checkout (Stripe Elements)
- [[public-pages#booking]] — service booking calendar + confirmation

### Customer Portals
- [[client-portal]] — customer-facing portal (invoices, project status, tickets, docs)
- [[learner-portal]] — external learner course access, progress, certificates
- [[community-public]] — community forums, member directory, event listings

---

## Technology

```mermaid
graph LR
    Laravel["Laravel 13\n(routing, Inertia responses)"]
    Inertia["Inertia.js v2\n(client-side navigation)"]
    Vue["Vue 3\n(components, composables)"]
    TS["TypeScript 5\n(typed props from PHP DTOs)"]
    Tailwind["Tailwind CSS v4\n(design tokens)"]
    Vite["Vite 6\n(build, HMR)"]

    Laravel --> Inertia --> Vue --> TS
    Vue --> Tailwind
    Vue --> Vite
```

### Key Conventions

- All pages live in `resources/js/pages/` (kebab-case directory per section)
- Shared components: `resources/js/components/`
- Composables: `resources/js/composables/`
- TypeScript types auto-generated from PHP Data classes
- No jQuery, no Bootstrap
- SSR-compatible (Laravel + `@inertiajs/ssr`)

---

## Routing

Public routes in `routes/web.php` (no auth middleware):

```php
// Marketing site
Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/features/{domain}', [MarketingController::class, 'features'])->name('features');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');

// Client portal (auth:portal guard)
Route::middleware('auth:portal')->prefix('portal')->group(function () {
    Route::get('/dashboard', [PortalController::class, 'dashboard']);
    Route::get('/invoices', [PortalInvoiceController::class, 'index']);
});

// Learner portal (auth:learner guard)
Route::middleware('auth:learner')->prefix('learn')->group(function () {
    Route::get('/courses', [LearnerController::class, 'courses']);
});
```

---

## SEO

- All marketing site pages have meta title, description, OG image in Inertia `<Head>`
- Sitemap auto-generated via `spatie/laravel-sitemap`
- Structured data (JSON-LD) on pricing and homepage
- Blog uses static SSR render for crawler access

---

## Related

- [[00_MOC_LeftBrain]]
- [[tech-stack]]
- [[MOC_Marketing]] — Marketing domain (Filament panel for customers)
- [[MOC_Ecommerce]] — E-commerce domain (Filament + storefront)
- [[MOC_LMS]] — Learning domain (Filament + learner portal)
- [[MOC_Community]] — Community domain (Filament + community pages)
