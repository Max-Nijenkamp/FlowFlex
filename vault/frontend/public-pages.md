---
type: frontend
category: public-pages
color: "#FBBF24"
---

# Public Pages

Additional Vue + Inertia pages that are publicly accessible or accessible to external users of a specific tenant. These pages are not part of the core marketing site and are activated conditionally based on which modules a tenant has enabled.

---

## Storefront & Checkout

Activated when a tenant enables the E-commerce module.

### Storefront (`/store`)

- Product grid with category filter and search
- Product detail page: image gallery, full description, variant selector (size/colour), customer reviews, real-time stock indicator
- Persistent shopping cart (localStorage + server session sync)
- Wishlist (requires a customer account)

### Checkout Flow (`/store/checkout`)

Steps:

1. Review cart items
2. Enter email address (guest checkout or login with existing account)
3. Shipping address entry with address autocomplete (Google Places or Loqate)
4. Shipping method selection (rates fetched from shipping provider)
5. Payment entry via Stripe Elements (card stays on page, no redirect)
6. Order confirmation page

On confirmation: order created in E-commerce module, confirmation email dispatched, inventory decremented.

---

## Booking & Appointment Scheduling

Activated when a tenant enables the Booking module.

### Service Listing (`/book`)

- All available services with duration, price, and short description
- Provider selector if the tenant has multiple bookable staff
- Real-time calendar availability picker (slots checked server-side)
- Confirmation page + email reminder + SMS reminder (if configured)

### Booking Flow (`/book/:service`)

1. Select date and time slot from available calendar
2. Customer details: name, email, phone number
3. Optional intake questions configured per service (free text or multiple choice)
4. Payment (if the service requires upfront payment via Stripe)
5. Confirmation page with calendar invite download (.ics file)

---

## Learner Portal

Activated when a tenant enables the LMS module with the external learner option.

### Routes

| Route | Page |
|---|---|
| `/learn` | Public course catalogue |
| `/learn/:course` | Course detail with description, syllabus, and enrol CTA |
| `/learn/my-courses` | Enrolled learner's course progress dashboard |
| `/learn/course/:id/lesson/:id` | Lesson player (video player + quiz) |
| `/learn/certificates` | Earned certificates with PDF download |

### Notes

- White-labeled per tenant company (logo, primary colour)
- External learners use a dedicated `learner_users` table and `learner` auth guard
- Lesson video served from S3 via Cloudflare CDN or embedded from YouTube/Vimeo
- Certificates generated server-side as PDFs with tenant branding

---

## Community Pages

Activated when a tenant enables the Community module with public or member-only access.

### Routes

| Route | Page |
|---|---|
| `/community` | Homepage: featured posts, upcoming events, member spotlight |
| `/community/forums` | Forum topic list |
| `/community/forums/:topic/:post` | Post thread with replies |
| `/community/members` | Member directory (searchable by skills and location) |
| `/community/members/:username` | Public member profile |
| `/community/events` | Event listing |
| `/community/events/:slug` | Event detail with registration form |

### Access

Controlled by the `community_public_access` company setting:

- `public` — anyone can browse without an account; posting requires registration
- `members-only` — all pages require authentication with a community account

---

## Public Org Chart

Activated when a tenant enables the Public Org Chart setting in HR.

### Routes

| Route | Page |
|---|---|
| `/org` | Filterable org chart by department and location |
| `/org/:username` | Individual employee card (name, title, department, LinkedIn if opted in) |

### Privacy

- No contact information (email, phone) exposed unless the employee explicitly opts in via their HR profile
- Photos only shown if the employee has a public avatar set

---

## Shared Technical Decisions

| Concern | Solution |
|---|---|
| Auth guards | Separate guard per portal type: `portal`, `learner`, `community` |
| Branding | Per-company primary colour and logo injected via Inertia shared data as CSS variables |
| Meta / SEO | Inertia `<Head>` per page for title, description, OG, and canonical |
| Performance | Vite code-splitting: each section (`store`, `book`, `learn`, `community`, `org`) is its own async chunk |
| Analytics | Events sent to GTM dataLayer or directly to the Analytics domain via API |
| Images | S3 + Cloudflare CDN; responsive `srcset` via Inertia image helpers |

---

## Related

- [[frontend/INDEX]] — frontend section overview
- [[frontend/marketing-site]] — public marketing site
- [[frontend/client-portal]] — authenticated client portal
- [[domains/ecommerce/INDEX]] — storefront data source
- [[domains/lms/INDEX]] — learner portal data source
- [[domains/community/INDEX]] — community pages data source
- [[domains/hr/INDEX]] — public org chart data source
