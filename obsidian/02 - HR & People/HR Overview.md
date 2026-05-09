---
tags: [flowflex, domain/hr, overview, phase/2]
domain: HR & People
panel: hr
color: "#7C3AED"
status: complete
last_updated: 2026-05-07
---

# HR Overview

The HR & People domain is the people layer of FlowFlex. It manages every stage of the employee lifecycle — from the first job posting to the final offboarding — and serves as the source of truth for every other domain that needs to know who works at the company.

**Filament Panel:** `hr`
**Domain Colour:** Violet `#7C3AED` / Light: `#EDE9FE`
**Domain Icon:** `users` (Heroicons)
**Phase:** 2 (core: Employee Profiles, Onboarding, Leave, Payroll) · 8 (extensions: Recruitment, Performance, Scheduling, Benefits, Feedback, HR Compliance, Offboarding)

## Modules in This Domain

| Module | Phase | Status | Description |
|---|---|---|---|
| [[Employee Profiles]] | 2 | ✅ complete | Central employee record, directory |
| [[Onboarding]] | 2 | ✅ complete | Structured new hire journeys |
| [[Leave Management]] | 2 | ✅ complete | Leave requests, balances, approvals |
| [[Payroll]] | 2 | ✅ complete | Payroll runs, payslips, tax |
| [[Offboarding]] | 8 | planned | Exit checklist, access revocation, final pay |
| [[Performance & Reviews]] | 8 | planned | OKRs, review cycles, 360 feedback, ratings |
| [[Recruitment & ATS]] | 8 | planned | Full ATS, applicant pipeline, offers, handoff to onboarding |
| [[Scheduling & Shifts]] | 8 | planned | Shift building, clock-in/out, absence coverage |
| [[Benefits & Perks]] | 8 | planned | Benefits catalogue, enrolment, flex benefits |
| [[Employee Feedback]] | 8 | planned | Pulse surveys, eNPS, burnout signals |
| [[HR Compliance]] | 8 | planned | Certifications, mandatory training deadlines |
| [[Org Chart & Workforce Planning]] | 8 | planned | Visual org chart, headcount planning, position management |
| [[AI Recruiting Assistant]] | 8 | planned | JD generation, CV scoring, bias detection, screening questions |
| [[DEI & Workforce Analytics]] | 8 | planned | Pay equity, EU Pay Transparency, aggregate diversity metrics |
| [[Compensation Management]] | 8 | planned | Salary bands, comp review cycles, market benchmarking |

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
