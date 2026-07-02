---
domain: customer-success
module: nps
feature: public-collector
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Public Collector

The unauthenticated, token-scoped page where a recipient submits their 0–10 score and optional comment.

## Behaviour

- A recipient clicks a score button in the survey email → lands on `/nps/{token}`. The email button may pre-fill the score; the page confirms and captures an optional comment.
- `RecordNpsResponseAction` validates the token (exists + unanswered), stores `score`, computes `category` (promoter 9–10 / passive 7–8 / detractor 0–6), stores `comment`, sets `responded_at`.
- Single-use: a token already answered renders an "already responded / thank you" state — no overwrite.
- A **detractor** response raises a CSM alert via `core.notifications`.
- Tenant + recipient are resolved entirely from the token — no login.

## UI

- **Kind**: public-vue — `resources/js/Pages/Nps/Respond.vue` via `NpsResponseController` (Vue + Inertia).
- **Page**: `/nps/{token}` (public, no panel guard).
- **Layout**: branded single-card page — the question, a 0–10 scale selector, an optional comment box, a submit button; minimal, mobile-first (survey emails open on mobile).
- **Key interactions**: pick/confirm score → optional comment → submit (POST) → thank-you state. Score buttons in email deep-link with the value pre-selected.
- **States**: empty/default (score not yet chosen; submit disabled) · loading (submitting spinner) · error (invalid/expired token → friendly "link no longer valid"; rate-limited → gentle retry) · answered (already-responded thank-you). 
- **Gating**: none (public) — access is the token itself; single-use + rate-limited at the controller boundary ([[../security]]).

## Data

- Owns / writes: `cs_nps_responses` (score, category, comment, responded_at on the token's row).
- Reads: nothing cross-domain at response time (the recipient/account are already on the row from send).
- Cross-domain writes: none — the detractor alert dispatches via `core.notifications` (its listener writes its own tables) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: recipient rows from [[./survey-send|Survey Send]].
- Feeds: `core.notifications` (detractor alert); the stored response feeds [[./sentiment-scoring|Sentiment Scoring]] and `cs.health`.
- Shared entity: none written; the pre-stored `contact_id`/`account_id` are read-only refs.

## Unknowns

- Token expiry is by single-use, not time *(assumed)* — [[../unknowns]].
- Anonymous-mode responses not modelled v1.

## Related

- [[../_module|NPS]] · [[./survey-send|Survey Send]] · [[../security|Security]]
- [[../../../../architecture/ui-strategy]] · [[../../../../security/data-ownership]]
