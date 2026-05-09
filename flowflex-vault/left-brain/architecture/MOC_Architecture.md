---
type: moc
section: left-brain/architecture
last_updated: 2026-05-08
---

# Architecture — Map of Content

System design, technical decisions, and architectural patterns for FlowFlex.

---

## System Overview

```mermaid
graph TD
    Client["Browser / Mobile App"]

    subgraph Edge
        CDN["CDN (Cloudflare)"]
        LB["Nginx (reverse proxy)"]
    end

    subgraph App["Laravel Application"]
        FERoutes["Vue + Inertia Routes\n(public pages)"]
        FilamentPanels["Filament 5 Panels\n(admin / hr / crm / …)"]
        APIRoutes["REST API\n(Laravel Sanctum)"]
        Queue["Horizon Queue Workers"]
    end

    subgraph Data
        PG["PostgreSQL 17"]
        Redis["Redis 8 (cache + queue)"]
        S3["S3-compatible Storage"]
        Search["Meilisearch"]
    end

    subgraph External
        Stripe["Stripe"]
        Mail["Mailgun / SES"]
        SMS["Twilio"]
        Push["FCM / APNs"]
    end

    Client --> CDN --> LB
    LB --> FERoutes
    LB --> FilamentPanels
    LB --> APIRoutes
    FERoutes --> PG
    FilamentPanels --> PG
    APIRoutes --> PG
    Queue --> PG
    App --> Redis
    App --> S3
    App --> Search
    App --> Stripe
    Queue --> Mail
    Queue --> SMS
    Queue --> Push
```

---

## Request Flow

```mermaid
sequenceDiagram
    participant Browser
    participant Inertia
    participant Controller
    participant Interface
    participant Service
    participant DB

    Browser->>Inertia: GET /hr/employees
    Inertia->>Controller: __invoke()
    Controller->>Interface: listEmployees(DTO)
    Interface->>Service: (bound via ServiceProvider)
    Service->>DB: query with company scope
    DB-->>Service: Collection
    Service-->>Controller: EmployeeListData[]
    Controller-->>Inertia: Inertia::render('HR/Employees/Index', data)
    Inertia-->>Browser: HTML + props
```

---

## Files

| File | Contents |
|---|---|
| [[tech-stack]] | Full technology stack with rationale |
| [[auth-rbac]] | Authentication flows, 2-layer RBAC |
| [[multi-tenancy]] | Tenant isolation strategy |
| [[module-system]] | Interface/Service/ServiceProvider pattern |
| [[event-bus]] | Cross-domain event architecture |
| [[data-architecture]] | DTOs, migrations, ULID, soft deletes, multi-currency |
| [[analytics-data-architecture]] | Read replica vs warehouse decision, dbt project |
| [[ai-gdpr-data-residency]] | LLM routing, EU AI Act, data residency, GDPR |
| [[portal-architecture]] | Unified 6-portal framework, guard isolation |

---

## Key Architectural Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Admin UI | Filament 5 (Livewire) | Fastest admin CRUD, built-in auth, extensible |
| Public pages | Vue 3 + Inertia | SPA-feel, SSR-capable, shared Laravel routing |
| Primary key | ULID | Sortable, URL-safe, no sequential ID enumeration |
| Multi-tenancy | `company_id` + global scope | Simplest for PostgreSQL, no separate schemas |
| Service binding | Interface + ServiceProvider | Testable, swappable implementations |
| API layer | Laravel Sanctum | Stateful for SPA, token for mobile/API |
| Queue | Horizon + Redis | Full visibility dashboard, priority queues |
| Search | Meilisearch | Typo-tolerant, fast, self-hosted |
| DTO | spatie/laravel-data | Input validation + output serialization in one |

---

## Related

- [[00_MOC_LeftBrain]] — master index
- [[concept-interface-service-pattern]]
- [[concept-multi-tenancy]]
- [[concept-dto-pattern]]
