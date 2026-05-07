---
tags: [flowflex, marketing, implementation, status]
domain: Marketing Site
status: complete
last_updated: 2026-05-07
phase: marketing-v1
---

# Marketing Site Implementation Status

Tracks what has been built vs. planned for the marketing site.

## Backend (PHP)

### NavigationGroup
- [x] `MarketingContent = 'Marketing & Content'` added to enum

### Migrations
- [x] `2026_05_07_600000_create_demo_requests_table`
- [x] `2026_05_07_600001_create_blog_categories_table`
- [x] `2026_05_07_600002_create_blog_posts_table`
- [x] `2026_05_07_600003_create_testimonials_table`
- [x] `2026_05_07_600004_create_newsletter_subscribers_table`
- [x] `2026_05_07_600005_create_faq_entries_table`
- [x] `2026_05_07_600006_create_team_members_table`
- [x] `2026_05_07_600007_create_open_roles_table`
- [x] `2026_05_07_600008_create_changelog_entries_table`
- [x] `2026_05_07_600009_create_help_categories_table`
- [x] `2026_05_07_600010_create_help_articles_table`
- [x] `2026_05_07_600011_create_contact_submissions_table`

### Models (`app/Models/Marketing/`)
- [x] DemoRequest
- [x] BlogCategory
- [x] BlogPost
- [x] Testimonial
- [x] NewsletterSubscriber
- [x] FaqEntry
- [x] TeamMember
- [x] OpenRole
- [x] ChangelogEntry
- [x] HelpCategory
- [x] HelpArticle
- [x] ContactSubmission

### Filament Resources (`app/Filament/Admin/Resources/Marketing/`)
- [x] DemoRequestResource (list + view + edit status)
- [x] BlogCategoryResource (CRUD)
- [x] BlogPostResource (CRUD)
- [x] TestimonialResource (CRUD)
- [x] NewsletterSubscriberResource (list only)
- [x] FaqEntryResource (CRUD)
- [x] TeamMemberResource (CRUD)
- [x] OpenRoleResource (CRUD)
- [x] ChangelogEntryResource (CRUD)
- [x] HelpCategoryResource (CRUD)
- [x] HelpArticleResource (CRUD)
- [x] ContactSubmissionResource (list + view)

### Controllers (`app/Http/Controllers/Marketing/`)
- [x] MarketingController (static page renders)
- [x] DemoController (GET /demo + POST /demo)
- [x] ContactController (POST /contact)

### Routes
- [x] All marketing routes added to `routes/web.php`
- [x] Redirects: /register → /demo, /signup → /demo, /trial → /demo

---

## Frontend (Vue + Inertia)

### CSS
- [x] FlowFlex brand color tokens added to `app.css @theme` (ocean-*, slate-*, semantic)

### Packages installed
- [x] `vue-i18n@10` — EN/NL translations
- [x] `@vueuse/motion` — scroll-reveal animations

### i18n (`resources/js/i18n/`)
- [x] `index.ts` — createI18n setup (legacy: false, locales: en/nl)
- [x] `locales/en.ts` — full English translation keys for all pages
- [x] `locales/nl.ts` — full Dutch translation keys for all pages

