---
type: architecture
category: security
pattern-key: security
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Security

Comprehensive security model for a multi-tenant SaaS. Every layer: authentication, authorization, rate limiting, input validation, file uploads, payment webhooks, headers, and GDPR.

---

## Authentication Security

**Session security:**
- `session.regenerate()` called on every login — prevents session fixation
- `HttpOnly` and `Secure` cookie flags enforced in production
- Session lifetime: 2 hours of inactivity (configurable per company)
- Concurrent session limit: configurable (default: no limit)

**Two-factor authentication:**
- TOTP 2FA, self-service: **every user can enable/disable it themselves, any time, in their own settings page** (no admin involvement needed)
- Enable flow: show QR + secret → user confirms with a valid TOTP code → `two_factor_confirmed_at` set → recovery codes shown once
- Disable flow: requires current password + valid TOTP code
- Company admins can additionally mandate 2FA for all users via Company Settings
- Secret stored encrypted in `users.two_factor_secret`; recovery codes encrypted in `users.two_factor_recovery_codes` (single-use, regenerable)
- Login with 2FA enabled: password ok → 2FA challenge page (TOTP or recovery code) → session marked confirmed

**Email verification (mandatory):**
- **No portal access without a verified email** — applies to every Filament panel and every authenticated route
- `User` implements `MustVerifyEmail`; `verified` middleware on all panels; unverified users land on a resend-verification page
- Invitation-accepted users: accepting the invite link IS verification (`email_verified_at` set on accept)
- **Email change resets verification**: any change to `users.email` nulls `email_verified_at` and sends a new verification mail to the NEW address; user is locked out of portals until re-verified
- Verification links: signed URLs, 60-min expiry, throttled resend (6/hour)

**Password requirements:**
- Minimum 12 characters
- HaveIBeenPwned check via Laravel's `Password::defaults()` + `uncompromised()` rule
- Bcrypt cost factor: 12 (default Laravel, acceptable for 2026)

**API tokens:**
- Sanctum tokens hashed in `personal_access_tokens.token` — plain token never stored
- Tokens shown once at creation; no mechanism to retrieve the plain token again
- Token expiry: optional, set at creation; default: no expiry (suitable for integration tokens)
- All tokens scoped to ability list — no wildcard tokens except for company owners

---

## Rate Limiting

Defined in `RouteServiceProvider` using Redis-backed rate limiters:

```php
RateLimiter::for('login', fn (Request $r) =>
    Limit::perMinute(5)->by($r->ip())
);

RateLimiter::for('api', fn (Request $r) =>
    $r->user()
        ? Limit::perMinute(300)->by($r->user()->id)
        : Limit::perMinute(30)->by($r->ip())
);

RateLimiter::for('api-write', fn (Request $r) =>
    Limit::perMinute(60)->by($r->user()?->id . ':' . $r->ip())
);

RateLimiter::for('password-reset', fn (Request $r) =>
    Limit::perMinutes(5, 3)->by($r->input('email'))
);

RateLimiter::for('exports', fn (Request $r) =>
    Limit::perHour(5)->by($r->user()?->company_id)
);
```

Rate limit state stored in Redis. All rate limiters return `429 Too Many Requests` with `Retry-After` on breach.

**Filament panel rate limiting:** the Filament login form applies the `login` rate limiter. Custom forms with sensitive actions (bulk delete, data export) apply the `api-write` limiter.

---

## Authorization

**Two-layer check on every resource:**

```php
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.employees.view-any')  // permission check
        && BillingService::hasModule('hr.employees');   // module subscription check
}
```

Neither check can be skipped. Permission without module = 403. Module without permission = 403.

**BillingService::hasModule() is cached in Redis** — checking it on every page load without caching would create N+1 database hits. See [[architecture/caching]].

**Ownership checks**: any endpoint that accesses a record by ID must verify `company_id` matches the current company. The `BelongsToCompany` + `CompanyScope` global scope does this automatically for Eloquent queries. Raw queries or `DB::` calls must add `WHERE company_id = ?` manually.

**Admin panel**: `/admin` panel uses a separate `admin` guard and `Admin` model — company users cannot access it by any means. No `withoutGlobalScope()` in any non-admin code path.

---

## Input Security

**SQL injection**: zero risk for Eloquent queries (parameterized). For `DB::` raw queries, always use bindings:

```php
// Safe
DB::select('SELECT * FROM users WHERE company_id = ?', [$companyId]);

// Unsafe — never do this
DB::select("SELECT * FROM users WHERE company_id = '$companyId'");
```

**XSS**: Blade templates auto-escape via `{{ $var }}`. Use `{!! $var !!}` only for trusted HTML (e.g. Tiptap output that has been purified server-side). Tiptap HTML must be purified via HTMLPurifier before storage — see rich text section below.

