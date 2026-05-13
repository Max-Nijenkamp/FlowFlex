---
type: frontend
category: marketing-site
color: "#FBBF24"
---

# Marketing Site

FlowFlex's public-facing website. Unauthenticated, SEO-optimised, managed via the CMS module in the `/marketing` Filament panel. Built with Vue 3 + Inertia.js and Tailwind CSS v4. No Filament UI is used on this site.

---

## Routes

| Route | Page |
|---|---|
| `/` | Homepage |
| `/features/:domain` | Domain feature pages (dynamic) |
| `/pricing` | Pricing comparison |
| `/blog` | Blog listing |
| `/blog/:slug` | Individual article |
| `/demo` | Demo request form |
| `/about` | Company about page |
| `/legal/:page` | Legal pages (privacy, terms, DPA) |
| `/login` | Tenant login redirect |
| `/register` | Sign-up flow |

---

## Homepage

### Hero

- H1: "The AI-native platform that runs your entire business"
- H2: "Replace 12 apps with one workspace. Self-serve in 15 minutes."
- Primary CTA: "Start free trial" — routes to `/register`
- Secondary CTA: "Watch 3-min demo" — opens inline video modal
- Social proof bar below hero: customer logos + "Trusted by X+ teams"

### Differentiators Bar

| Icon | Label |
|---|---|
| lightning | Self-serve setup — no consultant needed |
| robot | AI built into every module |
| lock | GDPR-ready, EU data residency |
| euro | One subscription, up to 15 active domains |

### Features Grid

6-column responsive grid of domain cards. Each card: icon, domain name, 2–3 representative module names. Domains shown: HR, Finance, CRM, Marketing, Operations, Projects, Analytics, IT, Legal, E-commerce, Communications, LMS, AI & Automation, Community.

### Comparison Table

| | FlowFlex | Legacy ERP (SAP/Oracle) | SaaS Stack (12 tools) |
|---|---|---|---|
| Setup time | 15 minutes | 3–18 months | 2–4 weeks |
| Implementation cost | €0 | €50k–€500k | €5k–€20k |
| Monthly cost | from €149 | €2,000+/user | €1,500+/company |
| AI built-in | Yes — every module | No — add-on | No — separate tool |
| Self-serve | Yes | No — consultant needed | Yes per app |
| Single data model | Yes | Yes | No — fragmented |

### Social Proof

- 3–5 customer testimonials (pull quotes, name, role, company logo)
- Case study highlights with ROI numbers
- G2 and Capterra review summary badges

### AI Capability Cards

Four cards in a 2×2 grid:

1. **AI Content Studio** — generate on-brand content across all channels
2. **AI Sales Coach** — coach reps, forecast deals, identify at-risk pipeline
3. **AI Insights Engine** — ask your data anything in plain English
4. **AI Agents** — automate recurring workflows, no code required

### Pricing Preview

3-plan summary card row with "See full pricing" CTA linking to `/pricing`.

---

## Pricing Page

- Monthly / Annual billing toggle (annual = 20% saving)
- Full feature comparison table across all plans
- Explanation of module activation model (enable only what you need)
- FAQ accordion (common questions about billing, contracts, data)
- Enterprise CTA (custom quote form)

### Plans

| Plan | Price | Users | Domains |
|---|---|---|---|
| Starter | €49/mo | Up to 5 | 3 active |
| Growth | €149/mo | Up to 25 | 10 active |
| Scale | €399/mo | Up to 100 | All 15 |
| Enterprise | Custom | Unlimited | All + custom |

---

## Feature Pages (`/features/:domain`)

One page per domain, dynamically rendered from a shared template:

1. **Hero** — domain name + single value proposition sentence
2. **Feature list** — icons + short descriptions of key modules
3. **Screenshot / demo embed** — product screenshot or guided demo video
4. **Comparison table** — FlowFlex vs the dominant market-leader tool for that domain
5. **Adjacent features** — cards linking to related domains (e.g. CRM page surfaces Finance and Projects)
6. **CTA** — "Start free trial" button

---

## Blog

- `/blog` — listing page with category filter tabs and search input
- `/blog/:slug` — article with table of contents sidebar, author card, estimated read time, related articles footer
- Categories: Product Updates, Guides, Comparisons, Use Cases
- SEO: canonical tags, Open Graph meta, JSON-LD Article schema per post

---

## Demo Request Flow

Multi-step form at `/demo`:

1. Company size (dropdown) + industry (dropdown)
2. Pain points — checkbox grid of currently-used tools
3. Contact details — name, work email, company name
4. Schedule — Calendly embed or native time-slot picker

On submit: fires `DemoRequestReceived` event → CRM module (creates a lead) + Notification (assigns to sales team member).

---

## Technical Details

- **Content management:** Homepage hero, testimonials, comparison table, and blog posts are all editable via the CMS & Website Builder module in the `/marketing` Filament panel
- **Images:** Served from S3 via Cloudflare CDN
- **Per-page meta:** Inertia `<Head>` component for title, description, OG, and canonical
- **Brand colours:** FlowFlex brand palette (not Filament panel theme colours)
- **Build:** Vite with code-splitting; marketing site is its own entry point

---

## Related

- [[frontend/INDEX]] — frontend section overview
- [[frontend/client-portal]] — authenticated external portal
- [[frontend/public-pages]] — storefront, booking, learner, community pages
- [[domains/marketing/INDEX]] — CMS module that manages this site's content
