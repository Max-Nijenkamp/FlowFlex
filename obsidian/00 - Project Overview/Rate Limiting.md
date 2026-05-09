---
tags: [flowflex, architecture, rate-limiting, throttle, phase/1]
domain: Platform
status: planned
last_updated: 2026-05-08
---

# Rate Limiting

Three-layer rate limiting: per IP (unauthenticated), per user, and per tenant. Limits scale with the subscription plan tier — Enterprise customers get higher limits than Starter.

---

## Layers

| Layer | Scope | Default Limit | Applies To |
|---|---|---|---|
| IP (global) | Per IP address | 60 req/min | Unauthenticated requests, login attempts |
| User | Per authenticated user | 300 req/min (Starter), 600 (Pro), 1,200 (Enterprise) | All authenticated requests |
| Tenant | Per company | 1,000 req/min (Starter), 3,000 (Pro), 10,000 (Enterprise) | Sum of all users in the company |

If a request hits any layer's limit, a `429 Too Many Requests` response is returned immediately.

---

## Configuration

### Define Rate Limiters (AppServiceProvider)

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    // 1. Per-IP (unauthenticated)
    RateLimiter::for('global', function (Request $request) {
        return Limit::perMinute(60)
            ->by($request->ip())
            ->response(fn () => response()->json([
                'message' => 'Too many requests.',
                'retry_after' => 60,
            ], 429));
    });

    // 2. Per-user (scales with plan)
    RateLimiter::for('api', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return Limit::perMinute(60)->by($request->ip());
        }

        $limit = match ($user->company->plan_tier) {
            'enterprise' => 1200,
            'pro'        => 600,
            default      => 300, // starter
        };

        return Limit::perMinute($limit)
            ->by("user:{$user->id}")
            ->response(fn (Request $req, array $headers) => response()->json([
                'message' => 'Rate limit exceeded.',
                'retry_after' => $headers['Retry-After'] ?? 60,
                'limit' => $limit,
                'upgrade_url' => route('billing.upgrade'),
            ], 429)->withHeaders($headers));
    });

    // 3. Per-tenant
    RateLimiter::for('tenant', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return Limit::none();
        }

        $limit = match ($user->company->plan_tier) {
            'enterprise' => 10000,
            'pro'        => 3000,
            default      => 1000, // starter
        };

        return Limit::perMinute($limit)
            ->by("tenant:{$user->company_id}");
    });

    // 4. Login endpoint — strict
    RateLimiter::for('login', function (Request $request) {
        return [
            Limit::perMinute(5)->by($request->ip()),
            Limit::perMinute(10)->by($request->input('email')),
        ];
    });

    // 5. API tokens — higher limits
    RateLimiter::for('api-token', function (Request $request) {
        $token = $request->bearerToken();
        $limit = $request->user()?->company->plan_tier === 'enterprise' ? 5000 : 1000;
        return Limit::perMinute($limit)->by("api-token:{$token}");
    });
}
```

### Apply to Routes

```php
// routes/web.php
Route::middleware(['throttle:global'])->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
});

Route::middleware(['auth', 'throttle:api', 'throttle:tenant'])->group(function () {
    // All authenticated app routes
});

// API routes (higher limits for programmatic access)
Route::middleware(['auth:sanctum', 'throttle:api-token'])->prefix('api/v1')->group(function () {
    // REST API endpoints
});
```

---

## Rate Limit Headers

All API responses include rate limit headers (RFC 6585 + IETF draft):

```
X-RateLimit-Limit: 300
X-RateLimit-Remaining: 287
X-RateLimit-Reset: 1746700860
Retry-After: 47  (only on 429)
```

These are included automatically by Laravel's throttle middleware. Ensure they're exposed in CORS config:

```php
// config/cors.php
'exposed_headers' => [
    'X-RateLimit-Limit',
    'X-RateLimit-Remaining',
    'X-RateLimit-Reset',
    'Retry-After',
],
```

---

## Plan Tier Limits Reference

| Endpoint Type | Starter | Pro | Enterprise |
|---|---|---|---|
| Per-user req/min | 300 | 600 | 1,200 |
| Per-tenant req/min | 1,000 | 3,000 | 10,000 |
| API token req/min | 500 | 1,500 | 5,000 |
| File uploads/hour | 50 | 200 | 1,000 |
| Webhook deliveries/hour | 100 | 500 | 2,000 |
| AI queries/day | 100 | 500 | 2,000 |

---

## Specific Endpoint Rate Limits

Some endpoints have additional specific limits regardless of plan:

| Endpoint | Limit | Scope |
|---|---|---|
| `POST /login` | 5/min + 10/min by email | IP + email |
| `POST /register` | 3/min | IP |
| `POST /password/reset` | 3/min | IP + email |
| `POST /api/v1/webhooks` | 1,000/hr | Per company |
| `POST /files/upload` | 100/hr | Per user |
| `POST /ai/chat` | 60/min | Per user |
| `POST /ai/agents/run` | 20/min | Per company |
| `GET /api/v1/*` (public API) | 1,000/hr | Per API token |

---

## Rate Limit Exceeded (429) Response

```json
{
  "message": "Rate limit exceeded.",
  "retry_after": 47,
  "limit": 300,
  "upgrade_url": "https://app.flowflex.com/billing/upgrade"
}
```

Headers:
```
HTTP/1.1 429 Too Many Requests
Retry-After: 47
X-RateLimit-Limit: 300
X-RateLimit-Remaining: 0
Content-Type: application/json
```

---

## Frontend Handling (Vue/Inertia)

```typescript
// resources/js/composables/useApi.ts
import axios, { AxiosError } from 'axios'
import { useToast } from '@/composables/useToast'

export function useApi() {
  const toast = useToast()

  axios.interceptors.response.use(
    (response) => response,
    (error: AxiosError) => {
      if (error.response?.status === 429) {
        const retryAfter = error.response.headers['retry-after'] ?? 60
        toast.warning(`Too many requests. Please wait ${retryAfter} seconds before trying again.`)
      }
      return Promise.reject(error)
    }
  )
}
```

---

## Admin Monitoring

Rate limit events are stored for monitoring:

```php
// When a rate limit is exceeded, log to Redis for monitoring
RateLimiter::for('api', function (Request $request) {
    // ...
    ->response(function (Request $req) use ($user) {
        RateLimitExceeded::dispatch($user, $req->path(), now());
        // ...
    });
});
```

Admins can see rate limit breaches in the IT & Security panel → API Usage section.

---

## Related

- [[Architecture]]
- [[Security Rules]]
- [[API & Integrations Layer]]
- [[Error Handling]]
