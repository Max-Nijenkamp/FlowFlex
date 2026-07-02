---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Hybrid Service Pattern: Actions + Interface→Service

---

## Context

The original vault used Interface→Service pattern for all domain operations. This adds boilerplate (interface file + service file + provider binding + controller injection) for operations that have a single implementation and are called from one place.

`lorisleiva/laravel-actions` provides single-class actions that can be invoked as a plain method, a queued job, a controller endpoint, or a Filament action simultaneously.

---

## Decision

Use a hybrid approach:

- **`lorisleiva/laravel-actions`** for: single-step operations, one implementation, called from 1–2 places (e.g. `SendWelcomeEmail`, `MarkInvoiceAsPaid`, `DeactivateModule`)
- **Interface→Service** for: multi-method domain services, testable swappable implementations, cross-domain dependencies (e.g. `EmployeeService`, `InvoiceService`, `LeaveService`)

Decision rule: if the operation has one method and no likely swap, use an Action. If it has 3+ methods or needs interface-based test mocking, use Interface→Service.

---

## Consequences

- Actions live in `app/Actions/{Domain}/`
- Interface→Service files stay in `app/Contracts/`, `app/Services/`, `app/Providers/`
- Two patterns to understand — documented in [[architecture/patterns/actions-pattern]] and [[architecture/patterns/interface-service]]
- Mixed pattern is an acceptable trade-off: reduces boilerplate for simple operations without losing the flexibility of Interface→Service for complex ones

---

## Related

- [[architecture/patterns/actions-pattern]]
- [[architecture/patterns/interface-service]]
