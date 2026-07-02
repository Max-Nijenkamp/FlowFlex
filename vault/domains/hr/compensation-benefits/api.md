---
domain: hr
module: compensation-benefits
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# API — Compensation & Benefits

DTOs (spatie/laravel-data) and the service contract. No cross-domain events fired or consumed.

## DTOs (intended)

### CreateBandData
- `job_grade` (required)
- `department_id` (nullable)
- `min_salary_cents` / `mid_salary_cents` / `max_salary_cents` (required, cross-field `min ≤ mid ≤ max`)
- `currency`
- Validation message: "Band values must satisfy min ≤ mid ≤ max."

### AdjustSalaryData
- `employee_id`
- `new_salary_cents` (`min:0`)
- `effective_date`
- `reason` (in set: hire / promotion / comp-review / correction)

### EnrollBenefitData
- `employee_id`
- `benefit_id` (must be active, not already enrolled)

## Service contract (intended)

`CompensationServiceInterface` → `CompensationService` — see [[architecture]].

| Method | Signature | Notes |
|---|---|---|
| adjustSalary | `(AdjustSalaryData $data): void` | payroll update + history append, single transaction |
| bulkAdjust | `(array<AdjustSalaryData> $rows): BulkResult` | comp review; per-row try/catch |
| compaRatio | `(string $employeeId): ?float` | null when no matching band |
| enroll | `(EnrollBenefitData $data)` | benefit cost feeds next payroll run |
| unenroll | `(string $employeeBenefitId)` | |

## Filament surfaces (intended)

Nav group **Payroll**. All artifacts gate via `canAccess()` — see [[security]].

| Artifact | Kind | Notes |
|---|---|---|
| `CompensationBandResource` | CRUD resource | band builder; band-vs-actual comparison at band level, not exact salaries |
| `BenefitResource` | CRUD resource | catalog |
| `BenefitEnrollmentResource` | CRUD resource | enrollments, enroll/unenroll actions |
| `SalaryHistoryRelationManager` | on employee view | sensitive-gated, read-only |

## Related

- [[architecture]]
- [[data-model]]
- [[security]]
