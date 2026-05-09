---
type: architecture
category: data
last_updated: 2026-05-09
---

# Data Architecture

Conventions for database schema, migrations, models, and DTOs.

---

## Migration Ranges

Every domain owns a numeric range. Migrations within a domain must stay in that domain's range. Ranges are assigned in build order (Phase 0 first).

| # | Domain | Range | Phase |
|---|---|---|---|
| 00 | Foundation | `000000–009999` | 0 |
| 01 | Core Platform | `010000–099999` | 1 |
| 02 | HR & People | `100000–149999` | 2/8 |
| 03 | Projects & Work | `150000–199999` | 2/8 |
| 04 | Finance & Accounting | `200000–249999` | 3/6 |
| 05 | CRM & Sales | `250000–299999` | 3/8 |
| 06 | Marketing & Content | `400000–449999` | 5 |
| 07 | Operations | `300000–399999` | 4/5 |
| 08 | Analytics & BI | `450000–499999` | 6 |
| 09 | IT & Security | `500000–549999` | 4/6 |
| 10 | Legal & Compliance | `550000–599999` | 4/7 |
| 11 | E-commerce | `600000–649999` | 4/5 |
| 12 | Communications | `650000–699999` | 5 |
| 13 | Learning & Development | `700000–749999` | 7 |
| 14 | AI & Automation | `750000–799999` | 6 |
| 15 | Community & Social | `800000–849999` | 7 |
| 16 | Workplace & Facility | `850000–869999` | 4/6 |
| 17 | Professional Services (PSA) | `870000–889999` | 5/7 |
| 18 | Product-Led Growth | `890000–909999` | 6/7 |
| 19 | Business Travel | `910000–929999` | 5/7 |
| 20 | ESG & Sustainability | `930000–949999` | 5/6 |
| 21 | Real Estate & Property | `950000–969999` | 6 |
| 22 | Customer Success | `970000–974999` | 5 |
| 23 | Subscription Billing & RevOps | `975000–979999` | 3 |
| 24 | Procurement & Spend Management | `980000–984999` | 3 |
| 25 | Financial Planning & Analysis | `985000–989999` | 4 |
| 26 | Events Management | `990000–994999` | 5 |
| 27 | Document Management | `995000–999999` | 4 |
| 28 | Whistleblowing & Ethics | `1000000–1049999` | 4 |
| 29 | Field Service Management | `1050000–1099999` | 5 |
| 30 | Pricing Management | `1100000–1149999` | 4 |
| 31 | Enterprise Risk Management | `1150000–1199999` | 5 |

> **Note on Core Platform range**: Foundation takes `000000–009999` (companies, users, admins, company_module_subscriptions). Core Platform starts at `010000`. The existing Core Platform module spec files reference `000000–099999` — treat this as the broad domain range; specific module migrations should use `010000+` within that range.

> **Note on range ordering**: Migration ranges are assigned by build phase, not domain number. Domain 07 (Operations, Phase 4) gets range `300000–399999` before domain 06 (Marketing, Phase 5) which gets `400000–449999`. This is intentional — table is sorted by domain number for readability, but ranges follow phase order.

> **Note on sub-range conflicts**: Some module spec files contain sub-ranges that were assigned before the domain-level ranges were finalised. These are reference-only — the domain-level ranges in this table are authoritative. Correct per-module ranges at build time.

---

## Standard Table Schema

Every module table follows this pattern:

```php
Schema::create('hr_employees', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('company_id')->references('id')->on('companies');
    
    // business columns...
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email')->nullable();
    $table->string('status')->default('active');
    
    // audit columns
    $table->foreignUlid('created_by')->nullable()->references('id')->on('users');
    $table->foreignUlid('updated_by')->nullable()->references('id')->on('users');
    
    // standard timestamps
    $table->timestamps();
    $table->softDeletes();

    // indices
    $table->index('company_id');
    $table->unique(['company_id', 'email']); // business uniques always scoped to company
});
```

---

## ULID Keys

All primary keys are ULIDs (`HasUlids` trait):

```php
class Employee extends Model
{
    use HasUlids;
    use BelongsToCompany;
    use SoftDeletes;
    use LogsActivity;
}
```

ULID vs UUID vs integer:
- Sortable (timestamp prefix) → better B-tree index performance
- URL-safe, no sequential enumeration
- 26 chars (shorter than UUID36)

---

## Enum Columns

Use PHP 8.1+ enums backed by strings:

```php
enum EmployeeStatus: string
{
    case Active   = 'active';
    case OnLeave  = 'on_leave';
    case Offboarded = 'offboarded';
}

// In migration
$table->string('status')->default(EmployeeStatus::Active->value);

// In model
protected $casts = [
    'status' => EmployeeStatus::class,
];
```

---

## DTO Pattern (spatie/laravel-data)

DTOs replace both FormRequest (validation) and JsonResource (output):

```php
class EmployeeData extends Data
{
    public function __construct(
        // input validation attributes
        #[Required, StringType, Max(100)]
        public readonly string $first_name,

        #[Required, StringType, Max(100)]
        public readonly string $last_name,

        #[Email]
        public readonly string $email,

        #[Date]
        public readonly CarbonImmutable $start_date,

        // output-only (nullable for creates)
        public readonly ?string $id = null,
        public readonly ?string $company_id = null,
    ) {}

    // Transform from model
    public static function fromModel(Employee $employee): self
    {
        return new self(
            first_name: $employee->first_name,
            last_name: $employee->last_name,
            email: $employee->email,
            start_date: $employee->start_date,
            id: $employee->id,
            company_id: $employee->company_id,
        );
    }
}
```

---

## TypeScript Auto-Generation

spatie/laravel-typescript-transformer reads Data classes and generates TypeScript:

```typescript
// auto-generated: resources/js/types/generated.d.ts
export interface EmployeeData {
    first_name: string;
    last_name: string;
    email: string;
    start_date: string;
    id: string | null;
    company_id: string | null;
}
```

Use in Vue components:

```typescript
import type { EmployeeData } from '@/types/generated'

const props = defineProps<{
    employee: EmployeeData
}>()
```

---

## Soft Deletes

All models use soft deletes. Hard delete only by:
- Super-admin explicit action
- Scheduled purge job (90 days after soft delete)
- GDPR erasure request (through Data Privacy module)

---

## Audit Trail

Spatie Activitylog on every model:

```php
class Employee extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['first_name', 'last_name', 'email', 'status'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Employee was {$eventName}";
    }
}
```

---

## Multi-Currency Data Model

Multi-currency is a **Phase 1 data model concern**, not a Phase 6 UI feature. All money columns must store both the original amount and a base-currency equivalent from day one:

```php
// Every table that stores money amounts follows this pattern:
$table->string('currency', 3)->default('EUR');   // ISO 4217
$table->decimal('amount', 15, 4);                // original currency
$table->decimal('amount_base', 15, 4)->nullable(); // company base currency
$table->decimal('fx_rate', 12, 6)->nullable();   // rate at time of record
```

Rationale: retrofitting currency columns onto 200+ tables in Phase 6 is a high-risk migration. Nullable `amount_base` and `fx_rate` add minimal storage overhead but enable Phase 6 consolidation reporting without schema changes.

`FxRateService` converts on write. `ExchangeRate` table caches daily rates from ECB / OpenExchangeRates.

---

## Related

- [[MOC_Architecture]]
- [[multi-tenancy]]
- [[concept-dto-pattern]]
- [[analytics-data-architecture]] — analytics tier data model
- [[ai-gdpr-data-residency]] — GDPR erasure in analytics tier
