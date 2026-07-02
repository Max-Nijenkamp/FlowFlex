---
domain: hr
module: compensation-benefits
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Architecture — Compensation & Benefits

Planned Interface→Service binding per [[../../../architecture/patterns/interface-service]]: `CompensationServiceInterface` → `CompensationService`.

## Services & Actions (intended)

- `adjustSalary(AdjustSalaryData $data): void` — updates payroll profile salary **and** appends an `hr_salary_history` row in a single transaction.
- `bulkAdjust(array<AdjustSalaryData> $rows): BulkResult` — comp review cycle; per-row try/catch.
- `compaRatio(string $employeeId): ?float` — employee salary vs band midpoint; `null` when no matching band.
- `enroll(EnrollBenefitData $data)` / `unenroll(string $employeeBenefitId)` — benefit cost intended to reflect in next payroll run *(assumed: payroll reads active enrollments at run time)*.

## Money handling

Monetary amounts are integer minor units (cents, `bigint`). Compa-ratio and all arithmetic go through `brick/money` — never raw float math. See [[../../../architecture/packages]].

## Salary change flow (intended)

Salary changes are append-only into `hr_salary_history`; the flow updates payroll and writes history atomically.

```mermaid
flowchart TD
    A[HR submits AdjustSalaryData] --> B{Validate: new_salary_cents >= 0, reason in set}
    B -->|invalid| E[Reject / validation error]
    B -->|valid| C[BEGIN transaction]
    C --> D[Update payroll profile salary]
    D --> F[Append hr_salary_history row<br/>amount_raw encrypted, salary_band derived,<br/>changed_by, effective_date, reason]
    F --> G[COMMIT]
    G --> H[Next payroll run reads salary + active enrollments]
```

## Related

- [[api]]
- [[data-model]]
- [[../../../architecture/patterns/interface-service]]
- [[../../../architecture/packages]]
