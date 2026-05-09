---
tags: [flowflex, domain/hr, dei, analytics, workforce, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-08
---

# DEI & Workforce Analytics

Data-driven insights into workforce diversity, equity, and inclusion — and the broader HR metrics executives need. Private, aggregate-only reporting that meets GDPR requirements.

**Who uses it:** HR directors, C-suite, DEI leads
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]], [[Payroll]], [[Recruitment & ATS]], [[Performance & Reviews]]
**Phase:** 8

---

## Features

### Diversity Metrics (Aggregate Only)

- Gender distribution: company-wide, by department, by level
- Age distribution: company-wide, by department
- Tenure distribution: under 1yr, 1-3yr, 3-5yr, 5yr+
- Nationality/origin (if self-declared — optional field)
- All metrics shown as percentages — no individual identification
- Trend charts: how composition has changed over time
- Hiring funnel diversity: by stage (applicants → interviews → offers → hires)

### Pay Equity Analysis

- Gender pay gap: mean and median salary by gender, by level, by department
- Pay bands compliance: % of employees in vs out of role band
- Compa-ratio analysis: salary ÷ midpoint of band
- Unexplained pay gap identification (controls for role, level, tenure)
- Export: structured data for regulatory reporting

### Attrition & Retention

- Voluntary vs involuntary attrition (monthly, quarterly, annual)
- Regretted attrition: flag when high performers leave
- Attrition by: department, manager, tenure band, age group
- Flight risk indicators: tenure + performance + engagement score combination
- Exit interview theme analysis (AI-categorises reasons)

### Employee Sentiment

- eNPS trend (from [[Employee Feedback]])
- Engagement score over time
- Burnout risk index (composite of leave, overtime, feedback signals)
- Manager effectiveness score (from 360 feedback)

### Inclusion Index

- Psychological safety score (from pulse survey questions)
- Promotion equity: promotion rates by demographic group
- Mentoring coverage: % of employees with active mentor
- Training access: L&D hours by department, level, group

### Reporting & Compliance

- Pre-built report: UK Gender Pay Gap Report (statutory format)
- Pre-built report: EU Pay Transparency Directive compliance
- Pre-built report: Workforce diversity snapshot (board-ready PDF)
- Custom report builder: drag-and-drop metric selection
- Scheduled email delivery to stakeholders

---

## Privacy Controls

- All DEI data collected as self-declared (employees choose to share)
- No metric reveals individual data — minimum group size of 5 before showing stat
- Admin must set privacy policy before DEI data collection activates
- GDPR deletion: DEI data purged with employee record

---

## Database Tables (2)

### `employee_dei_declarations`
| Column | Type | Notes |
|---|---|---|
| `employee_id` | ulid FK | |
| `gender_identity` | string encrypted nullable | self-declared |
| `age_band` | string encrypted nullable | stored as band not DOB |
| `ethnicity` | string encrypted nullable | |
| `disability_status` | string encrypted nullable | |
| `declared_at` | timestamp | |

### `dei_snapshots`
| Column | Type | Notes |
|---|---|---|
| `snapshot_date` | date | |
| `metric_key` | string | e.g. `gender_ratio_engineering` |
| `value` | decimal | |
| `group_size` | integer | privacy: hide if < 5 |

---

## Permissions

```
hr.dei.view-aggregate
hr.dei.view-pay-equity
hr.dei.manage-declarations
hr.dei.generate-reports
hr.workforce-analytics.view
```

---

## Competitor Comparison

| Feature | FlowFlex | BambooHR | Rippling | Lattice |
|---|---|---|---|---|
| DEI dashboards | ✅ | partial | ✅ | ✅ |
| Pay equity analysis | ✅ | ❌ | ✅ | ✅ |
| EU Pay Transparency report | ✅ | ❌ | ❌ | ❌ |
| GDPR-safe aggregate-only | ✅ | partial | partial | partial |
| Integrated with performance data | ✅ | ❌ | ✅ | ✅ |

---

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Employee Feedback]]
- [[Performance & Reviews]]
- [[Payroll]]
