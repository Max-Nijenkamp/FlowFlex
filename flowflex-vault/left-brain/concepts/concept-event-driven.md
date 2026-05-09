---
type: concept
category: architecture
last_updated: 2026-05-08
---

# Concept: Event-Driven Cross-Domain Communication

> **Canonical implementation**: [[event-bus]] — full event map, listener registration, queued listener pattern, dead-letter policy.

Domains communicate exclusively through Laravel Events. No direct service-to-service calls across domain boundaries.

---

## Problem It Solves

Without events: HR domain directly calls Payroll service when an employee is hired. HR now depends on Payroll. Add 5 more consumers and HR has 5 direct dependencies — tight coupling, fragile, hard to extend.

With events: HR fires `EmployeeHired` and forgets. Payroll, Onboarding, IT, LMS all listen independently. Zero coupling.

---

## Rules / Invariants

1. Cross-domain = always via event (never direct service call across domain boundary)
2. Within-domain = direct service call is fine and preferred
3. Events carry scalar IDs only (no Eloquent models — consuming domain may have a different model)
4. All cross-domain listeners implement `ShouldQueue` (async)
5. Listener failure MUST NOT break the emitting transaction
6. Events always include `company_id` in payload

---

## Examples

### Good — fire and forget

```php
// HR\EmployeeService
public function hire(CreateEmployeeData $data): Employee
{
    $employee = Employee::create($data->toArray());
    event(new EmployeeHired(
        company_id: $employee->company_id,
        employee_id: $employee->id,
        user_id: $employee->user_id,
        start_date: $employee->start_date,
    ));
    return $employee;
}
```

### Bad — direct cross-domain call

```php
// HR\EmployeeService — DON'T DO THIS
public function hire(CreateEmployeeData $data): Employee
{
    $employee = Employee::create($data->toArray());
    app(PayrollServiceInterface::class)->createRecord($employee); // ❌ tight coupling
    app(OnboardingServiceInterface::class)->startFlow($employee); // ❌
    return $employee;
}
```

---

## Applied In

- [[event-bus]] — full architecture note with complete event map
- Every domain's Service layer fires events on state changes

---

## Related

- [[concept-interface-service-pattern]]
- [[event-bus]]
