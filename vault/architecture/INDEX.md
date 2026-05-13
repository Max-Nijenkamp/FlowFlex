---
type: architecture
category: index
color: "#A78BFA"
---

# Architecture — Map of Content

This section defines how FlowFlex is built: the technology choices, structural patterns, and non-obvious decisions that every builder must understand before writing code.

## Core Documents

- [[overview]] — System diagram, key non-obvious architectural decisions explained, and the reasoning behind each
- [[tech-stack]] — Every package, version, and technology in use; frontend vs admin decision table; key constraints
- [[data-model]] — Core entities, schema conventions, ER diagram, multi-tenancy schema pattern, model traits
- [[multi-tenancy]] — CompanyContext singleton, BelongsToCompany trait, CompanyScope global scope, queue context restoration
- [[auth-rbac]] — Two-layer RBAC (admins vs users), guards, Spatie permission teams, canAccess() pattern, API auth
- [[module-system]] — module_catalog table, company_module_subscriptions, BillingService::hasModule(), module keys, marketplace
- [[api-design]] — REST V1 API, Sanctum tokens, thin controllers, webhook delivery, response format
- [[event-bus]] — Cross-domain event pattern, event structure rules, queued listeners, cross-domain event map
- [[filament-patterns]] — Critical non-obvious Filament 5 patterns every builder must read before writing a resource or page

## Patterns

- [[patterns/interface-service]] — Contract interface, ServiceProvider binding, thin controller, never inject concrete
- [[patterns/dto-pattern]] — spatie/laravel-data for all input and output, TypeScript auto-generation
- [[patterns/belongs-to-company]] — Three required traits on every tenant model, ULID keys, soft deletes
- [[patterns/testing-pattern]] — Pest PHP, SQLite in-memory, CompanyContext setup, factory conventions, Filament test patterns
