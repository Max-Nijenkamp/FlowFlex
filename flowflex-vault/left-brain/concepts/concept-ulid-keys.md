---
type: concept
category: data
last_updated: 2026-05-09
---

# Concept: ULID Primary Keys

---

## Rule

Every table uses a ULID as its primary key. No auto-increment integers anywhere in the application schema.

---

## What Is a ULID

ULID stands for Universally Unique Lexicographically Sortable Identifier. It is a 26-character string composed of a 10-character timestamp prefix and a 16-character random suffix.

Example: `01HXYZ3M4K7P8T9QRSVW2JNBFD`

Properties:
- **Lexicographically sortable**: the timestamp prefix means ULIDs sort in creation order, just like integer IDs.
- **URL-safe**: no special characters, no hyphens required.
- **Case-insensitive**: `01HXYZ...` and `01hxyz...` are the same identifier.
- **26 characters**: shorter than a UUID (36 characters with hyphens).
- **Millisecond precision**: the timestamp is encoded to the millisecond.

---

## Why Over UUID

| Property | ULID | UUID v4 |
|---|---|---|
| Length | 26 chars | 36 chars (with hyphens) |
| Sortable | Yes (time-ordered) | No (random) |
| B-tree index performance | Good (monotonic inserts) | Poor (random page splits) |
| URL-safe without encoding | Yes | Yes (with hyphens stripped) |
| Time-ordered | Yes | No |

The sortability is the critical advantage. UUID v4 causes B-tree index fragmentation on high-volume tables because each insert lands at a random position. ULIDs insert near the end of the index (newest timestamp), matching integer ID behaviour for insert performance.

---

## Why Over Integer

| Property | ULID | Integer |
|---|---|---|
| Sequential enumeration attack | Not possible | Trivial (`id=1,2,3`) |
| Safe to expose in URLs | Yes | No |
| Distributed generation | Yes (no DB round-trip) | No (requires DB sequence) |
| Globally unique | Yes | Only within one table |

Integers in URLs (`/invoices/1042`) allow competitors or attackers to enumerate records by incrementing the ID. ULIDs in URLs (`/invoices/01HXYZ3M4K7P8T9QRSVW2JNBFD`) reveal nothing about the total record count or creation order.

---

## Migration Pattern

```php
Schema::create('employees', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('company_id')->references('id')->on('companies');
    $table->foreignUlid('department_id')->nullable()->references('id')->on('departments');
    // ... columns ...
    $table->timestamps();
    $table->softDeletes();
});
```

Use `$table->foreignUlid()` for all foreign keys that reference ULID primary keys. This sets the correct column type and creates the foreign key constraint.

---

## Model Pattern

```php
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Employee extends Model
{
    use HasUlids; // auto-generates ULID on the 'creating' Eloquent event

    public $incrementing = false;
    protected $keyType = 'string';
}
```

`HasUlids` is a Laravel built-in trait (available since Laravel 10). Do not use a custom ULID generator package — use the built-in trait. It handles ULID generation automatically before `INSERT`, so there is no need to set `$model->id` manually.

---

## Important Notes

- `$incrementing = false` is required to prevent Eloquent from trying to cast the key to an integer after insert.
- `$keyType = 'string'` is required so Eloquent does not cast the key to int during retrieval.
- Both flags are always required alongside `HasUlids`.
- Seeder factories: `Employee::factory()` works normally — `HasUlids` fires on the `creating` event, which factory `create()` triggers.

---

## Related

- `[[data-architecture]]` — overall data model conventions including ULID, soft deletes, and multi-tenancy column patterns
