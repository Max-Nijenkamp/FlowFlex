---
tags: [flowflex, admin, cms, content-management, marketing]
domain: Marketing Site
panel: admin
status: planned
last_updated: 2026-05-07
---

# Admin Panel CMS

Everything that controls the marketing site from the inside. FlowFlex super-admins manage all public-facing content here — no code deploys for copy changes.

**Panel:** `/admin`
**Navigation group:** Marketing & Content

---

## Resources Overview

| Resource | Route | Purpose |
|---|---|---|
| Blog Posts | `/admin/blog/posts` | Create, edit, publish articles |
| Blog Categories | `/admin/blog/categories` | Manage blog category taxonomy |
| Testimonials | `/admin/testimonials` | Customer quotes shown on homepage/pricing |
| Demo Requests | `/admin/demo-requests` | Inbound lead management |
| Newsletter Subscribers | `/admin/newsletter` | Email list management |
| FAQ Entries | `/admin/faqs` | FAQ accordion content (pricing + module pages) |
| Team Members | `/admin/team` | About page team profiles |
| Open Roles | `/admin/careers` | Job listings on /careers |
| Changelog Entries | `/admin/changelog` | Product update posts |
| Help Articles | `/admin/help/articles` | Help centre documentation |
| Help Categories | `/admin/help/categories` | Help centre categories |
| Contact Submissions | `/admin/contact` | General contact form submissions |
| Partner Applications | `/admin/partners` | Partner programme applications |
| Module Content | `/admin/modules` | Edit marketing copy for module pages |
| Page SEO | `/admin/page-seo` | SEO metadata overrides per page |
| Pricing Config | `/admin/pricing` | Plan display prices and feature table |

---

## Blog Posts Resource

### Fields

| Field | Type | Notes |
|---|---|---|
| title | Text | Required. Auto-generates slug. |
| slug | Text | URL-safe, unique. Auto-generated, editable. |
| category | BelongsTo: BlogCategory | Required |
| tags | Tags input | Comma-separated |
| excerpt | Textarea | Used as meta description + listing card text (max 155 chars) |
| featured_image | File (image) | OG image if no custom one provided |
| body | Rich text (Filament Tiptap or Markdown) | Main content |
| author | BelongsTo: User (admin user) | Auto-fills to current user |
| status | Select: `draft` · `scheduled` · `published` | |
| published_at | DateTime | Required if scheduled/published |
| seo_title | Text | Custom `<title>`. Falls back to post title. |
| seo_description | Text | Custom meta description. Falls back to excerpt. |
| seo_noindex | Toggle | Prevents indexing (drafts, test posts) |
| og_image | File (image) | Custom OG image. Falls back to featured_image. |
| reading_time | Integer | Auto-calculated on save (words ÷ 200) |
| related_posts | HasMany: Post | Manual selection of 3 related posts |
| cta_type | Select: `demo` · `module` · `pricing` · `none` | Which CTA to show at end of post |
| cta_module | BelongsTo: Module (if cta_type = module) | Links to the relevant module page |

### Table View Columns

status badge · title · category · author · published_at · views (from GA4 — Phase 2)

### Filters

status · category · author · date range

### Actions

- Publish (draft → published)
- Preview (opens `/blog/{slug}?preview=1` — shows post without indexing)
- Duplicate
- Delete (soft delete)

---

## Testimonials Resource

### Fields

| Field | Type | Notes |
|---|---|---|
| name | Text | Person's full name |
| role | Text | e.g. "Head of Operations" |
| company | Text | Company name |
| quote | Textarea | Max 200 chars for display |
| photo | File (image) | Square crop, min 200×200 |
| is_featured | Toggle | Show on homepage testimonials carousel |
| display_order | Integer | Sort order |
| published | Toggle | |

---

## Demo Requests Resource

### Fields (see also [[Demo Request Flow]])

| Field | Type | Editable |
|---|---|---|
| first_name, last_name | Text | No |
| email | Email | No |
| company_name | Text | No |
| company_size | Text | No |
| modules_interested | JSON | No |
| heard_from | Text | No |
| notes | Textarea | No (prospect input) |
| phone | Text | No |
| utm_source/medium/campaign | Text | No |
| ip_address | Text | No |
| status | Select | Yes |
| assigned_to | BelongsTo: User | Yes |
| scheduled_at | DateTime | Yes |
| notes_internal | Textarea | Yes (internal team notes) |

### Table View

status badge · name · company · company_size · modules (chips) · heard_from · created_at · assigned_to

### Filters

status · company_size · assigned_to · date range · heard_from

### Actions

- Mark contacted
- Schedule demo (sets scheduled_at, sends confirmation email)
- Convert to tenant (opens new company wizard, pre-fills data)
- Mark lost (with reason: price · fit · no response · chose competitor)
- Send follow-up email

### Status Pipeline

```
new → contacted → demo_scheduled → demo_done → converted
                                              → lost
```

### Notifications

- New demo request: email + in-app notification to all admin users
- 48h with status `new`, no action: escalation notification
- Demo in 24h: reminder email to prospect

---

## Newsletter Subscribers Resource

### Fields

| Field | Type |
|---|---|
| email | Email |
| status | `subscribed` · `unsubscribed` · `bounced` |
| source | Where they signed up (blog post slug, homepage, etc.) |
| subscribed_at | DateTime |
| unsubscribed_at | DateTime (nullable) |
| double_opt_in_confirmed | Boolean |
| double_opt_in_sent_at | DateTime |