**Mass assignment**: all models define `$fillable` or use `$guarded = ['id', 'company_id']`. Never use `$guarded = []` on tenant models.

**CSRF**: Laravel's CSRF middleware is active on all web routes. Filament forms include CSRF tokens automatically. API routes use Sanctum token authentication instead of CSRF (token presence replaces the CSRF check).

**Validation**: every input goes through a `spatie/laravel-data` Data class. Invalid input returns 422 before any service code runs. Never pass `$request->all()` to a service.

---

## Rich Text Security

Tiptap rich text editor produces HTML. This HTML must be purified before storage:

```php
use HTMLPurifier;

class CreateWikiPageData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $content, // raw Tiptap HTML
    ) {}

    public static function sanitize(self $data): self
    {
        $purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
        return new self(
            title: strip_tags($data->title),
            content: $purifier->purify($data->content),
        );
    }
}
```

Call `sanitize()` in the service before persisting. Never store raw Tiptap HTML without purification — stored XSS is the result.

---

## File Upload Security

All uploads go through `spatie/laravel-media-library`. Rules:

1. **MIME type validation**: validate both MIME type and file extension — never trust the `Content-Type` header alone
2. **Max file size**: enforced in PHP config (`upload_max_filesize`, `post_max_size`) and validated in the Data class
3. **Storage path**: always under `companies/{company_id}/` — never in a web-accessible public directory
4. **No executable files**: reject `.php`, `.exe`, `.sh`, `.js` uploads
5. **Pre-signed URLs**: serve files via temporary S3 pre-signed URLs (1 hour TTL) — never expose direct S3 paths

```php
// In UploadDocumentData
#[Required, FileTypes(['pdf', 'docx', 'xlsx', 'png', 'jpg', 'webp']), MaxSize(50 * 1024)] // 50MB
public readonly UploadedFile $file;
```

---

## Stripe Webhook Security

Stripe sends `POST /api/stripe/webhook`. Always verify the signature before processing:

```php
class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException) {
            abort(400, 'Invalid signature');
        }

        // Now safe to process $event
        match ($event->type) {
            'invoice.payment_succeeded' => HandleInvoicePaymentSucceeded::run($event->data->object),
            'customer.subscription.deleted' => HandleSubscriptionCancelled::run($event->data->object),
            default => null,
        };

        return response('', 200);
    }
}
```

The webhook endpoint is excluded from CSRF middleware but protected by signature verification.

---

## CORS

The API at `/api/v1/` uses Sanctum cookie auth for same-origin SPA requests and token auth for third-party API clients.

```php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => [env('APP_URL')], // same-origin only by default
    'allowed_origins_patterns' => [],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'exposed_headers' => ['X-RateLimit-Limit', 'X-RateLimit-Remaining'],
    'max_age' => 86400,
    'supports_credentials' => true, // required for Sanctum cookie auth
];
```

If third-party partners need CORS access to the API, add their origin to `allowed_origins` — do NOT use `*` (wildcard) as that disables credential-bearing requests.

---

## Admin Panel Protection

`/admin` must be protected by IP allowlist in production — only FlowFlex staff IP ranges can access it. Configure in Nginx:

```nginx
location /admin {
    allow 1.2.3.4;     # FlowFlex office IP
    allow 5.6.7.8;     # VPN exit node
    deny all;

    proxy_pass http://php-fpm;
}
```

Additionally, the `AdminPanelProvider` uses the `admin` guard — even if the IP check is bypassed, company users cannot log in to `/admin` without an `Admin` model account.

---

## Horizon and Pulse Protection

`/horizon` and `/pulse` must not be publicly accessible.

Horizon gate (restricts to authenticated admin users):

```php
// app/Providers/AppServiceProvider.php
Horizon::auth(function (Request $request): bool {
    return $request->user() instanceof Admin
        || app()->environment('local');
});
```

Pulse middleware (restrict to authenticated admin users):

```php
// config/pulse.php
'middleware' => ['web', Authenticate::class, EnsureUserIsAdmin::class],
```

`EnsureUserIsAdmin::class` checks `Auth::guard('admin')->check()`.

---

## Database and Redis Security

**PostgreSQL SSL in production**: add `sslmode=require` to database URL:

```env
DATABASE_URL=pgsql://user:pass@host:5432/dbname?sslmode=require
```

Or in `config/database.php`:
```php
'sslmode' => env('DB_SSLMODE', 'require'),
```

**Redis AUTH**: Redis must require a password in production. Configure in `config/database.php`:
```php
'redis' => [
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null), // must be set in production
        'port' => env('REDIS_PORT', 6379),
    ],
],
```

Never run Redis without authentication on a non-loopback interface.

---

## HTTP Security Headers

