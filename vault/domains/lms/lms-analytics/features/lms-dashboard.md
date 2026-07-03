---
domain: lms
module: lms-analytics
feature: lms-dashboard
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: LMS Dashboard

The learning analytics dashboard: completion, engagement, quiz performance, certification status, and popularity.

## Behaviour

- Aggregates `LmsAnalyticsService::metrics(from, to)` into charts + widgets.
- Sections for certifications/skills/paths are hidden when those modules are inactive.
- Drop-off analysis pinpoints the lesson with the highest abandonment.
- Results are cached (1h historical / 15min current).

## UI

- **Kind**: widget  <!-- dashboard page composed of apex-chart widgets, ui-strategy row #6 -->
- **Page**: "LMS Dashboard" (`LmsDashboardPage` + `CompletionRateWidget` / `EngagementWidget`, `/lms/analytics`)
- **Layout**: KPI row (active learners, avg completion, certificates issued) + charts (completion trend, engagement, quiz pass rates, popular courses) + a date-range filter.
- **Key interactions**: change date range → recompute (cached); drill into drop-off lesson; toggle course/path scope.
- **States**: empty (no learning activity yet → "No learning data for this period") · loading (skeleton charts) · error (aggregation failed → retry) · selected (chart segment → detail).
- **Gating**: `lms.analytics.view`.

## Data

- Owns / writes: nothing.
- Reads: enrolments, lesson progress, certificates, skills (via owning modules).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: nothing (a view).
- Shared entity: all LMS sibling data, read-only.

## Test Checklist

### Unit
- [ ] Cache key embeds company + window; soft-dep sections null when source module inactive

### Feature (Pest)
- [ ] Inactive module (certifications/skills/mentoring) -> section null, widget omitted, no error
- [ ] Tenant isolation on all aggregates

### Livewire
- [ ] Dashboard page canAccess() explicit; date filter re-scopes widgets

## Unknowns

- Real time-on-task tracking vs inferred; feeding the Analytics domain — see [[../unknowns]].

## Related

- [[../_module|LMS Analytics module]] · [[compliance-report]] · [[../architecture]]
