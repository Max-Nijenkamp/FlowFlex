---
tags: [flowflex, domain/projects, knowledge-base, wiki, phase/5]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-06
---

# Knowledge Base & Wiki

The internal brain of the company. SOPs, runbooks, handbooks, and how-tos — searchable, versioned, and always current.

**Who uses it:** All employees
**Filament Panel:** `projects`
**Depends on:** Core
**Phase:** 5
**Build complexity:** High — 2 resources, 1 page, 4 tables

## Features

- **Rich block editor** — headings, paragraphs, bullet lists, numbered lists, tables, code blocks, callouts, image embeds, file embeds, dividers
- **Nested pages** — unlimited depth (pages inside pages)
- **Category and tag system**
- **Full-text search** across all articles
- **Article templates** — SOP, runbook, meeting notes, decision record
- **Change history per article** — who changed what, when — revert to any version
- **Contributor tracking** — who wrote/edited each article
- **Comments and inline suggestions** on articles
- **Article status** — draft / published / archived / needs review
- **Public articles** — embed in customer-facing portal or external knowledge base
- **Article feedback** — "Was this helpful? Yes / No"
- **Reading time estimate**
- **Related articles suggestions**
- **Article ownership and review schedule** — "this article should be reviewed every 6 months"

## Article Templates Built-in

| Template | Use case |
|---|---|
| SOP (Standard Operating Procedure) | Step-by-step process documentation |
| Runbook | Operational playbooks for repeatable scenarios |
| Meeting notes | Structured meeting record with action items |
| Decision record | Document a decision, rationale, alternatives considered |
| Team handbook | Onboarding and team culture documentation |

## Database Tables (4)

1. `kb_articles` — article content and metadata
2. `kb_categories` — category/folder structure
3. `kb_article_versions` — version history per article
4. `kb_article_feedback` — helpful / not helpful feedback

## Related

- [[Projects Overview]]
- [[Document Management]]
- [[Team Collaboration]]
- [[HR Compliance]]
- [[Internal IT Helpdesk]]
