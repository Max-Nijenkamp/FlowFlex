---
tags: [flowflex, domain/hr, overview, phase/2]
domain: HR & People
panel: hr
color: "#7C3AED"
status: in-progress
last_updated: 2026-05-06
---

# HR Overview

The HR & People domain is the people layer of FlowFlex. It manages every stage of the employee lifecycle — from the first job posting to the final offboarding — and serves as the source of truth for every other domain that needs to know who works at the company.

**Filament Panel:** `hr`
**Domain Colour:** Violet `#7C3AED` / Light: `#EDE9FE`
**Domain Icon:** `users` (Heroicons)
**Phase:** 2 (core: Employee Profiles, Onboarding, Leave, basic Payroll) + 5 (full suite)

## Modules in This Domain

| Module | Phase | Status | Description |
|---|---|---|---|
| [[Employee Profiles]] | 2 | planned | Central employee record, directory |
| [[Onboarding]] | 2 | planned | Structured new hire journeys |
| [[Leave Management]] | 2 | planned | Leave requests, balances, approvals |
| [[Payroll]] | 2 | planned | Payroll runs, payslips, tax |
| [[Offboarding]] | 5 | planned | Exit checklist, access revocation |
| [[Performance & Reviews]] | 5 | planned | OKRs, review cycles, 360 feedback |
| [[Recruitment & ATS]] | 5 | planned | Full ATS, applicant pipeline, offers |
| [[Scheduling & Shifts]] | 5 | planned | Shift building, clock-in/out |
| [[Benefits & Perks]] | 5 | planned | Benefits catalogue, enrolment |
| [[Employee Feedback]] | 5 | planned | Pulse surveys, eNPS, burnout signals |
| [[HR Compliance]] | 5 | planned | Certifications, mandatory training |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `EmployeeHired` | [[Recruitment & ATS]] | [[Onboarding]], [[Payroll]], [[Scheduling & Shifts]], LMS |
| `EmployeeOffboarded` | [[Offboarding]] | IT (revoke access), [[Payroll]] (final run), Asset Management |
| `LeaveApproved` | [[Leave Management]] | [[Payroll]] (deductions), [[Scheduling & Shifts]] (rota) |
| `TimeEntryApproved` | [[Time Tracking]] | [[Payroll]] (add to pay run) |
| `CandidateHired` | [[Recruitment & ATS]] | [[Employee Profiles]] (create record), [[Onboarding]] (start flow) |
| `CertificationExpired` | [[HR Compliance]] | LMS (renewal course), Notifications |
| `BurnoutSignalDetected` | [[Employee Feedback]] | HR managers, Notifications |

## The Employee Profile as Source of Truth

[[Employee Profiles]] is the anchor record for this entire domain. All other HR modules reference and extend the employee record. When a candidate is hired via [[Recruitment & ATS]], a profile is automatically created.

## Related

- [[Employee Profiles]]
- [[Onboarding]]
- [[Offboarding]]
- [[Leave Management]]
- [[Payroll]]
- [[Performance & Reviews]]
- [[Recruitment & ATS]]
- [[Scheduling & Shifts]]
- [[Benefits & Perks]]
- [[Employee Feedback]]
- [[HR Compliance]]
- [[Panel Map]]
