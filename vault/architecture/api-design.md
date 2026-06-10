---
type: architecture
category: api
pattern-key: api
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# API Design

REST API at `/api/v1/`. Sanctum-authenticated, thin-controller. Shares the same domain services as Filament panels — not a separate application.

---

## Authentication

```
POST /api/v1/auth/token
Body: { email, password, device_name }
Response: { token: "1|abc...", expires_at: null }

All other requests:
Authorization: Bearer 1|abc...
```

Tokens carry abilities (scopes). A read-only token cannot POST to mutation endpoints. Verified via `$request->user()->tokenCan('hr:write')`.

---

## Thin Controllers

```php
class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeServiceInterface $employees,
    ) {}

    public function index(ListEmployeesData $data): JsonResponse
    {
        return response()->json($this->employees->list($data));
    }

    public function store(CreateEmployeeData $data): JsonResponse
    {
        return response()->json($this->employees->create($data), 201);
    }
}
```

Under 10 lines per method. No business logic, no model access, no validation — all in the service and DTO.

---

## Response Format

```json
// List
{
  "data": [...],
  "meta": { "current_page": 1, "last_page": 5, "per_page": 25, "total": 112 }
}

// Single resource
{ "data": { "id": "01ARZ...", "first_name": "Max" } }

// Validation error — 422
{
  "message": "The given data was invalid.",
  "errors": { "email": ["The email field is required."] }
}

// Unauthorized — 401
{ "message": "Unauthenticated." }

// Forbidden — 403
{ "message": "This action is unauthorized." }

// Not found — 404
{ "message": "No query results for model [Employee]." }
```

---

## Rate Limiting

Defined in `RouteServiceProvider`:

| Endpoint Group | Limit | Window |
|---|---|---|
| `POST /api/v1/auth/token` | 5 requests | 1 min per IP |
| `GET /api/v1/*` (read) | 300 requests | 1 min per token |
| `POST /api/v1/*` (write) | 60 requests | 1 min per token |
| `DELETE /api/v1/*` | 30 requests | 1 min per token |
| `POST /api/v1/*/export` | 5 requests | 1 hour per token |

Headers on every response: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`.

Exceeding the limit returns `429 Too Many Requests` with `Retry-After` header.

---

## Token Abilities (Scopes)

| Ability | What it permits |
|---|---|
| `hr:read` | GET all HR endpoints |
| `hr:write` | POST/PATCH/DELETE HR endpoints |
| `finance:read` | GET Finance endpoints |
| `finance:write` | POST/PATCH Finance endpoints |
| `crm:read` | GET CRM endpoints |
| `crm:write` | POST/PATCH CRM endpoints |
| `*` | Full access (owner tokens only) |

---

## Core Endpoints

```
# Auth
POST   /api/v1/auth/token
POST   /api/v1/auth/logout
DELETE /api/v1/auth/tokens/{id}     — revoke specific token

# Company
GET    /api/v1/company
PATCH  /api/v1/company/settings

# HR
GET    /api/v1/employees            — paginated, filterable
POST   /api/v1/employees
GET    /api/v1/employees/{id}
PATCH  /api/v1/employees/{id}
DELETE /api/v1/employees/{id}
GET    /api/v1/leave-requests
POST   /api/v1/leave-requests
PATCH  /api/v1/leave-requests/{id}/approve
PATCH  /api/v1/leave-requests/{id}/reject

# Finance
GET    /api/v1/invoices
POST   /api/v1/invoices
GET    /api/v1/invoices/{id}
POST   /api/v1/invoices/{id}/send
POST   /api/v1/invoices/{id}/payments
GET    /api/v1/expenses
POST   /api/v1/expenses

# CRM
GET    /api/v1/contacts
POST   /api/v1/contacts
GET    /api/v1/contacts/{id}
PATCH  /api/v1/contacts/{id}
GET    /api/v1/deals
POST   /api/v1/deals
PATCH  /api/v1/deals/{id}

# Webhooks
GET    /api/v1/webhooks
POST   /api/v1/webhooks
DELETE /api/v1/webhooks/{id}
```

---

## Webhook Payload Format

```json
{
  "event": "employee.hired",
  "company_id": "01ARZ...",
  "occurred_at": "2026-06-01T14:00:00Z",
  "data": {
    "id": "01ARZ...",
    "first_name": "Max",
    "email": "max@example.com"
  }
}
```

Signed with `X-FlowFlex-Signature: sha256={hmac}`. Recipients verify with `hash_equals()`.

---

## Pagination

All list endpoints are paginated. Default: 25 per page. Maximum: 100. Controlled by `?per_page=` query param. Use `spatie/laravel-query-builder` when the external API layer is built (deferred — see [[architecture/packages]]).

### Cursor vs Offset Rule

| Use | When | Endpoints |
|---|---|---|
| **Offset** (`paginate()`, `current_page`/`last_page` meta) | human-browsed admin tables; result sets that fit in a UI; client needs page numbers | default for all Filament tables + most API list endpoints |
| **Cursor** (`cursorPaginate()`, `next_cursor` meta, no `total`) | append-only feeds and timelines (activities, audit log, notifications, messages); any programmatic consumer walking the full set (sync clients, exports via API); tables where deep `OFFSET` gets slow (≳100k rows per company) | activity/audit/notification/message endpoints from day one |

Hard rules: never offer offset paging past page 400 (`max page × per_page = 10,000` cap → tell clients to use cursor or filters); cursor endpoints sort on an indexed, immutable key (`(company_id, id)` — ULIDs are time-ordered); bulk data extraction is NOT paginated API walking — use the export jobs ([[architecture/queue-jobs]]).

---

## Versioning

API version in the URL path: `/api/v1/`. When breaking changes are needed, `/api/v2/` runs alongside `v1` for a minimum 6-month deprecation period. No header-based versioning.
