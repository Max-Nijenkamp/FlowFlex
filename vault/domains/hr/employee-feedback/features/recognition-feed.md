---
domain: hr
module: employee-feedback
feature: recognition-feed
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Recognition Feed

## Purpose

Public praise wall visible to the team.

## Behavior

- `RecognitionFeedPage` — custom Filament page (ui-strategy row #3), polling every 60s *(interval unverified)*.
- Shows only public praise; constructive and coaching-note feedback never appear.
- Gates on `canAccess()` = `hr.feedback.view-any` + module active — see [[../security]].

## Tables & Permissions

- Table: reads `hr_feedback` where `visibility` is public (see [[../data-model]] feed index `(company_id, visibility, created_at)`)
- Permission: `hr.feedback.view-any` for the page gate — see [[../security]]

## UI

- **Kind**: custom-page (social recognition feed)
- **Page**: "Recognition" (`/hr/recognition`) — `RecognitionFeedPage`
- **Layout**: chronological public-praise wall (giver → recipient, message, tags), polling every 60s *(interval unverified)*; ui-strategy row #3
- **Key interactions**: team members read the feed; new public praise appears live via polling; constructive/coaching-note feedback never surfaces
- **States**: empty (no public praise yet → "No recognition yet") · loading (feed skeleton on first load) · error (poll failure falls back to last snapshot) · selected (a praise card)
- **Gating**: `canAccess()` = `hr.feedback.view-any` + module active

## Data

- Owns / writes: none (read-only view)
- Reads: `hr_feedback` where `visibility` is public (feed index `(company_id, visibility, created_at)`) — own module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none directly — reflects public praise created in [[feedback]]
- Feeds: none *(recognition-to-notifications feed is on the feedback record's create path, not this read page)*
- Shared entity: `hr_employees` (hr.profiles) for giver/recipient display

Follows [[../../../architecture/patterns/custom-pages]]. Back to [[../_module]].
