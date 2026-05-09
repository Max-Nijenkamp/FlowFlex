---
tags: [flowflex, architecture, error-handling, exceptions, inertia, phase/1]
domain: Platform
status: planned
last_updated: 2026-05-08
---

# Error Handling

Every error in FlowFlex has a designed response. No raw stack traces to users, no silent failures. Inertia pages, Filament panels, and API endpoints each have appropriate handling.

---

## Error Layers

```
┌─────────────────────────────────────────────────────────┐
│  Global Exception Handler (bootstrap/app.php)            │
│  ↓ catches all uncaught exceptions                       │
├─────────────────────────────────────────────────────────┤
│  Inertia Middleware — wraps all Inertia responses        │
│  ↓ handles connection loss, version mismatch             │
├─────────────────────────────────────────────────────────┤
│  Filament Exception Handler — panel-specific rendering   │
├─────────────────────────────────────────────────────────┤
│  Vue Error Boundary — catches Vue component errors       │
│  ↓ shows friendly fallback, reports to error tracker     │
└─────────────────────────────────────────────────────────┘
```

---

## HTTP Error Pages

Custom error pages for all standard error codes. All pages use the FlowFlex design system.

### Registration (bootstrap/app.php)

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withExceptions(function (Exceptions $exceptions) {

        // Render Inertia error pages for HTTP exceptions
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403, 429, 419])) {
                return Inertia::render('Errors/Error', [
                    'status' => $response->getStatusCode(),
                ])
                ->toResponse($request)
                ->setStatusCode($response->getStatusCode());
            }

            // For API requests, always return JSON
            if ($request->expectsJson()) {
                return match ($response->getStatusCode()) {
                    404 => response()->json(['message' => 'Not found.'], 404),
                    403 => response()->json(['message' => 'Forbidden.'], 403),
                    429 => response()->json([
                        'message' => 'Too many requests.',
                        'retry_after' => $response->headers->get('Retry-After'),
                    ], 429),
                    default => response()->json(['message' => 'Server error.'], $response->getStatusCode()),
                };
            }
        });

        // Authentication: redirect to login
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });

        // Validation: return errors to Inertia
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

    })->create();
```

### Error Page Component (Vue)

```vue
<!-- resources/js/pages/Errors/Error.vue -->
<script setup lang="ts">
interface Props {
  status: number
}
const props = defineProps<Props>()

const messages: Record<number, { title: string; description: string }> = {
  404: {
    title: 'Page not found',
    description: "The page you're looking for doesn't exist or has been moved.",
  },
  403: {
    title: 'Access denied',
    description: "You don't have permission to view this page.",
  },
  419: {
    title: 'Session expired',
    description: 'Your session has expired. Please refresh the page and try again.',
  },
  429: {
    title: 'Too many requests',
    description: 'You\'ve made too many requests. Please wait a moment before trying again.',
  },
  500: {
    title: 'Something went wrong',
    description: "We've encountered an unexpected error. Our team has been notified.",
  },
  503: {
    title: 'Down for maintenance',
    description: 'FlowFlex is currently undergoing scheduled maintenance. We\'ll be back shortly.',
  },
}

const error = messages[props.status] ?? messages[500]
</script>

<template>
  <div class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
      <!-- Status code (large, subtle) -->
      <p class="text-8xl font-bold text-slate-200 mb-2">{{ status }}</p>
      <!-- Title -->
      <h1 class="text-h2 text-slate-900 mb-3">{{ error.title }}</h1>
      <!-- Description -->
      <p class="text-body text-slate-600 mb-8">{{ error.description }}</p>
      <!-- Actions -->
      <div class="flex gap-3 justify-center">
        <a href="javascript:history.back()"
           class="btn-ghost">
          Go back
        </a>
        <a :href="route('dashboard')"
           class="btn-primary">
          Go to dashboard
        </a>
      </div>
    </div>
  </div>
</template>
```

---

## Inertia Connection Handling

Inertia detects when the server is unreachable and shows a connection error. Override the default modal with a FlowFlex-branded overlay.

### Inertia Event Listeners (app.ts)

```typescript
// resources/js/app.ts
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'

createInertiaApp({
  // ...

  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)

    // Connection error: show reconnecting overlay
    router.on('invalid', (event) => {
      // Version mismatch (new deployment) → full page reload
      if (event.detail.response.status === 409) {
        event.preventDefault()
        router.reload()
        return
      }
    })

    router.on('error', (event) => {
      // Network errors — handled by progress indicator + toast
      console.error('Inertia error:', event.detail.errors)
    })

    router.on('finish', () => {
      // Dismiss any connection error toasts
    })

    app.mount(el)
  },
})
```

### Offline / Connection Lost Banner

```vue
<!-- resources/js/components/ConnectionStatus.vue -->
<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const isOffline = ref(!navigator.onLine)
const isReconnecting = ref(false)
const retryCount = ref(0)
const MAX_RETRIES = 5

function handleOffline() {
  isOffline.value = true
  startReconnecting()
}

function handleOnline() {
  isOffline.value = false
  isReconnecting.value = false
  retryCount.value = 0
}

function startReconnecting() {
  if (retryCount.value >= MAX_RETRIES) return
  isReconnecting.value = true
  retryCount.value++
  
  const delay = Math.min(1000 * 2 ** retryCount.value, 30000) // exponential backoff, max 30s
  setTimeout(() => {
    if (!navigator.onLine) {
      startReconnecting()
    }
  }, delay)
}

onMounted(() => {
  window.addEventListener('offline', handleOffline)
  window.addEventListener('online', handleOnline)
})

