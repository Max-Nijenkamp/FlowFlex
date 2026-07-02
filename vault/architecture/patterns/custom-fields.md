---
type: architecture
category: patterns
pattern-key: custom-fields
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Custom Fields (Per-Company Schemaless Attributes)

Companies add their own fields to records (CRM contacts first; later Support tickets, Projects tasks) without migrations. Implemented with `spatie/laravel-schemaless-attributes` on a JSONB column + a per-company field-definition table.

**Scope rule**: custom fields are for company-specific *extra* data. Anything FlowFlex logic reads belongs in a real column — never branch business logic on a custom field.

---

## Storage Model

Two pieces:

### 1. Field definitions — `{domain}_custom_field_definitions`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | PK | |
| company_id | ulid | not null, indexed | BelongsToCompany |
| entity | string | not null | e.g. `contact` — one definitions table per domain, entity disambiguates |
| key | string | not null | snake_case, immutable after creation |
| label | string | not null | display name |
| field_type | string | not null | `text` \| `number` \| `date` \| `select` \| `multi-select` \| `boolean` |
| options | jsonb | nullable | select/multi-select choices |
| is_required | boolean | default false | |
| sort_order | int | default 0 | form ordering |
| deleted_at | timestamp | nullable | soft delete hides field, values retained |

**Indexes:** `(company_id, entity, key)` unique.

### 2. Values — JSONB column on the host model

```php
// migration on crm_contacts
$table->jsonb('custom_fields')->default('{}');

// model
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Contact extends Model
{
    protected $casts = ['custom_fields' => SchemalessAttributes::class];

    public function scopeWithCustomField(Builder $q, string $key, mixed $value): Builder
    {
        return $q->where("custom_fields->{$key}", $value); // PostgreSQL JSONB operator
    }
}
```

Field creation/edit is a **database operation, not a migration** — owners manage definitions in the panel.

---

## Validation

Build rules dynamically from definitions inside the Data class:

```php
class CreateContactData extends Data
{
    // ...fixed fields...
    public array $custom_fields = [];

    public static function rules(ValidationContext $context): array
    {
        $defs = CustomFieldDefinition::query()
            ->where('entity', 'contact')->get();          // CompanyScope applies

        $rules = [];
        foreach ($defs as $def) {
            $rules["custom_fields.{$def->key}"] = [
                $def->is_required ? 'required' : 'nullable',
                ...match ($def->field_type) {
                    'number' => ['numeric'],
                    'date' => ['date'],
                    'boolean' => ['boolean'],
                    'select' => [Rule::in($def->options ?? [])],
                    'multi-select' => ['array'],
                    default => ['string', 'max:1000'],
                },
            ];
        }
        return $rules;
    }
}
```

Unknown keys (not in definitions) are rejected — strip or 422 *(assumed: strip silently)*.

---

## Filament Rendering

Generate form components from definitions:

```php
public static function customFieldComponents(string $entity): array
{
    return CustomFieldDefinition::where('entity', $entity)
        ->orderBy('sort_order')->get()
        ->map(fn ($def) => match ($def->field_type) {
            'text' => TextInput::make("custom_fields.{$def->key}")->label($def->label),
            'number' => TextInput::make("custom_fields.{$def->key}")->numeric()->label($def->label),
            'date' => DatePicker::make("custom_fields.{$def->key}")->label($def->label),
            'boolean' => Toggle::make("custom_fields.{$def->key}")->label($def->label),
            'select' => Select::make("custom_fields.{$def->key}")->options(array_combine($def->options, $def->options))->label($def->label),
            'multi-select' => Select::make("custom_fields.{$def->key}")->multiple()->options(array_combine($def->options, $def->options))->label($def->label),
        })
        ->map(fn ($c) => $def->is_required ?? false ? $c->required() : $c)
        ->all();
}
```

Wrap in a "Custom Fields" form Section, appended after fixed fields. Table columns/filters: opt-in per definition later — v1 shows custom fields on view/edit only *(assumed)*.

---

## Limits

- **Search**: custom fields are NOT in Meilisearch v1. Filtering = JSONB queries via `scopeWithCustomField` (add GIN index on the jsonb column when a customer hits performance: `$table->index('custom_fields', null, 'gin')`)
- **No type migration**: changing `field_type` after values exist is blocked — create a new field instead
- **Per-entity cap**: 50 custom fields per entity per company *(assumed)*
- **Exports**: custom fields included as extra columns in Excel exports (definitions drive headers)
- **DSAR**: custom field values on person entities count as PII → erased with the record per [[architecture/data-lifecycle]]

---

## Related

- [[architecture/packages]] — spatie/laravel-schemaless-attributes evaluation
- [[domains/crm/contacts/_module]] — first consumer
- [[architecture/patterns/dto-pattern]] — dynamic rules
