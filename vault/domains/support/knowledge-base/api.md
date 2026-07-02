---
domain: support
module: knowledge-base
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Knowledge Base — DTOs & API

## DTOs

### CreateArticleData (input)

| Field | Type | Validation |
|---|---|---|
| title | string | required, max:255 |
| body | string | required, purified rich text |
| category_id | string | exists in company |
| status | string | in `draft,published` |

### FeedbackData (public input)

`article_id`, `helpful` (bool) — rate-limited, no auth.

---

## Public / Portal Endpoints

Vue + Inertia help centre (guest guard, rate-limited):

| Route | Purpose |
|---|---|
| `GET /help/{company}` | Help-centre index (published articles, categories) |
| `GET /help/{company}/{category}/{slug}` | Article page + "was this helpful?" |
| `POST /help/{company}/{article}/feedback` | `RecordFeedbackAction` — rate-limited |
| `POST /help/{company}/{article}/view` | `RecordArticleViewAction` — rate-limited |

All public reads/writes filter `is_published` + `company_id`; drafts never leak. Company resolved from the help-centre slug *(assumed: `/help/{company-slug}`)*.
