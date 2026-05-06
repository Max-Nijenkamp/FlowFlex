---
tags: [flowflex, domain/hr, benefits, perks, phase/5]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# Benefits & Perks

Benefits catalogue that employees can browse and enrol in. Eligibility rules keep complexity manageable. Costs flow automatically to payroll deductions.

**Who uses it:** HR team, all employees
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]], [[Payroll]] (for deductions)
**Phase:** 5
**Build complexity:** Medium — 2 resources, 4 tables

## Features

- **Benefits catalogue builder** — define each benefit: name, description, cost, provider
- **Benefit types:** health insurance, dental, vision, pension/401k, life insurance, cycle-to-work, gym membership, childcare vouchers, private travel insurance
- **Eligibility rules** per benefit — by role, employment type, tenure, location
- **Open enrolment periods** — annual window when employees can change selections
- **Life event triggers** — marriage, new child — allows mid-year changes
- **Employee enrolment portal** — browse available benefits, select, see cost impact on pay
- **Employer cost dashboard** — total benefits spend by type and headcount
- **Pension contribution tracking** — employee vs employer contribution, per country rules
- **Provider contact and documentation storage** per benefit

## Integration with Payroll

When [[Payroll]] is active:
- Elected benefit costs automatically create deductions in the next payroll run
- Employer pension contributions included in employer cost calculations
- Salary sacrifice benefits correctly reduce gross pay for tax purposes

## Database Tables (4)

1. `benefits` — benefit definitions and costs
2. `benefit_eligibility_rules` — eligibility criteria per benefit
3. `employee_benefit_enrolments` — employee elections per benefit
4. `open_enrolment_windows` — configured enrolment period dates

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Payroll]]