### Bulk Actions

- Export CSV (for import into email tool)
- Mark as unsubscribed
- Delete

### Email Tool Integration

Newsletter is sent via Mailgun/Postmark or a dedicated newsletter tool (Buttondown, Loops, or similar). Subscriber list syncs from this resource via API or webhook.

Do not store newsletter content in the admin panel — use the email tool for campaign management.

---

## FAQ Entries Resource

### Fields

| Field | Type |
|---|---|
| question | Text |
| answer | Textarea (supports Markdown) |
| context | Select: `pricing` · `general` · `module/{slug}` |
| display_order | Integer |
| is_published | Toggle |

Context determines where the FAQ appears. Module FAQ entries appear on the relevant module page.

---

## Team Members Resource

### Fields

| Field | Type |
|---|---|
| name | Text |
| role | Text |
| bio | Textarea (max 150 chars) |
| photo | File (image) — square, professional |
| linkedin_url | URL |
| twitter_url | URL (optional) |
| display_order | Integer |
| is_published | Toggle |

---

## Open Roles Resource

### Fields

| Field | Type |
|---|---|
| title | Text |
| slug | Auto-generated |
| department | Select |
| location | Text (e.g. "Remote — Europe") |
| type | Select: `full-time` · `part-time` · `contract` |
| salary_range | Text (optional — e.g. "€60,000–€80,000") |
| about_role | Textarea |
| responsibilities | Textarea (supports Markdown bullet list) |
| requirements | Textarea |
| nice_to_have | Textarea (optional) |
| how_to_apply | Textarea |
| status | `open` · `closed` · `filled` |
| published_at | DateTime |

---

## Changelog Entries Resource

### Fields

| Field | Type |
|---|---|
| title | Text |
| type | Select: `feature` · `improvement` · `fix` · `infrastructure` |
| body | Rich text |
| screenshot | File (image, optional) |
| docs_url | URL (optional — links to help article) |
| published_at | DateTime |
| is_published | Toggle |

---

## Help Centre Resources

### Help Categories

- name
- slug
- description
- icon
- display_order
- parent_category (nullable — for nested categories)

### Help Articles

- title
- slug
- category (BelongsTo)
- body (Rich text / Markdown)
- last_reviewed_at
- is_published
- seo_title (override)
- seo_description (override)
- helpful_count / not_helpful_count (from thumbs up/down widget)
- related_articles (manual links)
- module_link (link to relevant marketing module page)

---

## Module Content Resource

Allows editing the marketing copy on each module page without a code deploy.

### Fields per Module

| Field | Description |
|---|---|
| module_id | Matches the URL slug (e.g. `hr-payroll`) |
| tagline | H1 hero line (e.g. "Pay your team, every time, on time") |
| intro | 2–3 sentence intro paragraph |
| problem_statement | The pain this module solves (2–3 sentences) |
| features | JSON array: `[{icon, name, description}]` — feature card grid |
| screenshots | Multiple image upload (up to 5) |
| comparison_table | JSON: `[{feature, flowflex, competitor_name, competitor_value}]` |
| connected_modules | JSON array of module slugs ("works with") |
| seo_title | Override |
| seo_description | Override |
| og_image | Override |
| is_published | Toggle |

---

## Page SEO Resource

Override SEO metadata for any static marketing page that doesn't have its own content model.

### Fields

| Field | Type |
|---|---|
| page_path | Text (e.g. `/features`, `/about`) |
| seo_title | Text |
| seo_description | Text |
| og_image | File |
| noindex | Toggle |
| canonical_url | URL (if different from page_path) |

---

## Pricing Config Resource

Prices and plan features are never hardcoded in Blade templates.

### Fields

| Field | Type |
|---|---|
| plan_id | `starter` · `pro` · `enterprise` |
| monthly_price_eur | Integer (cents) |
| annual_price_eur | Integer (cents per month equivalent) |
| currency | EUR (extensible to GBP etc.) |
| user_limit | Integer (null = unlimited) |
| module_limit | Integer (null = unlimited) |
| storage_gb | Integer (null = unlimited) |
| features | JSON array (displayed in comparison table) |

A scheduled job can pull prices from Stripe's API to keep this in sync.

---

## Contact Submissions Resource

### Fields

- name, email, subject, message
- status: `new` · `replied` · `closed`
- created_at

### Actions

- Mark replied
- Delete

---

## Admin Panel Navigation

Group all CMS resources under a single "Marketing & Content" navigation group in the admin panel:

```
Marketing & Content
├── Demo Requests        [notification badge: count of 'new']
├── Blog Posts
├── Blog Categories
├── Help Articles
├── Help Categories
├── FAQ Entries
├── Testimonials
├── Newsletter Subscribers
├── Changelog
├── Team Members
├── Open Roles
├── Contact Submissions
├── Module Content
├── Page SEO
└── Pricing Config
```

---

## Permissions

All CMS resources use the `web` guard (FlowFlex staff only). Permission naming:
- `marketing.blog.view`, `marketing.blog.create`, `marketing.blog.edit`, `marketing.blog.delete`
- `marketing.demo-requests.view`, `marketing.demo-requests.manage`
- etc.

## Related

- [[Admin Panel]]
- [[Blog & Content Strategy]]
- [[Demo Request Flow]]
- [[SEO Strategy]]
- [[Pricing Page]]
- [[Features & Modules Pages]]
