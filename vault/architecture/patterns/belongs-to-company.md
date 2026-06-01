---
type: architecture
category: pattern
color: "#A78BFA"
---

# BelongsToCompany Pattern

Every Eloquent model storing tenant data must apply three traits. No exceptions.

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

---

## HasUlids

26-character, sortable, URL-safe identifiers. From Laravel core — no custom code.

Migration column: `$table->ulid('id')->primary();`

**Why ULID not UUID4**: UUID4 is random, causes B-tree index fragmentation at scale. ULID has a timestamp prefix — near-integer index performance while preventing sequential enumeration attacks.

**Why ULID not integer**: Integer PKs expose record count and are vulnerable to IDOR enumeration.

---

## BelongsToCompany

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

`company_id` is **never nullable** — `NOT NULL` in migration, never `?string` in model.

---

## SoftDeletes

`deleted_at` timestamp. Deleted records excluded from all queries. Records are restorable.

Hard delete only happens in:
1. Scheduled purge job — 90 days after soft delete
2. GDPR erasure flow — Legal domain DSAR process
3. Super-admin explicit action in `/admin`

---

## Migration Template

```php
Schema::create('hr_employees', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('company_id')->references('id')->on('companies');

    // ... business columns ...

    $table->timestamps();
    $table->softDeletes();

    $table->index('company_id');  // mandatory — CompanyScope does a WHERE on this
});
```

---

## Common Mistakes

**Missing the trait**: model has `company_id` in the table but no `BelongsToCompany`. Scope not applied. Queries return records from all companies. Critical data leak.

**Nullable `company_id`**: `?string $company_id` allows null. Auto-set on create only works if `CompanyContext` is set — in seeders without context, record is created without a company and breaks all subsequent queries.

**`withoutGlobalScope` outside admin panel**: Always a bug in the application layer. See [[architecture/multi-tenancy]].
