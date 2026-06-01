---
type: architecture
category: pattern
color: "#A78BFA"
---

# Actions Pattern (lorisleiva/laravel-actions)

Use for single-step operations that do not need a full Interface→Service setup. See [[architecture/patterns/interface-service]] for when to use that instead.

---

## When to Use Actions

| Signals to use an Action | Signals to use Interface→Service |
|---|---|
| Single operation, one method | Multiple methods on the same service |
| Called from 1–2 places | Multiple callers needing the same interface |
| No alternative implementation needed | Testable swappable implementation needed |
| Simple, self-contained | Cross-domain dependency |
| e.g. "send welcome email", "mark as read" | e.g. `EmployeeService`, `InvoiceService` |

---

## File Location

```
app/Actions/{Domain}/{ActionName}.php
```

Examples:
- `app/Actions/HR/SendWelcomeEmail.php`
- `app/Actions/Core/DeactivateModule.php`
- `app/Actions/Finance/RecalculateInvoiceTotals.php`

---

## Basic Action

```php
namespace App\Actions\HR;

use App\Models\HR\Employee;
use Lorisleiva\Actions\Concerns\AsAction;

class SendWelcomeEmail
{
    use AsAction;

    public function handle(Employee $employee): void
    {
        Mail::to($employee->email)->send(new WelcomeEmail($employee));
    }
}
```

Call from anywhere:
```php
SendWelcomeEmail::run($employee);
```

---

## Action as Filament Action

`AsAction` makes the class usable as a controller, job, listener, and Filament action simultaneously:

```php
// As a queued job
SendWelcomeEmail::dispatch($employee);

// As a Livewire/Filament action
Action::make('send_welcome')
    ->action(fn (Employee $employee) => SendWelcomeEmail::run($employee))
```

---

## Action with Validation

For actions that need input validation (usable as a controller endpoint):

```php
namespace App\Actions\Core;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeactivateModule
{
    use AsAction;

    public function rules(): array
    {
        return [
            'module_key' => ['required', 'string', 'exists:module_catalog,module_key'],
        ];
    }

    public function handle(string $module_key): void
    {
        CompanyModuleSubscription::where('module_key', $module_key)
            ->update(['deactivated_at' => now()]);
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->validated('module_key'));
        return redirect()->back()->with('success', 'Module deactivated.');
    }
}
```

---

## Testing Actions

Test the `handle()` method directly — no mocking needed:

```php
it('sends a welcome email to the employee', function () {
    Mail::fake();

    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);

    $employee = Employee::factory()->for($company)->create();

    SendWelcomeEmail::run($employee);

    Mail::assertSent(WelcomeEmail::class, fn ($mail) => $mail->hasTo($employee->email));
});
```

---

## Rules

1. Actions live in `app/Actions/{Domain}/` — not in `Services/`
2. One action = one operation. If you find yourself adding a second `public function handle()` variant, extract a second action
3. Actions may call other actions for composition
4. Actions may dispatch events — fire the event inside `handle()`, not in a controller or Livewire component
5. Actions must NOT call other actions from the same domain's service if that service is Interface→Service — call the service interface instead
