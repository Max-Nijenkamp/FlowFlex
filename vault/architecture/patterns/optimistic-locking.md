---
type: architecture
category: pattern
pattern-key: locking
status: stable
last-reviewed: 2026-07-02
color: "#A78BFA"
---

# Optimistic Locking — Concurrent Edit Standard

The platform default for protecting concurrent edits, authorised by [[decisions/decision-2026-07-02-optimistic-locking-standard]]. Every module spec states which tier each write path uses in its `## Concurrency` section.

---

## The Problem

Two people open the same record, both edit, both save. Without a guard the second save silently overwrites the first — last-write-wins data loss, no error, no trace. FlowFlex makes this worse than usual: domains are heavily linked, so the *same* record is reachable from several surfaces at once. A deal edited from its resource form while the pipeline board drags it to a new stage; an employee profile edited by HR while self-service updates an address; an invoice edited while a payment posts against it. The audit found this protection almost entirely undocumented across 172 modules — this pattern is the fix.

---

## Three-Tier Decision Table

Pick the tier per **write path**, not per module — one model can use optimistic for its CRUD form and pessimistic for a state transition.

| Tier | Mechanism | Use for |
|---|---|---|
| **Optimistic** (default) | `updated_at` stale-check on save → `StaleRecordException` → conflict notification with a **Reload record** action | All ordinary CRUD edits — Filament forms, Livewire actions, API `PATCH`/`PUT` |
| **Pessimistic** | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write | State-machine transitions ([[architecture/patterns/states]]), money mutations (payments, journal postings, payroll), inventory / capacity decrements (stock, tickets, room slots) |
| **Document locks** | Explicit checkout / checkin rows (`dms_document_locks`) | DMS versioned documents only |

The stale-check uses `updated_at` — already on every table, no new version column. *(A dedicated `lock_version` integer may be introduced later by ADR if `updated_at` second-precision collisions ever surface.)*

---

## Mechanism — Optimistic

The record's `updated_at` is captured when the form or DTO is loaded, then compared against the current DB value inside the write transaction. A mismatch means someone else saved in the meantime — abort, never merge.

### The exception

Platform-level, not per-domain — lives in `app/Exceptions/StaleRecordException.php` alongside the other cross-cutting exceptions ([[architecture/error-handling]]).

```php
// app/Exceptions/StaleRecordException.php
namespace App\Exceptions;

class StaleRecordException extends \RuntimeException
{
    public function __construct(
        public readonly string $model,
        public readonly string $recordId,
    ) {
        parent::__construct("Record {$model}:{$recordId} was modified by another writer.");
    }
}
```

### Shared trait for Filament forms

Edit forms carry the loaded timestamp in a hidden field and compare it before save. The comparison lives in one trait so every resource guards the same way.

```php
// app/Support/Traits/ChecksStaleRecords.php
namespace App\Support\Traits;

use App\Exceptions\StaleRecordException;
use Filament\Forms;

trait ChecksStaleRecords
{
    /** Add to the form schema of any editable resource. */
    protected static function staleGuardField(): Forms\Components\Hidden
    {
        // Populated from the record at form fill; travels with the submit.
        return Forms\Components\Hidden::make('_loaded_at')
            ->dehydrated(false);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $loadedAt = $this->data['_loaded_at'] ?? null;

        if ($loadedAt && $this->record->updated_at->ne($loadedAt)) {
            throw new StaleRecordException(
                model: class_basename($this->record),
                recordId: $this->record->getKey(),
            );
        }

        return $data;
    }
}
```

The hidden field is filled from the record when the Edit page mounts:

```php
// In the Edit page
protected function fillForm(): void
{
    parent::fillForm();
    $this->data['_loaded_at'] = $this->record->updated_at?->toIso8601String();
}
```

### Service / API layer

Input DTOs carry `loaded_at`; the service compares before writing, inside the same transaction as the update so no writer slips between the check and the save.