### Layouts & Components
- [x] `resources/js/layouts/MarketingLayout.vue`
- [x] `resources/js/components/marketing/MarketingNav.vue` — sticky, glassmorphism scroll, dark mode toggle, EN/NL switcher, mobile hamburger
- [x] `resources/js/components/marketing/MarketingFooter.vue` — 4-column ocean-950, social icons (Lucide), i18n
- [x] `resources/js/app.ts` updated — MarketingLayout for Marketing/* pages, vue-i18n + MotionPlugin installed

### Pages
- [x] `pages/Welcome.vue` — Full homepage (hero, problem, how it works, module grid, stats, pricing teaser, CTA)
- [x] `pages/Marketing/Pricing.vue` — 3 plan cards, annual toggle, FAQ accordion
- [x] `pages/Marketing/Features.vue` — 13 domain cards with module chips
- [x] `pages/Marketing/About.vue` — Story, values, team, stats, CTA
- [x] `pages/Marketing/Demo.vue` — Demo request form with useForm, success state
- [x] `pages/Marketing/Contact.vue` — Contact reasons grid + contact form
- [x] `pages/Marketing/Blog/Index.vue` — Blog listing with props from server
- [x] `pages/Marketing/Blog/Post.vue` — Single post with body rendered as HTML
- [x] `pages/Marketing/Changelog.vue` — Chronological changelog entries
- [x] `pages/Marketing/Careers.vue` — Careers page with empty state
- [x] `pages/Marketing/Legal/Privacy.vue` — Full GDPR privacy policy
- [x] `pages/Marketing/Legal/Terms.vue` — Terms of service
- [x] `pages/Marketing/Legal/Cookies.vue` — Cookie policy with consent modal spec
- [x] `pages/Marketing/Legal/Dpa.vue` — Data Processing Agreement
- [x] `pages/Marketing/Legal/Aup.vue` — Acceptable Use Policy
- [x] `pages/Marketing/Security.vue` — Security page (at /security)

---

## Tests (`tests/Feature/Marketing/` + `tests/Unit/Models/Marketing/`)
- [x] `MarketingRoutesTest.php` — smoke tests all 19 GET routes return 200
- [x] `DemoControllerTest.php` — 11 tests: valid submission, UTM capture, validation failures, honeypot
- [x] `ContactControllerTest.php` — 9 tests: valid submission, validation failures
- [x] `DemoRequestTest.php` — unit: getFullNameAttribute, array cast, ULID
- [x] `BlogPostTest.php` — unit: scopePublished (4 scenarios), reading_time auto-calc
- [x] `FaqEntryTest.php` — unit: scopeForContext (3 scenarios)
- [x] `OpenRoleTest.php` — unit: scopeOpen (3 scenarios)
- [x] `ChangelogEntryTest.php` — unit: scopePublished (3 scenarios)
- **35 tests, 35 passing**

## Seeders (`database/seeders/Marketing/`)
- [x] `BlogCategorySeeder` — 4 categories
- [x] `BlogPostSeeder` — 6 posts (4 published, 2 draft) with full body + reading_time
- [x] `TestimonialSeeder` — 4 testimonials (2 featured)
- [x] `FaqEntrySeeder` — 8 entries (4 general, 2 pricing, 2 security)
- [x] `TeamMemberSeeder` — 3 members (CEO, CTO, Head of CS)
- [x] `OpenRoleSeeder` — 2 open roles (Senior Full-Stack, Customer Success Manager)
- [x] `ChangelogEntrySeeder` — 5 entries spanning 2025–2026
- [x] `HelpCategorySeeder` — 3 categories
- [x] `HelpArticleSeeder` — 6 articles (2 per category)
- [x] `DemoRequestSeeder` — 5 requests (all statuses)
- [x] `NewsletterSubscriberSeeder` — 8 subscribers
- [x] `ContactSubmissionSeeder` — 3 submissions
- [x] `MarketingSeeder` (root) — orchestrates all sub-seeders
- Run with: `php artisan db:seed --class=MarketingSeeder`

## Pending (post-implementation)
- [x] Run `php artisan migrate` to apply all marketing migrations
- [x] Run `php artisan optimize:clear` to clear compiled routes
- [x] Run `npm run build` — ✓ built in 2.47s, 2952 modules, 0 errors
- [x] Run `php artisan db:seed --class=MarketingSeeder` — all 12 seeders clean
- [ ] Test `/demo` form end-to-end in browser (submit → DemoRequest in admin panel)
- [ ] Test `/contact` form end-to-end in browser
- [ ] Check `/admin` for Marketing & Content nav group with all 12 resources
- [ ] Implement cookie consent modal (spec'd in Legal & Compliance Pages note)

## UI/UX Redesign (Phase 2 Frontend — complete)
- [x] Welcome.vue — full redesign: deep-space hero (`#050E1A` base), product chrome mockup, tools-replaced bar, 2-col problem section, 3-row feature showcase with visual cards, expanded module grid (descriptions + hover glow), testimonials section (3 quote cards), improved how-it-works timeline, dark pricing teaser (Pro card as solid ocean gradient), dark final CTA
- [x] MarketingNav — FlowFlex logo mark SVG added (flow-line icon in ocean-500 rounded square)
- [x] All inner page heroes — unique dark branded backgrounds, eyebrow badges, `font-black` tracking-tighter headings (Features, Pricing, About, Demo, Contact, Blog, Changelog, Careers, Security)
- [x] Pricing.vue — hero updated to match brand system
- [x] Features.vue — hero with floating module icons in background; domain cards "Explore" button opens modal (was broken `<Link href="/features">` reloading page). Modal shows: domain header with gradient, all 6 modules with name + description, CTA button per domain. Escape + click-outside to close. `Teleport to="body"`, scroll-lock on open.
- [x] i18n — added missing keys: `problem.label`, `problem.cta`, `modules.label`, `howItWorks.label`, `pricing.label`, `features.showcaseHeading`, `toolsReplaced`, `testimonials`, `cta.or`
- Build: ✓ 2952 modules, 2.51s

## Bug Fixes & Mobile Pass (Phase 3 — complete)
- [x] Login button — changed `<Link href="/login">` → `<a href="/workspace/login">` in both desktop + mobile nav; was opening Inertia sub-modal instead of redirecting to workspace panel
- [x] Blog menu item — fixed `Inertia::render('Marketing/Blog')` → `'Marketing/Blog/Index'` and `'Marketing/BlogPost'` → `'Marketing/Blog/Post'` in MarketingController; pages are at `Blog/Index.vue` and `Blog/Post.vue`
- [x] Feature showcase frames — replaced identical bar-chart skeletons with 3 distinct UI mockups: (1) module toggle panel with ON/OFF switches, (2) data flow diagram showing FlowFlex core connected to satellite modules, (3) GDPR compliance dashboard with progress bars + compliance badge grid
- [x] Feature showcase alternating layout — replaced `lg:flex lg:flex-row-reverse` with `lg:order-1/2` on children to avoid flex/grid collision
- [x] Tools replaced section — colours Jira `#0052CC` + Slack `#4A154B` were invisible on dark bg; redesigned to white/50 text + small brand-colour square dot per tool; added Moneybird + Trello; centred layout
- [x] Mobile responsiveness — hero h1 reduced from `text-6xl` base to `text-4xl sm:text-6xl md:text-7xl lg:text-8xl`; all inner page heroes reduced from `text-6xl` to `text-4xl sm:text-6xl`; product chrome sidebar hidden on xs; dashboard mockup uses flex instead of fixed grid
- Build: ✓ 2958 modules, 2.85s

## Bug Fixes — Round 2 (Phase 3b — complete)
- [x] Changelog broken — `->paginate(20)` returned LengthAwarePaginator (object); Vue typed prop as `Array<{...}>` so `v-for="entry in entries"` iterated nothing. Fixed: changed to `->get()` in MarketingController
- [x] Status page replaced — was a blank static page. Now uses `spatie/laravel-health` (installed v1.39). `StatusController` injects `ResultStore`, fetches `latestResults()`, passes checks + overall status to `Status.vue`
- [x] Health checks registered: `DatabaseCheck`, `CacheCheck`, `UsedDiskSpaceCheck` (warn >70%, fail >90%). Redis check added conditionally when PHP `redis` extension is loaded (not available in local dev)
- [x] Cron job: `Schedule::command('health:check')->everyFiveMinutes()` added to `routes/console.php`
- [x] Eyebrow badges removed — all `<p class="inline-flex...bg-ocean-500/10...rounded-full">` hero badges removed from: Welcome.vue (hero), About.vue, Pricing.vue, Features.vue, Demo.vue, Contact.vue, Careers.vue, Changelog.vue, Blog/Index.vue
- [x] Section eyebrow labels removed from Welcome.vue — `problem.label`, `features.label`, `modules.label`, `testimonials.label`, `howItWorks.label`, `pricing.label` all removed (headings speak for themselves)
- [x] `MarketingStatsWidget` — fixed `protected static ?string $heading` → `protected ?string $heading` (PHP 8.1 cannot redeclare non-static property as static)
- Build: ✓ 0 errors, 2.75s

## Phase 4 Backlog
- [ ] Individual module pages (`/modules/{domain}/{module}`)
- [ ] Comparison pages (`/compare/vs-{competitor}`)
- [ ] Sitemap.xml auto-generation
- [ ] OG image auto-generation for blog posts
- [ ] Connect newsletter to email provider (Mailgun/Postmark)
- [ ] Help centre full-text search (Scout + Meilisearch) at `/help`

## Routes Reference

```
GET  /                  → Welcome page (homepage)
GET  /pricing           → Marketing/Pricing
GET  /features          → Marketing/Features
GET  /about             → Marketing/About
GET  /demo              → Marketing/Demo
POST /demo              → DemoController@store
GET  /contact           → Marketing/Contact
POST /contact           → ContactController@store
GET  /blog              → Marketing/Blog/Index
GET  /blog/{slug}       → Marketing/Blog/Post
GET  /changelog         → Marketing/Changelog
GET  /careers           → Marketing/Careers
GET  /security          → Marketing/Security
GET  /legal/privacy     → Marketing/Legal/Privacy
GET  /legal/terms       → Marketing/Legal/Terms
GET  /legal/cookies     → Marketing/Legal/Cookies
GET  /legal/dpa         → Marketing/Legal/Dpa
GET  /legal/aup         → Marketing/Legal/Aup

REDIRECTS:
/register → /demo
/signup   → /demo
/trial    → /demo
/login    → /workspace/login
```

## Related
- [[Marketing Site Overview]]
- [[Admin Panel CMS]]
- [[Demo Request Flow]]
- [[Blog & Content Strategy]]
- [[Legal & Compliance Pages]]
