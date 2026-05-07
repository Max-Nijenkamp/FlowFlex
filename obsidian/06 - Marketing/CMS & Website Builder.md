---
tags: [flowflex, domain/marketing, cms, website, phase/5]
domain: Marketing & Content
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# CMS & Website Builder

Block-based CMS for marketing websites. Includes blog, SEO, media library, redirect manager, scheduled publishing, and a headless API for decoupled frontends.

**Who uses it:** Marketing team, content editors
**Filament Panel:** `marketing`
**Depends on:** Core, [[File Storage]]
**Phase:** 5
**Build complexity:** Very High — 3 resources, 2 pages, 6 tables

---

## Features

- **Block-based page builder** — visual drag-and-drop section composer; blocks: hero, text, image, CTA, columns, feature grid, testimonial, FAQ, form embed
- **Blog / news** — article management with categories, tags, author bio, estimated read time
- **SEO meta fields** — title, meta description, canonical URL, OG image, OG type per page/post
- **Media library** — centralised image and file management; auto-WebP conversion, CDN delivery via S3
- **Multi-language (i18n)** — content translated per locale; language switcher in frontend
- **Scheduled publishing** — set a future publish date/time; draft → published auto-transition
- **Redirect manager** — manage 301/302 redirects; import from CSV; redirect chain detection
- **Template library** — save pages as reusable templates; apply to new pages
- **A/B test pages** — split traffic 50/50 between two page variants; winner declared by conversion rate
- **Headless CMS API** — REST endpoints at `/api/v1/cms/pages`, `/api/v1/cms/posts`; used by decoupled React/Next.js frontends
- **Content versioning** — every save creates a version; rollback to any prior version
- **Sitemap auto-generation** — `/sitemap.xml` updates on publish/unpublish
- **Robots.txt management** — editable from admin panel

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `cms_pages`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `slug` | string unique (per company + locale) | URL path |
| `locale` | string(5) | default "en" |
| `status` | enum | `draft`, `published`, `scheduled`, `archived` |
| `publish_at` | timestamp nullable | scheduled publish time |
| `template_id` | ulid FK nullable | → cms_templates |
| `meta_title` | string nullable | SEO title |
| `meta_description` | text nullable | |
| `og_image_file_id` | ulid FK nullable | → files |
| `canonical_url` | string nullable | |
| `blocks` | json | ordered array of block definitions |
| `is_homepage` | boolean | default false |
| `ab_variant_of_id` | ulid FK nullable | → cms_pages (parent of A/B test) |
| `ab_traffic_split` | integer nullable | 0–100 % sent to this variant |

### `cms_posts`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `slug` | string unique (per company + locale) | |
| `locale` | string(5) | default "en" |
| `author_tenant_id` | ulid FK nullable | → tenants |
| `status` | enum | `draft`, `published`, `scheduled` |
| `publish_at` | timestamp nullable | |
| `excerpt` | text nullable | |
| `body` | longtext | markdown |
| `body_html` | longtext | rendered |
| `featured_image_file_id` | ulid FK nullable | → files |
| `meta_title` | string nullable | |
| `meta_description` | text nullable | |
| `estimated_read_minutes` | integer nullable | |

### `cms_categories`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string unique (per company) | |
| `description` | text nullable | |
| `parent_id` | ulid FK nullable | self-referential |

### `cms_tags`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string unique (per company) | |

### `cms_redirects`
| Column | Type | Notes |
|---|---|---|
| `from_path` | string | |
| `to_path` | string | |
| `status_code` | integer | 301 or 302 |
| `is_active` | boolean | default true |
| `note` | string nullable | |

### `cms_media`
| Column | Type | Notes |
|---|---|---|
| `file_id` | ulid FK | → files |
| `alt_text` | string nullable | |
| `caption` | text nullable | |
| `folder` | string nullable | virtual folder path |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `PagePublished` | `cms_page_id` | Sitemap regeneration job |
| `PostPublished` | `cms_post_id` | Sitemap regeneration, social auto-share (if configured) |

---

## Permissions

```
marketing.content.view
marketing.content.create
marketing.content.edit
marketing.content.delete
marketing.content.publish
marketing.media.view
marketing.media.upload
marketing.media.delete
marketing.redirects.view
marketing.redirects.manage
```

---

## Related

- [[Marketing Overview]]
- [[SEO & Analytics]]
- [[Email Marketing]]
- [[File Storage]]
