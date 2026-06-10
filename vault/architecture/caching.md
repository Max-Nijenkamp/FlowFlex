---
type: architecture
category: infra
pattern-key: caching
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Caching Strategy

Redis backs all caching in FlowFlex. Three categories: application data cache, session store, and rate limiter state. This file documents what to cache, cache keys, TTLs, and invalidation rules.

---

## Redis Configuration

```php
// config/cache.php
'default' => 'redis',

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

Separate Redis connections for cache (`DB 1`), sessions (`DB 2`), queues (`DB 3`), rate limiters (`DB 4`). Keeps data types isolated and allows independent flushing.

---

## What to Cache and Why

### 1. Module Subscriptions (CRITICAL)

`BillingService::hasModule()` is called on every `canAccess()` check — potentially 20+ times per page load. Without caching, this is 20 queries per page.

```php
class BillingService
{
    public function hasModule(string $moduleKey): bool
    {
        $companyId = app(CompanyContext::class)->currentId();

        return Cache::remember(
            "company:{$companyId}:modules",
            now()->addMinutes(5),
            fn () => CompanyModuleSubscription::active()
                ->pluck('module_key')
                ->toArray()
        )
        |> fn ($modules) => in_array($moduleKey, $modules);
    }
}
```

**Invalidate** when: module activated, module deactivated.

```php
Cache::forget("company:{$companyId}:modules");
```

**TTL**: 5 minutes. Short enough that activation takes effect quickly; long enough to remove the N+1 risk.

---

### 2. Company Settings

`spatie/laravel-settings` has its own caching layer — enable it:

```php
// config/settings.php
'cache' => true,
'cache_prefix' => 'settings.',
```

Settings are invalidated automatically by Spatie when updated. Manual invalidation:

```php
app(CompanyLocaleSettings::class)->fresh(); // re-read from DB
```

**TTL**: 10 minutes (Spatie default).

---

### 3. Spatie Permission (Roles & Permissions)

Spatie Permission has built-in caching. Configure in `config/permission.php`:

```php
'cache' => [
    'expiration_time' => DateInterval::createFromDateString('1 hour'),
    'key' => 'spatie.permission.cache',
    'store' => 'redis',
],
```

Invalidate after role/permission changes (Spatie does this automatically when using `syncPermissions()` or `assignRole()`).

**TTL**: 1 hour. Safe because permission changes are low-frequency.

---

### 4. Meilisearch Query Results

Do **not** cache Meilisearch results in the application layer. Meilisearch handles its own internal index caching. Application-layer caching of search results creates stale results when records are updated.

---

### 5. Expensive Reports

Financial reports (P&L, balance sheet, AR aging) that aggregate large date ranges should be cached:

```php
$cacheKey = "company:{$companyId}:report:pl:{$yearMonth}";

return Cache::remember($cacheKey, now()->addHours(1), fn () =>
    $this->financeService->generatePL($yearMonth)
);
```

**Invalidate** when: a new journal entry is posted for that period.

**TTL**: 1 hour for historical periods; do not cache current-period reports (too volatile).

---

### 6. Dashboard Widgets

Filament dashboard widgets that aggregate stats (headcount, pipeline value, monthly revenue) should cache their queries:

```php
class HeadcountWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $companyId = app(CompanyContext::class)->currentId();

        $count = Cache::remember(
            "company:{$companyId}:hr:headcount",
            now()->addMinutes(15),
            fn () => Employee::where('status', 'active')->count()
        );

        return [Stats::make('Active Employees', $count)];
    }
}
```

**TTL**: 15 minutes. Stale headcount on a dashboard is acceptable; stale data on a form is not.

---

## Cache Key Convention

```
company:{company_id}:{domain}:{resource}[:{qualifier}]
```

Examples:
- `company:01ARZ...:modules` — active module list
- `company:01ARZ...:hr:headcount` — employee headcount
- `company:01ARZ...:finance:pl:2026-05` — May 2026 P&L report
- `company:01ARZ...:crm:pipeline-value` — total pipeline value

Always prefix with `company:{id}` — never cache data without a tenant scope.

---

## What NOT to Cache

| Data | Reason |
|---|---|
| Individual model fetches by ID | Eloquent is fast enough; caching creates stale-write bugs |
| Form data during editing | Must always be fresh |
| Paginated table results | Changes too often; cache hit rate would be low |
| File download URLs | Use S3 pre-signed URLs with their own TTL |
| Anything involving money real-time | Invoice payment status, bank balance |

---

## Cache Invalidation Rules

| Event | Invalidate |
|---|---|
| Module activated or deactivated | `company:{id}:modules` |
| Company settings saved | Spatie settings cache (automatic) |
| Role or permission changed | Spatie permission cache (automatic) |
| Employee hired or terminated | `company:{id}:hr:headcount` |
| Journal entry posted | `company:{id}:finance:pl:{period}` |
| Deal stage changed | `company:{id}:crm:pipeline-value` |

### Cascading Invalidation

Rules for writes that affect cached aggregates beyond their own model:

1. **The writer busts, not the reader.** Whichever service/action mutates the source data is responsible for `Cache::forget()` of every derived key — listed in the module spec's `## Caching` table under "Invalidated by". Readers never check freshness.
2. **Bust by exact key, not by pattern.** No `KEYS`/`SCAN` wildcard deletes in request paths. If a write invalidates a period-keyed family (e.g. P&L per month), the spec must bound the family (current + affected period only).
3. **Cross-domain busts go through listeners.** Domain A's write never busts Domain B's keys directly — the queued listener consuming the event does it (e.g. `PostPayrollJournalEntryListener` busts `finance:pl:{period}` after posting).
4. **Parent-rename rule.** Renames of referenced entities (company name, employee name, stage name) do NOT bust aggregate caches — aggregates store IDs + numbers, display names resolve at render. If a cached payload embeds display names, that's a spec bug.
5. **TTL is the safety net, not the mechanism.** Every key has a TTL even when explicitly invalidated — a missed bust self-heals within the TTL window. Max TTL anywhere: 1 hour.

---

## Session Caching

Sessions use Redis (`DB 2`). Configured in `config/session.php`:

```php
'driver' => 'redis',
'connection' => 'sessions',
'lifetime' => 120, // minutes of inactivity
'secure' => true,   // HTTPS only
'http_only' => true, // no JS access
'same_site' => 'lax',
```

---

## Queue Caching

Queue jobs use Redis (`DB 3`) via Horizon. See [[architecture/queue-jobs]].

---

## Rate Limiter State

Rate limiter state uses Redis (`DB 4`). Stored automatically by Laravel's `RateLimiter` facade. No manual management needed.
