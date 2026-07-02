---
domain: support
module: knowledge-base
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Knowledge Base

Self-service article library. Internal agents reference articles; customers browse a public help centre. Reduces ticket volume by deflecting common questions.

---

## Module-key

`support.kb`

**Priority:** p2  
**Panel:** support  
**Permission prefix:** `support.kb`  
**Tables:** `sup_kb_articles`, `sup_kb_categories`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../tickets/_module\|support.tickets]] | article suggestions in the reply composer; standalone otherwise |

---

## Core Features

- Article record: title, slug, body (rich text), category, status (draft/published), author
- Categories and sub-categories for organisation
- Rich text via `awcodes/filament-tiptap-editor` — purified before storage ([[../../../architecture/security]])
- Slugs via `spatie/laravel-sluggable`
- Public help centre: published articles browsable at a public URL (Vue + Inertia) — company resolved from help-centre slug *(assumed: `/help/{company-slug}`)*
- Article search via Meilisearch (public search limited to published)
- Article feedback: "Was this helpful?" thumbs up/down with counts (rate-limited per visitor)
- View-count tracking per article
- Suggest relevant articles to agents while replying to a ticket (search on ticket subject)
- Article versioning: previous bodies kept (jsonb history *(assumed: last 20 revisions)*)

See [[./features/article-authoring|Article Authoring]] and [[./features/public-help-centre|Public Help Centre]] features.

---

## Build Manifest

```
database/migrations/xxxx_create_sup_kb_categories_table.php
database/migrations/xxxx_create_sup_kb_articles_table.php
app/Models/Support/{KbArticle,KbCategory}.php
app/Data/Support/{CreateArticleData,FeedbackData}.php
app/Services/Support/KbService.php
app/Actions/Support/{RecordArticleViewAction,RecordFeedbackAction}.php
app/Http/Controllers/HelpCentreController.php + resources/js/Pages/Help/{Index,Category,Article}.vue
app/Filament/Support/Resources/{KbArticleResource,KbCategoryResource}.php
database/factories/Support/{KbArticleFactory,KbCategoryFactory}.php
tests/Feature/Support/{KbArticleTest,HelpCentreTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Public help centre serves published-only, right company
- [ ] Draft invisible publicly + in public search
- [ ] Body purified (XSS fixture)
- [ ] Revisions appended on edit, capped
- [ ] Feedback rate-limited; counts increment
- [ ] Agent suggestions return relevant published articles

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `KbService::suggestFor` | support.tickets (soft) | article suggestions in reply composer |
| Public | help centre + feedback | unauthenticated visitors | published-only, rate-limited |

**Data ownership:** `support.kb` writes only `sup_kb_articles`, `sup_kb_categories`; public feedback/views write its own counters; no cross-domain writes ([[../../../security/data-ownership]]).

---

## Related

- [[../tickets/_module|support.tickets]]
- [[../../../architecture/search]]
- [[../../../frontend/_index]]