```php
// app/Data/CRM/UpdateDealData.php
class UpdateDealData extends Data
{
    public function __construct(
        public string $name,
        public int $value,          // cents
        public CarbonImmutable $loaded_at,   // captured when the client loaded the record
    ) {}
}

// In the service
public function update(Deal $deal, UpdateDealData $data): Deal
{
    return DB::transaction(function () use ($deal, $data) {
        $fresh = Deal::whereKey($deal->getKey())->lockForUpdate()->firstOrFail();

        if ($fresh->updated_at->ne($data->loaded_at)) {
            throw new StaleRecordException('Deal', $fresh->getKey());
        }

        $fresh->update(['name' => $data->name, 'value' => $data->value]);

        return $fresh;
    });
}
```

For an API `PATCH`, `StaleRecordException` maps to **HTTP 409 Conflict** in the global handler ([[architecture/error-handling]]) — the client re-fetches and retries.

---

## Conflict UX

The second writer always sees the conflict; the first write always survives. Copy is human, never exception text, per [[architecture/patterns/ux-states]] §2.

```php
// Catch in the Filament action / page
catch (StaleRecordException $e) {
    Notification::make()
        ->danger()
        ->title('This record was changed by someone else')
        ->body('Review the changes and try again — your edits were not saved.')
        ->persistent()
        ->actions([
            Action::make('reload')
                ->label('Reload record')
                ->button()
                ->action(fn () => $this->fillForm()),   // refresh form state from DB
        ])
        ->send();
}
```

Rules:
- **Never silently merge.** The user re-reads current state, then re-applies their change.
- **First write wins**, always. The conflict is surfaced to whoever saved second.
- No exception message, no status code, no field diff dump — the **Reload record** action does the recovery, the copy just explains it.

---

## What It Does NOT Cover

- **Cross-domain writes** — these go through events, which give a single writer per table ([[architecture/event-bus]]). No two domains write the same row concurrently, so there is nothing to stale-check.
- **Derived / read-only data** — projections, cached aggregates, report views. They are rebuilt from their source, never edited in place.
- **Batch imports** — `maatwebsite/laravel-excel` bulk upserts. Last import wins is acceptable and intended; do not stale-check per row. Note this explicitly in the module's `## Concurrency` section so it reads as a decision, not an oversight.

---

## Testing Recipe

Two tests per guarded write path: the service-level stale-check, and the Livewire notification.

```php
// Service level — second writer is rejected, first write survives
it('rejects a stale update and leaves the record untouched', function () {
    $deal = Deal::factory()->create(['name' => 'Original']);
    $loadedAt = $deal->updated_at;

    // Simulate a second writer committing in between
    $this->travel(1)->second();
    Deal::whereKey($deal->id)->update(['name' => 'Won by first writer']);

    $data = UpdateDealData::from([
        'name' => 'Won by second writer',
        'value' => 500000,
        'loaded_at' => $loadedAt,
    ]);

    expect(fn () => app(DealService::class)->update($deal, $data))
        ->toThrow(StaleRecordException::class);

    expect($deal->fresh()->name)->toBe('Won by first writer');
});

// Livewire — the conflict notification renders
it('shows the conflict notification when the record is stale', function () {
    $deal = Deal::factory()->create();

    livewire(EditDeal::class, ['record' => $deal->getKey()])
        ->fillForm(['name' => 'My edit', '_loaded_at' => now()->subMinute()->toIso8601String()])
        ->call('save')
        ->assertNotified('This record was changed by someone else');
});
```

---

## Related

- [[decisions/decision-2026-07-02-optimistic-locking-standard]] — the authorising ADR
- [[architecture/patterns/states]] — pessimistic transition locking (tier 2)
- [[architecture/error-handling]] — `StaleRecordException` in the platform exception set, 409 mapping
- [[architecture/patterns/ux-states]] — §2 error-copy voice
- [[architecture/patterns/filament-resource-checklist]] — edit forms carry the stale-record guard
- [[_meta/spec-template]] — the `## Concurrency` section every spec must fill