Set in `app/Http/Middleware/SecurityHeaders.php` (custom middleware):

```php
$response->headers->set('X-Frame-Options', 'DENY');
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('X-XSS-Protection', '1; mode=block');
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
$response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
$response->headers->set(
    'Content-Security-Policy',
    "default-src 'self'; script-src 'self' 'nonce-{$nonce}'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; connect-src 'self' wss:;"
);
```

HTTPS enforced via `config/session.php` `'secure' => true` and HSTS header in Nginx:
```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

---

## Sanctum SPA Authentication (Vue + Inertia Frontend)

The Vue 3 + Inertia public frontend (marketing site, invite acceptance, login) uses Sanctum's **cookie-based SPA auth** — not bearer tokens. Tokens are for the API only.

```php
// routes/web.php — SPA auth endpoints
// No open /register route — users join via invitation only (see domains/core/invitation-system)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register/invite/{token}', [AuthController::class, 'acceptInvite']);
```

```javascript
// Vue: login request (Inertia form)
router.post('/login', { email, password }, {
    onSuccess: () => router.visit('/app'),
    onError: (errors) => { /* show validation errors */ }
})
```

Sanctum creates a session cookie on successful login. The cookie is HttpOnly, Secure, and SameSite=Lax. No token to manage in JavaScript.

**CSRF**: Laravel issues a `XSRF-TOKEN` cookie that Axios/Inertia automatically includes as `X-XSRF-TOKEN` header. On first page load, hit `GET /sanctum/csrf-cookie` to initialise the cookie.

**For the Filament panels** (not Inertia): Filament handles session auth directly — no special CSRF wiring needed.

---

## Encrypted Attributes

Sensitive database columns must be encrypted at rest. See [[architecture/patterns/encryption]] for the full implementation.

Fields that MUST be encrypted:
- `hr_employees.national_id`, `date_of_birth`, `personal_email`
- `hr_payslips` — salary amounts (stored as encrypted integer strings)
- `fin_bank_accounts.iban`, `bic`
- `core_webhook_endpoints.secret`

Use Laravel's `'field' => 'encrypted'` cast. Encrypted columns use `text` column type (not `string` — ciphertext is longer than 255 chars).

---

## Audit Log: Authentication Events

Authentication events must appear in the audit log for compliance and intrusion detection:

```php
// In AuthController / Filament auth hooks
class LogAuthenticationEvents
{
    public function handle(Login $event): void
    {
        AuditLogger::log(
            event: 'user.login',
            subject: $event->user,
            causer: $event->user,
            properties: ['ip' => request()->ip(), 'user_agent' => request()->userAgent()],
        );
    }

    public function handleFailed(Failed $event): void
    {
        AuditLogger::log(
            event: 'user.login.failed',
            subject: null,
            causer: null,
            properties: ['email' => $event->credentials['email'], 'ip' => request()->ip()],
        );
    }
}
```

Register in `EventServiceProvider`:
```php
\Illuminate\Auth\Events\Login::class => [LogAuthenticationEvents::class],
\Illuminate\Auth\Events\Failed::class => [LogAuthenticationEvents::class . '@handleFailed'],
\Illuminate\Auth\Events\Logout::class => [LogAuthenticationEvents::class . '@handleLogout'],
```

---

## Data Isolation

See [[architecture/multi-tenancy]] for the full `CompanyScope` implementation.

Critical rule: any query bypassing `CompanyScope` (raw `DB::` queries, `withoutGlobalScope()`) must explicitly add `WHERE company_id = ?`. See [[architecture/patterns/testing-pattern]] for the tenant isolation test pattern.

---

## Security Checklist Per Module

Every new module must pass before merging:

- [ ] `canAccess()` on every resource and page
- [ ] `canViewAny()`, `canCreate()`, `canEdit()`, `canDelete()` implemented on resources
- [ ] Permission string follows `domain.module.action` format
- [ ] Module key checked via `BillingService::hasModule()`
- [ ] All input goes through a Data class — no `$request->all()`
- [ ] Sensitive fields use `encrypted` cast — see [[architecture/patterns/encryption]]
- [ ] Phone numbers validated via `propaganistas/laravel-phone` and stored as E.164
- [ ] File uploads: type whitelist, max size, stored under `companies/{id}/`
- [ ] Rich text stored only after HTMLPurifier (`ezyang/htmlpurifier`)
- [ ] No raw `DB::` queries without explicit `company_id` filter
- [ ] No `withoutGlobalScope()` outside `/admin` panel
- [ ] Rate limiter applied to expensive or sensitive endpoints
- [ ] Stripe webhooks verified before processing
- [ ] Tenant isolation test: company A cannot see company B's data
- [ ] Auth events (login/logout/failed) logged to audit log