onUnmounted(() => {
  window.removeEventListener('offline', handleOffline)
  window.removeEventListener('online', handleOnline)
})
</script>

<template>
  <!-- Offline banner — slides down from top -->
  <Transition
    enter-from-class="-translate-y-full"
    enter-to-class="translate-y-0"
    leave-from-class="translate-y-0"
    leave-to-class="-translate-y-full"
  >
    <div
      v-if="isOffline"
      class="fixed top-0 inset-x-0 z-50 bg-tide-500 text-white py-2 px-4 flex items-center justify-center gap-2 text-body-sm transition-transform duration-300"
    >
      <template v-if="isReconnecting">
        <svg class="animate-spin h-4 w-4" .../>
        Reconnecting... (attempt {{ retryCount }} of {{ MAX_RETRIES }})
      </template>
      <template v-else>
        No internet connection. Please check your network.
      </template>
    </div>
  </Transition>
</template>
```

### Filament Connection Loss

Filament Livewire components show a native reconnection notice. Override it to match FlowFlex branding:

```css
/* resources/css/filament/admin/theme.css */
/* Override Livewire's default lost-connection notification */
[wire\:loading-delay] .fi-loading-indicator {
  display: none; /* use our own */
}

/* Filament's built-in offline banner — style it */
.fi-offline-indicator {
  background-color: rgb(var(--color-tide-500));
  color: white;
  font-size: 0.875rem;
  padding: 0.5rem 1rem;
  text-align: center;
}
```

---

## Application Exception Hierarchy

Define custom exceptions per layer for precise handling:

```
App\Exceptions\
  ├── Domain\
  │   ├── DomainException.php              (base)
  │   ├── ResourceNotFoundException.php    (404 — known entity not found)
  │   ├── BusinessRuleException.php        (422 — valid request, rule violated)
  │   └── InsufficientPermissionException.php (403)
  ├── Integration\
  │   ├── ExternalServiceException.php     (502 — upstream API failed)
  │   └── PaymentException.php             (payment-specific)
  └── Tenant\
      ├── TenantNotFoundException.php      (404 — tenant/domain resolution fail)
      └── ModuleNotActiveException.php     (403 — module not active for tenant)
```

### Example Business Exception

```php
// App/Exceptions/Domain/BusinessRuleException.php
class BusinessRuleException extends \DomainException
{
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly array $context = [],
    ) {
        parent::__construct($message);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
            'context' => $this->context,
        ], 422);
    }
}

// Usage in service:
throw new BusinessRuleException(
    message: 'Cannot approve leave: employee has insufficient balance.',
    errorCode: 'leave.insufficient_balance',
    context: ['available_days' => 2, 'requested_days' => 5],
);
```

---

## Filament Panel Error Handling

### 500 Pages in Panels

```php
// In all PanelProviders — register custom error page
->renderHook(
    PanelsRenderHook::GLOBAL_SEARCH_END,
    fn () => view('filament.error-boundary'),
)
```

```php
// Filament renders 404 for missing records — customise the message
public static function getRecordRouteKeyName(): string
{
    return 'id'; // use ULID, not auto-increment
}
```

### Filament Notifications for Service Errors

In Filament actions, catch service exceptions and show notifications rather than crashing:

```php
public function handleCreate(CreateEmployeeData $data): void
{
    try {
        $this->employees->create($data);
        Notification::make()
            ->title('Employee created')
            ->success()
            ->send();
    } catch (BusinessRuleException $e) {
        Notification::make()
            ->title('Cannot create employee')
            ->body($e->getMessage())
            ->warning()
            ->send();
    } catch (\Exception $e) {
        Notification::make()
            ->title('Something went wrong')
            ->body('Please try again. If this persists, contact support.')
            ->danger()
            ->send();
        report($e); // sends to error tracker
    }
}
```

---

## Validation Error Display

### Inertia Forms (Vue)

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  first_name: '',
  email: '',
})

function submit() {
  form.post(route('hr.employees.store'))
}
</script>

<template>
  <form @submit.prevent="submit">
    <div>
      <label>First name</label>
      <input v-model="form.first_name" />
      <!-- Error shown automatically from Inertia form errors -->
      <p v-if="form.errors.first_name" class="text-danger-500 text-body-sm mt-1">
        {{ form.errors.first_name }}
      </p>
    </div>

    <!-- Processing state -->
    <button type="submit" :disabled="form.processing">
      <span v-if="form.processing">Saving...</span>
      <span v-else>Save</span>
    </button>
  </form>
</template>
```

### DTO Validation Errors

spatie/laravel-data automatically handles validation from DTO attributes:

```php
// In route (no FormRequest needed):
Route::post('/hr/employees', [EmployeeController::class, 'store']);

// In controller:
public function store(CreateEmployeeData $request): JsonResponse
{
    // spatie/laravel-data auto-validates from attribute rules
    // If invalid, throws ValidationException → Inertia handles it
    $employee = $this->employees->create($request);
    return response()->json(EmployeeResource::from($employee), 201);
}
```

---

## Error Monitoring

Integrate error tracking (Sentry or Flare) in production:

```php
// config/logging.php — report exceptions to Sentry
'channels' => [
    'sentry' => [
        'driver' => 'sentry',
        'level' => 'error',
    ],
],
```

```php
// bootstrap/app.php — skip expected exceptions
$exceptions->dontReport([
    AuthenticationException::class,
    ValidationException::class,
    ModelNotFoundException::class,
    ResourceNotFoundException::class,
]);
```

---

## Related

- [[Architecture]]
- [[Tech Stack]]
- [[Security Rules]]
- [[Rate Limiting]]
