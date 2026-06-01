---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Salary History Tracking

---

## Context

When an employee's salary changes (annual raise, promotion), do we track the history of salary changes, or just update the current value?

Options:
1. **No history** — just `hr_employees.salary_cents`, update in place
2. **History table** — `hr_salary_history` with effective date, amount, reason, changed by
3. **In payslips only** — salary history reconstructable from payslip records

---

## Decision

**Use a salary history table.** Add `hr_salary_history`.

---

## Rationale

- HR managers need to see "Max was on €50,000, got a raise to €55,000 on 2026-01-01"
- Payslips only show the paid amount, not the "effective salary change" event
- Compensation bands analysis requires knowing when salary changed, not just what it is now
- Comp review cycles produce bulk salary changes — need an audit trail

`hr_employees.salary_cents` is the current value (fast read for payroll calculations). `hr_salary_history` is the audit trail.

---

## Table

```php
Schema::create('hr_salary_history', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('company_id')->references('id')->on('companies');
    $table->foreignUlid('employee_id')->references('id')->on('hr_employees');
    $table->text('salary_cents');        // encrypted — same as hr_employees
    $table->string('currency', 3);
    $table->date('effective_date');
    $table->string('reason')->nullable(); // promotion, annual review, correction, etc.
    $table->foreignUlid('changed_by')->references('id')->on('users');
    $table->timestamps();

    $table->index(['company_id', 'employee_id', 'effective_date']);
});
```

## Update Process

When salary changes, a `SalaryChanged` action:
1. Records a new row in `hr_salary_history`
2. Updates `hr_employees.salary_cents` to the new value
3. Fires a `SalaryChanged` event (for notifications, analytics)

---

## Related

- [[domains/hr/compensation-benefits]]
- [[domains/hr/payroll]]
- [[architecture/patterns/encryption]] — salary is encrypted at rest
