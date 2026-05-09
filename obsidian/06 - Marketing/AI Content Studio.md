---
tags: [flowflex, domain/marketing, ai, content, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-08
---

# AI Content Studio

AI-powered content creation for every channel — blog posts, email campaigns, social media, ad copy, landing pages. Brief the AI, review the draft, publish from one place. No more bouncing between ChatGPT tabs and your CMS.

**Who uses it:** Marketing teams, content creators, social media managers
**Filament Panel:** `marketing`
**Depends on:** Core, [[CMS & Website Builder]], [[Email Marketing]], [[Social Media Management]], [[AI Infrastructure]]
**Phase:** 5

---

## Features

### Content Generation

- **Blog posts**: SEO-optimised articles from a title + brief (H1/H2 structure, meta description, target keyword density)
- **Email campaigns**: full email body or subject line variants for A/B test
- **Social captions**: platform-aware (LinkedIn professional, Instagram casual, X concise)
- **Ad copy**: headline + description pairs for Google Ads, Meta Ads
- **Landing page copy**: hero headline, subheadline, bullet benefits, CTA button text
- **Product descriptions**: Ecommerce catalogue descriptions from product specs
- **Newsletters**: weekly digest from recent blog posts + company updates

### Brief-to-Draft Flow

1. Select content type
2. Fill brief: topic, tone (professional/casual/playful), target audience, key points, keywords, word count
3. AI generates draft
4. Edit in rich text editor (same editor as CMS)
5. AI can regenerate specific sections on request
6. Publish direct to: CMS, Email campaign, Social post scheduler

### Brand Voice

- Define brand voice in settings: adjectives (e.g. "confident, jargon-free, human")
- Upload 3–5 example pieces of "ideal" content as style reference
- AI writes in your brand voice, not generic ChatGPT voice
- Voice consistency score shown per generated piece

### AI Rewriting Tools

- **Simplify**: reduce reading level to target grade
- **Expand**: add detail and examples
- **Shorten**: cut to key points
- **Repurpose**: convert blog → email, LinkedIn post, Twitter thread
- **Translate**: AI-translated + tone-adjusted versions (not just literal translation)
- **Localise**: adjust idiomatic language for NL, DE, FR audiences

### Content Calendar Integration

- Draft content directly inside Calendar slots in [[Social Media Management]]
- Batch generate: "Create 4 weeks of LinkedIn posts about [topic]" → fills calendar
- Approval workflow: writer → reviewer → approved → scheduled

### SEO Optimisation

- Target keyword suggestions (from integrated search data)
- Keyword density check on generated content
- Readability score (Flesch–Kincaid)
- Internal link suggestions from existing CMS content
- Meta title + description generator

### Plagiarism & Quality Checks

- Originality check (internal deduplication — not sending to external APIs)
- Fact disclaimer banner on AI-generated drafts until human-reviewed
- Brand guideline violations flagged (banned words list, required disclaimers)

---

## Database Tables (3)

### `marketing_content_briefs`
| Column | Type | Notes |
|---|---|---|
| `type` | enum | `blog`, `email`, `social`, `ad`, `landing_page`, `product_desc` |
| `topic` | string | |
| `tone` | string | |
| `target_audience` | string nullable | |
| `key_points` | json | string[] |
| `keywords` | json | string[] |
| `word_count_target` | integer nullable | |
| `output_document_id` | ulid FK nullable | → cms_documents or draft |

### `marketing_ai_generations`
| Column | Type | Notes |
|---|---|---|
| `brief_id` | ulid FK | |
| `prompt_hash` | string | dedup + audit |
| `model_used` | string | gpt-4o / claude-3.5-sonnet |
| `tokens_used` | integer | |
| `output` | text | raw generated content |
| `brand_voice_score` | decimal nullable | 0-1 |
| `created_by` | ulid FK | |

### `marketing_brand_voice_configs`
| Column | Type | Notes |
|---|---|---|
| `adjectives` | json | string[] |
| `example_file_ids` | json | ulid[] |
| `banned_words` | json | string[] |
| `required_disclaimers` | json | string[] |
| `updated_by` | ulid FK | |

---

## Permissions

```
marketing.ai-content.create
marketing.ai-content.publish
marketing.ai-content.manage-brand-voice
marketing.ai-content.view-usage
```

---

## Competitor Comparison

| Feature | FlowFlex | Jasper | Copy.ai | HubSpot AI |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€39+/mo) | ❌ (€49+/mo) | ❌ (€€€) |
| Brand voice training | ✅ | ✅ | ✅ | partial |
| Direct publish to CMS/email | ✅ | partial | ❌ | ✅ (HubSpot only) |
| SEO optimisation | ✅ | ✅ | partial | ✅ |
| Repurpose to multi-channel | ✅ | ✅ | ✅ | partial |
| NL/EU language support | ✅ | ✅ | ✅ | partial |

---

## Related

- [[Marketing Overview]]
- [[CMS & Website Builder]]
- [[Email Marketing]]
- [[Social Media Management]]
- [[AI Infrastructure]]
