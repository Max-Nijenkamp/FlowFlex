---
type: architecture
category: api
color: "#A78BFA"
---

# API Design

---

## Overview

FlowFlex exposes a REST API at `/api/v1/`. It is a Sanctum-authenticated, thin-controller API that proxies to the same domain services used by the Filament panels. The API is not a separate application — it shares the same Laravel codebase, service layer, and data access patterns.

---

## Authentication

All endpoints (except `POST /api/v1/auth/token`) require a bearer token:

```
POST /api/v1/auth/token
Content-Type: application/json

{
  "email": "max@example.com",
  "password": "...",
  "device_name": "Max's MacBook"
}

Response 200:
{
  "token": "1|abcdefgh...",
  "expires_at": null
}
```

Subsequent requests use the token in the `Authorization` header:

```
GET /api/v1/employees
Authorization: Bearer 1|abcdefgh...
```

Tokens carry abilities (scopes) that restrict what the token can do. A token created for read-only access cannot POST to mutation endpoints. Token abilities are defined at creation time and are validated by Sanctum's `tokenCan()` check in each controller.

---

## Thin Controllers

All API controllers are thin. Each action is under 10 lines. No business logic, no model access, no validation — all delegated to the service layer via an injected interface:

```php
class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeServiceInterface $employees,
    ) {}

    public function index(ListEmployeesData $data): JsonResponse
    {
        return response()->json(
            $this->employees->list($data)
        );
    }

    public function store(CreateEmployeeData $data): JsonResponse
    {
        $employee = $this->employees->create($data);
        return response()->json($employee, 201);
    }
}
```

The `$data` parameter is a `spatie/laravel-data` Data class. Laravel's service container resolves and validates it automatically before the controller method is called. Invalid input returns a `422 Unprocessable Entity` with structured validation errors before the controller is reached.

---

## Response Format

All responses follow a consistent JSON structure:

```json
// Success — list
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 25,
    "total": 112
  }
}

// Success — single resource
{
  "data": { "id": "...", "first_name": "Max", ... }
}

// Error
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

There is no wrapping envelope beyond `data` and `meta`. API consumers should expect `data` to be an array for list endpoints and an object for single-resource endpoints.

---

## Rate Limiting

Rate limits are defined per endpoint group in `RouteServiceProvider`:

| Endpoint Group | Limit | Window |
|---|---|---|
| `POST /api/v1/auth/token` | 5 requests | 1 minute per IP |
| `GET /api/v1/*` (read) | 300 requests | 1 minute per token |
| `POST /api/v1/*` (write) | 60 requests | 1 minute per token |
| `DELETE /api/v1/*` | 30 requests | 1 minute per token |

Rate limit headers are included in every response: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`.

---

## Endpoints

```
# Authentication
POST   /api/v1/auth/token           — issue token
POST   /api/v1/auth/logout          — revoke current token
POST   /api/v1/auth/refresh         — refresh token (not yet implemented)

# Company
GET    /api/v1/company              — current company details
PATCH  /api/v1/company              — update company settings

# Employees
GET    /api/v1/employees            — list employees (paginated)
POST   /api/v1/employees            — create employee
GET    /api/v1/employees/{id}       — get employee
PATCH  /api/v1/employees/{id}       — update employee
DELETE /api/v1/employees/{id}       — soft-delete employee

# Projects
GET    /api/v1/projects             — list projects
POST   /api/v1/projects             — create project
GET    /api/v1/projects/{id}        — get project
PATCH  /api/v1/projects/{id}        — update project

# Tasks
GET    /api/v1/tasks                — list tasks (filterable by project)
POST   /api/v1/tasks                — create task
GET    /api/v1/tasks/{id}           — get task
PATCH  /api/v1/tasks/{id}           — update task
DELETE /api/v1/tasks/{id}           — soft-delete task

# Webhooks
GET    /api/v1/webhooks             — list webhook endpoints
POST   /api/v1/webhooks             — register endpoint
DELETE /api/v1/webhooks/{id}        — remove endpoint
```

---

## Webhook Delivery

Outbound webhooks deliver domain events to registered external URLs.

**Signature**: every webhook request includes an `X-FlowFlex-Signature` header — a HMAC-SHA256 hex digest of the raw request body signed with the company's webhook secret:

```
X-FlowFlex-Signature: sha256=abc123...
```

Recipients verify the signature by computing their own HMAC and comparing with `hash_equals()`.

**Delivery**: the `DeliverWebhookJob` queued job sends the HTTP POST. On failure it retries with exponential backoff — 30 seconds, then 5 minutes, then 30 minutes (3 total attempts). After 3 failures the delivery is marked `failed` in `webhook_deliveries` and an alert fires to the company's notification inbox.

**Payload format**: all webhook payloads follow a common envelope:

```json
{
  "event": "employee.created",
  "company_id": "01ARZ...",
  "occurred_at": "2026-05-13T14:00:00Z",
  "data": {
    "id": "01ARZ...",
    "first_name": "Max",
    "email": "max@example.com"
  }
}
```
