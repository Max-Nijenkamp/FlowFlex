---
type: architecture
category: pattern
color: "#A78BFA"
---

# BelongsToCompany Pattern

Every Eloquent model that stores tenant data must apply three traits. These traits are the foundation of FlowFlex's multi-tenancy and data integrity guarantees.

---

## The Three Required Traits

```php
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasUlids;
    use BelongsToCompany;
    use SoftDeletes;
}
```

Every model with a `company_id` column uses all three. No exceptions.

---

## HasUlids

**What it does**: Replaces the auto-increment integer primary key with a ULID. ULIDs are 26-character, sortable, URL-safe identifiers.

**From Laravel core**: `Illuminate\Database\Eloquent\Concerns\HasUlids` — no custom code required.

**Migration column**: `$table->ulid('id')->primary();`

**Why ULID and not UUID**: UUID4 is random — it has no temporal ordering and causes B-tree index fragmentation at scale. ULID has a timestamp prefix, so new records sort lexicographically after older records. This gives near-integer index performance while preventing sequential enumeration attacks.

**Why ULID and not integer**: integer PKs expose record count and are vulnerable to IDOR enumeration. A user who creates record `id=4` knows there are 3 other records and can guess their IDs.

---

## BelongsToCompany

**What it does**: Registers the `CompanyScope` global scope so every Eloquent query on this model automatically filters by the current company. Also auto-sets `company_id` on create, and provides the `company()` relationship.

**Source**: `App\Support\Traits\BelongsToCompany`

```php
trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function ($model) {
            if (! $model->company_id) {
                $model->company_id = app(CompanyContext::class)->current()->id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
```

**company_id is never nullable**: The column is `NOT NULL` in the migration and never set to null in application code. A model without a `company_id` is an orphaned record and should not exist. Using `?string $company_id` type hints in a model's property cast is wrong — it must be `string`.

**Auto-set on create**: If a model is created without explicitly setting `company_id`, the trait reads it from `CompanyContext`. This prevents accidentally creating records without a company scope when `CompanyContext` is set correctly (which it always is for web requests).

---

## SoftDeletes

**What it does**: Adds `deleted_at` timestamp. Deleted records are excluded from all queries by default. Records can be restored. Hard deletion does not happen through normal application flow.

**From Laravel core**: `Illuminate\Database\Eloquent\SoftDeletes` — no custom code required.

**Migration column**: `$table->softDeletes();`

**Hard delete scenarios** (the only three valid ones):
1. Scheduled purge job — runs 90 days after soft delete, removes records that have no legal hold
2. GDPR erasure flow — the Legal domain's DSAR erasure process anonymises and then hard-deletes records
3. Super-admin explicit action in the `/admin` panel

**Pivot / child models**: soft deletes on pivot tables (many-to-many join tables) and simple child records with no audit requirement may be omitted if the parent's soft delete cascade handles them correctly. Document the decision in code comments when omitting.

---

## Migration Checklist

Every migration for a tenant model must include:

```php
Schema::create('hr_employees', function (Blueprint $table) {
    $table->ulid('id')->primary();                                    // HasUlids
    $table->foreignUlid('company_id')->references('id')->on('companies'); // BelongsToCompany
    
    // ... business columns ...
    
    $table->timestamps();
    $table->softDeletes();                                            // SoftDeletes

    $table->index('company_id');                                      // required index
});
```

The `company_id` index is mandatory. Without it, `CompanyScope`'s `WHERE company_id = ?` filter performs a full table scan.

---

## Common Mistakes

**Missing the trait**: model has `company_id` in the table but no `BelongsToCompany` trait. The global scope is not applied. Queries return records from all companies. This is a critical data leak.

**Nullable company_id**: declaring `?string $company_id` in a model cast or migration allows null. `BelongsToCompany::bootBelongsToCompany()` checks `if (! $model->company_id)` — a null will trigger auto-set, but only if `CompanyContext` is populated. If context is missing (e.g. in a seeder without CompanyContext set), the record is created without a company_id and breaks all subsequent queries.

**withoutGlobalScope outside admin panel**: calling `Employee::withoutGlobalScope(CompanyScope::class)` anywhere outside `app/Filament/Admin/` removes tenant isolation for that query. This is always a bug in the application layer.
