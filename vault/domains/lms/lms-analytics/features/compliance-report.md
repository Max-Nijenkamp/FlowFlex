---
domain: lms
module: lms-analytics
feature: compliance-report
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Compliance Report

Mandatory-training compliance: what % of required employees have completed, and who is overdue.

## Behaviour

- Computes % of required employees who completed each mandatory course (and overdue lists).
- Reads certification status (issued/expiring/expired) for compliance certificates.
- Exportable (throttled).

## UI

- **Kind**: widget  <!-- ComplianceWidget + export action on the dashboard's compliance tab -->
- **Page**: Compliance tab of `LmsDashboardPage` (`ComplianceWidget`, `/lms/analytics` → Compliance).
- **Layout**: compliance % per mandatory course (progress bars), overdue-employee table, certification-expiry summary, export button.
- **Key interactions**: filter to overdue; export report (rate-limited); drill into a course's overdue list.
- **States**: empty (no mandatory courses → "No mandatory training configured") · loading (skeleton) · error (export throttled → "Try again shortly") · selected (course row → overdue detail).
- **Gating**: `lms.analytics.view`.

## Data

- Owns / writes: nothing.
- Reads: enrolments (mandatory + due dates), certificates (expiry).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: nothing (a report). Export is a file, not a cross-domain write.
- Shared entity: enrolments, certificates (read-only).

## Unknowns

- Whether compliance % should feed an HR / regulator-facing audit report (cross-domain); export format + queueing — see [[../unknowns]].

## Related

- [[../_module|LMS Analytics module]] · [[lms-dashboard]] · [[../../certifications/_module|Certifications]]
