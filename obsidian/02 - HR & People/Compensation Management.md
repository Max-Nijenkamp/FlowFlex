---
tags: [flowflex, domain/hr, compensation, salary, benchmarking, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-08
---

# Compensation Management

Salary bands, pay equity analysis, and annual compensation reviews — all in one place. Stop managing compensation in spreadsheets. Know what everyone earns, what they should earn, and whether your pay is fair across gender and ethnicity. Replaces Ravio, Carta, and ChartHop for most SMBs.

**Who uses it:** HR managers, finance team, managers, executives
**Filament Panel:** `hr`
**Depends on:** Core, [[Employee Profiles]], [[Payroll]], [[DEI & Workforce Analytics]], [[Performance & Reviews]]
**Phase:** 8

---

## Features

### Salary Bands

- Define bands by: job level (L1–L7 or custom), job family (Engineering, Sales, Ops, etc.)
- Band structure per band: minimum, midpoint, maximum
- Currency per band (for international teams)
- Band versioning: track changes over time with effective dates
- Visualise: compa-ratio heatmap (where each employee sits within their band)
- Alert: employees below band minimum → flag for HR

### Compensation Review Cycles

- Create a review cycle: annual, mid-year, or ad-hoc
- Participants: all employees or filtered subset
- Manager budget: allocate a % increase budget per manager (e.g. 4% of their team's payroll)
- Manager workspace: managers see their team, current salaries, compa-ratios, and recommended increases
- Approval flow: manager proposes → HR reviews → exec approves → letters generated
- Lock: freeze changes during review, prevent off-cycle increases
- Effective date: all approved increases take effect on a specified date
- Payroll sync: approved increases flow into [[Payroll]] for the next pay run

### Market Benchmarking

- Industry benchmark data: pre-loaded percentile data (P25/P50/P75/P90) by role and country
- Benchmark sources: integrated with market data (manual import or API — Radford/Willis Towers Watson data sets)
- Compare: each role's midpoint vs P50 market benchmark
- Gap analysis: which roles are below market? By how much? Cost to bring to market
- NL/EU benchmarks: Dutch salary scales (cao-benchmarks), EU pay transparency ready

### Pay Equity Analysis

- Gender pay gap analysis: mean and median gap at company, department, and job family level
- EU Pay Transparency Directive compliance report (effective 2026)
- Ethnicity pay gap (if self-declared DEI data collected)
- Unexplained gap: controls for job level, tenure, performance — surfaces unjustified gaps
- Remediation planner: list of employees to adjust and cost of closing gaps

### Employee Total Rewards View

- Each employee can see their total compensation breakdown:
  - Base salary
  - Bonus target % and actual
  - Equity / RSUs (if applicable)
  - Benefits monetary value (healthcare, pension contribution, etc.)
  - Total compensation number
- Managers see their team's total rewards
- Customisable: show/hide components per plan tier

### Offer Letters & Compensation Letters

- Generate offer letter from candidate profile (pre-hire)
- Generate compensation letter post-review (increase letter)
- Template editor: customise content, company letterhead
- E-signature: send for e-signature via [[E-Signature Native]] in one click

---

## Database Tables (4)

### `hr_salary_bands`
| Column | Type | Notes |
|---|---|---|
| `job_family` | string | Engineering, Sales, etc. |
| `level` | string | L1, L2, Senior, etc. |
| `currency` | string | ISO 4217 |
| `min` | decimal | |
| `midpoint` | decimal | |
| `max` | decimal | |
| `effective_from` | date | |
| `market_p50` | decimal nullable | benchmark midpoint |

### `hr_compensation_reviews`
| Column | Type | Notes |
|---|---|---|
| `name` | string | "2026 Annual Review" |
| `type` | enum | `annual`, `mid_year`, `ad_hoc` |
| `effective_date` | date | when increases take effect |
| `status` | enum | `planning`, `active`, `approval`, `completed` |
| `budget_pct` | decimal | % of payroll |
| `locked_at` | timestamp nullable | |

### `hr_compensation_proposals`
| Column | Type | Notes |
|---|---|---|
| `review_id` | ulid FK | |
| `employee_id` | ulid FK | |
| `current_salary` | decimal | |
| `proposed_salary` | decimal | |
| `increase_pct` | decimal | |
| `increase_reason` | enum | `merit`, `promotion`, `equity`, `market_adj` |
| `notes` | text nullable | |
| `proposed_by` | ulid FK | manager |
| `approved_by` | ulid FK nullable | |
| `status` | enum | `draft`, `submitted`, `approved`, `rejected` |

### `hr_compensation_snapshots`
| Column | Type | Notes |
|---|---|---|
| `employee_id` | ulid FK | |
| `snapshot_date` | date | |
| `base_salary` | decimal | |
| `bonus_target_pct` | decimal nullable | |
| `currency` | string | |
| `compa_ratio` | decimal nullable | salary / band midpoint |
| `market_ratio` | decimal nullable | salary / market P50 |

---

## Permissions

```
hr.compensation.view-own
hr.compensation.view-team
hr.compensation.view-all
hr.compensation.manage-bands
hr.compensation.run-reviews
hr.compensation.approve-increases
hr.compensation.view-pay-equity
```

---

## Competitor Comparison

| Feature | FlowFlex | Ravio | ChartHop | Leapsome |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€/emp/mo) | ❌ (€€€) | ❌ (€€) |
| NL/EU market benchmarks | ✅ | ✅ (NL strong) | partial | partial |
| EU Pay Transparency ready | ✅ | ✅ | ❌ | ❌ |
| Payroll sync | ✅ | partial | ❌ | ❌ |
| Review cycle workflows | ✅ | partial | ✅ | ✅ |
| Total rewards view | ✅ | ✅ | ✅ | ✅ |

---

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Payroll]]
- [[Performance & Reviews]]
- [[DEI & Workforce Analytics]]
- [[E-Signature Native]]
