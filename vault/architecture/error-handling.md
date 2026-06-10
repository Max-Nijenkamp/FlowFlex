---
type: architecture
category: quality
pattern-key: errors
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Error Handling

Custom exception classes, global exception handler, API error responses, and Filament error display.

---

## Custom Exception Classes

One exception class per distinct failure reason. Lives in `app/Exceptions/{Domain}/`.

```php
// app/Exceptions/HR/LeaveBalanceExceededException.php
namespace App\Exceptions\HR;

class LeaveBalanceExceededException extends \RuntimeException
{
    public function __construct(
        public readonly int $requestedDays,
        public readonly int $availableDays,
    ) {
        parent::__construct("Requested {$requestedDays} days but only {$availableDays} available.");
    }
}

// app/Exceptions/Core/ModuleNotActiveException.php
class ModuleNotActiveException extends \RuntimeException
{
    public function __construct(string $moduleKey)
    {
        parent::__construct("Module '{$moduleKey}' is not active for this company.");
    }
}

// app/Exceptions/Core/MissingCompanyContextException.php
class MissingCompanyContextException extends \RuntimeException {}
```

**Rule**: throw a specific exception, never a generic `\Exception('something went wrong')`. Specific exceptions allow the handler to produce the correct response.

---

## Global Exception Handler

`app/Exceptions/Handler.php` — override `render()` to control API vs web responses:

```php
public function render($request, Throwable $e): Response
{
    // API requests: always return JSON
    if ($request->expectsJson() || $request->is('api/*')) {
        return $this->renderApiException($request, $e);
    }

    // Filament / web requests: let Laravel/Filament handle
    return parent::render($request, $e);
}

private function renderApiException(Request $request, Throwable $e): JsonResponse
{
    return match (true) {
        $e instanceof ValidationException => response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $e->errors(),
        ], 422),

        $e instanceof ModelNotFoundException => response()->json([
            'message' => 'Resource not found.',
        ], 404),

        $e instanceof AuthenticationException => response()->json([
            'message' => 'Unauthenticated.',
        ], 401),

        $e instanceof AuthorizationException => response()->json([
            'message' => 'This action is unauthorized.',
        ], 403),

        $e instanceof InvalidTransition => response()->json([
            'message' => 'Invalid status transition: ' . $e->getMessage(),
        ], 422),

        $e instanceof LeaveBalanceExceededException => response()->json([
            'message' => $e->getMessage(),
            'requested_days' => $e->requestedDays,
            'available_days' => $e->availableDays,
        ], 422),

        $e instanceof ModuleNotActiveException => response()->json([
            'message' => $e->getMessage(),
        ], 403),

        // Everything else in production: generic 500
        app()->environment('production') => response()->json([
            'message' => 'Server error.',
        ], 500),

        // In dev: show full exception
        default => response()->json([
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        ], 500),
    };
}
```

---

## Filament Error Display

Filament uses Livewire — exception handling is different from HTTP controllers.

### Validation Errors in Forms

Filament form validation happens via `spatie/laravel-data` rules. Validation errors automatically populate form field error states. No manual try/catch needed for validation.

### Business Logic Errors (non-validation)

For caught business exceptions in Filament actions:

```php
Tables\Actions\Action::make('approve')
    ->action(function (LeaveRequest $record) {
        try {
            ApproveLeaveRequest::run($record, auth()->user());
            Notification::make()->success()->title('Leave approved')->send();
        } catch (InvalidTransition $e) {
            Notification::make()
                ->danger()
                ->title('Cannot approve')
                ->body('Leave request is not in a state that can be approved.')
                ->send();
        } catch (LeaveBalanceExceededException $e) {
            Notification::make()
                ->danger()
                ->title('Insufficient leave balance')
                ->body("Only {$e->availableDays} days available.")
                ->send();
        }
    });
```

**Rule**: Filament actions always show a `Notification` on error — never let an exception propagate to a blank page.

### Unhandled Livewire Exceptions

For truly unexpected exceptions in Livewire components, Laravel renders a 500 page. In production, Sentry captures it. The user sees a generic "Something went wrong" message.

Configure Livewire to handle errors gracefully:

```php
// config/livewire.php
'throw_on_aborting_static_component_method_calls' => false,
```

---

## HTTP Error Pages

Custom Blade views for HTTP error codes (rendered by Inertia for Vue pages):

```
resources/views/errors/
├── 404.blade.php    ← Not found
├── 403.blade.php    ← Forbidden (module not active, no permission)
├── 500.blade.php    ← Server error
└── 503.blade.php    ← Maintenance mode
```

For Inertia pages, errors should render as Vue components:

```php
// Inertia error handling in HandleInertiaRequests or middleware
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'errors' => fn () => $request->session()->get('errors')
            ? $request->session()->get('errors')->getBag('default')->getMessages()
            : (object) [],
    ];
}
```

---

## Logging

All exceptions are logged. Configure channels in `config/logging.php`:

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'FlowFlex Logger',
        'emoji' => ':boom:',
        'level' => 'warning', // only warnings and above go to Slack
    ],
],
```

Production: `LOG_LEVEL=warning`. Errors above warning → Slack `#platform-alerts`.

Dev: `LOG_LEVEL=debug`. All logs → local file only.

---

## What NOT to Do

```php
// Wrong — swallows error silently
try {
    $service->doSomething();
} catch (\Throwable $e) {
    // nothing
}

// Wrong — generic exception loses context
throw new \Exception('Something went wrong');

// Wrong — shows stack trace to users in Filament action
->action(fn ($record) => $service->doSomething($record));
// If doSomething() throws, Livewire renders a 500 with no user feedback

// Correct — specific exception, caught and surfaced
try {
    ApproveLeaveRequest::run($record, auth()->user());
    Notification::make()->success()->title('Approved')->send();
} catch (InvalidTransition | LeaveBalanceExceededException $e) {
    Notification::make()->danger()->title($e->getMessage())->send();
}
```
