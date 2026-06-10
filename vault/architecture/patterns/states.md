---
type: architecture
category: patterns
pattern-key: states
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# State Machines (spatie/laravel-model-states)

Use for any field that has constrained transitions — status fields where moving from state A to state B is only valid under certain conditions. Replaces raw string enums with enforced transition rules.

---

## Transition Auditing

Every state transition is recorded via `spatie/laravel-activitylog` — who, when, from, to. Implement once in the transition base class, not per model:

```php
abstract class Transition extends \Spatie\ModelStates\Transition
{
    protected function logTransition(Model $model, string $from, string $to): void
    {
        activity('state-transition')
            ->performedOn($model)
            ->causedBy(auth()->user())          // null in jobs — WithCompanyContext sets no user
            ->withProperties(['from' => $from, 'to' => $to])
            ->log(class_basename($model) . " {$from} → {$to}");
    }
}
```

Rules:
- The log records `from`/`to` and field name — never additional model PII in `properties` ([[architecture/data-lifecycle]])
- Concurrent-transition safety: wrap transition + side effects in `DB::transaction()` with `lockForUpdate()` on the row — second writer re-reads state and gets `InvalidStateTransitionException` rather than double-firing events
- Transition history surfaces in Filament view pages via the activitylog timeline (`rmsramos/activitylog`)

---

## When to Use

Use `spatie/laravel-model-states` when:
- A field has multiple values AND not all transitions between them are valid
- You need to run code when a specific transition happens (e.g. send email on approval)
- You need to prevent invalid transitions at the model level, not just the controller

Do NOT use for:
- Simple boolean flags (`is_active`) — just use a boolean
- Fields with no transition logic (`color`, `name`) — just use a string

---

## Modules Using States

| Module | Model | Field | States |
|---|---|---|---|
| HR Leave | `LeaveRequest` | `status` | `draft → submitted → approved | rejected | cancelled` |
| HR Employment | `Employee` | `status` | `active → on_leave | suspended | terminated` |
| HR Payroll | `PayrollRun` | `status` | `draft → processing → approved → archived` |
| Finance | `Invoice` | `status` | `draft → sent → partially_paid → paid → overdue | voided` |
| Finance | `Expense` | `status` | `draft → submitted → approved | rejected → reimbursed` |
| CRM | `Deal` | `status` | `open → won | lost` |
| CRM | `Quote` | `status` | `draft → sent → accepted | declined | expired` |
| Support | `Ticket` | `status` | `open → in_progress → resolved | closed` |

---

## Implementation Pattern

### 1. Define State Classes

```php
// app/States/HR/LeaveRequest/LeaveRequestStatus.php
namespace App\States\HR\LeaveRequest;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class LeaveRequestStatus extends State
{
    abstract public function label(): string;
    abstract public function color(): string; // for Filament badge colour

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, Submitted::class)
            ->allowTransition(Submitted::class, Approved::class)
            ->allowTransition(Submitted::class, Rejected::class)
            ->allowTransition([Submitted::class, Approved::class], Cancelled::class);
    }
}
```

### 2. Define Concrete States

```php
// app/States/HR/LeaveRequest/Submitted.php
class Submitted extends LeaveRequestStatus
{
    public static string $name = 'submitted';
    public function label(): string { return 'Submitted'; }
    public function color(): string { return 'warning'; }
}

// app/States/HR/LeaveRequest/Approved.php
class Approved extends LeaveRequestStatus
{
    public static string $name = 'approved';
    public function label(): string { return 'Approved'; }
    public function color(): string { return 'success'; }
}
```

### 3. Register on Model

```php
// app/Models/HR/LeaveRequest.php
use Spatie\ModelStates\HasStates;

class LeaveRequest extends Model
{
    use HasUlids, BelongsToCompany, SoftDeletes, HasStates;

    protected $casts = [
        'status' => LeaveRequestStatus::class,
    ];
}
```

### 4. Transition in Service / Action

```php
// app/Actions/HR/ApproveLeaveRequest.php
class ApproveLeaveRequest
{
    use AsAction;

    public function handle(LeaveRequest $request, User $approvedBy): void
    {
        $request->status->transitionTo(Approved::class);

        $request->update([
            'approved_by' => $approvedBy->id,
            'approved_at' => now(),
        ]);

        Mail::to($request->employee->email)->queue(new LeaveApprovedMail($request));
        event(new LeaveRequestApproved(
            company_id: $request->company_id,
            leave_request_id: $request->id,
        ));
    }
}
```

`transitionTo()` throws `InvalidTransition` if the transition is not allowed. Catch it at the controller level:

```php
// In Filament action
->action(function (LeaveRequest $record) {
    try {
        ApproveLeaveRequest::run($record, auth()->user());
    } catch (\Spatie\ModelStates\Exceptions\InvalidTransition $e) {
        Notification::make()->danger()->title('Cannot approve')->send();
    }
})
```

---

## Filament Integration

### Status Badge Column

```php
TextColumn::make('status')
    ->badge()
    ->formatStateUsing(fn ($state) => $state->label())
    ->color(fn ($state) => $state->color());
```

### Filter by State

```php
SelectFilter::make('status')
    ->options([
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ]);
```

### Transition Actions

```php
Tables\Actions\Action::make('approve')
    ->visible(fn ($record) => $record->status->canTransitionTo(Approved::class))
    ->action(fn ($record) => ApproveLeaveRequest::run($record, auth()->user()))
    ->requiresConfirmation();
```

`canTransitionTo()` checks whether the transition is allowed per the state config — use it to conditionally show action buttons.

---

## Database Column

State values stored as strings in the database. Column definition:

```php
$table->string('status')->default('draft');
```

No enum column type — string is more portable and allows the state class to define the value via `$name`.

---

## File Location

```
app/States/
└── {Domain}/
    └── {Model}/
        ├── {ModelName}Status.php   ← abstract base
        ├── Draft.php
        ├── Submitted.php
        ├── Approved.php
        └── Rejected.php
```
