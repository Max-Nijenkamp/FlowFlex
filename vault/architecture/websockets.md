---
type: architecture
category: infra
pattern-key: websockets
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# WebSockets (Laravel Reverb)

> [!warning] Authoritative infra source moved
> Verified infrastructure facts now live in [[../infrastructure/websockets-reverb]] (Reverb runs on **:8081**, not 8080). Details on this page may predate the 2026-06-20 rebuild — trust the linked note on any conflict.


Real-time features via Laravel Reverb 1.x. Reverb is a first-party WebSocket server — no third-party service needed. Features: notification badges, live data updates, presence channels for collaborative editing.

---

## Broadcast vs Poll — The Decision Rule

Default = **no realtime**. Escalate only to the cheapest mechanism that meets the real user expectation (rule shared with [[architecture/ui-strategy]] — specs cite it in `## Search & Realtime`):

| Level | Mechanism | Use when | Examples |
|---|---|---|---|
| 0 | Nothing | data changes rarely / user refreshes naturally | all standard CRUD |
| 1 | Livewire polling — `wire:poll.30s`, widget `$pollingInterval` | staleness ≥ 30s acceptable, single-user view | dashboards, calendars, stats widgets, pending lists |
| 2 | Reverb broadcast | collaborative view (multiple users mutate same board, sub-5s expectation) · presence ("who's viewing") · notification bell | Kanban/pipeline boards, shared inbox, `company.{id}.notifications` |

Hard rules:
- Never poll faster than 15s — that workload belongs on Reverb
- Never broadcast what a 30s poll covers — each broadcast event is code + channel auth + frontend listener to maintain
- v1 Reverb surface is exactly: notification bell, pipeline/kanban boards, shared inbox. Adding a new broadcast use case = update this table first
- Filament live-search/table filters are plain Livewire requests — they never involve Reverb or Meilisearch-triggered events

---

## Architecture

```mermaid
graph LR
    Browser["Browser (Alpine.js / Echo.js)"]
    Reverb["Laravel Reverb\n:8080 (WebSocket)"]
    App["Laravel App\n(fires events)"]
    Redis["Redis (Broadcast channel)"]

    Browser <-->|WebSocket| Reverb
    App -->|broadcast()| Redis
    Redis --> Reverb
    Reverb -->|push| Browser
```

The Laravel app fires a broadcast event. Redis distributes it to Reverb. Reverb pushes it to connected clients. The browser updates the UI without a page refresh.

---

## Configuration

```php
// config/broadcasting.php
'default' => 'reverb',

'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key' => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
        'options' => [
            'host' => env('REVERB_HOST', 'localhost'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'https'),
        ],
    ],
],
```

---

## Channel Types

### Private Channels (most common)

Tenant-scoped. Every subscriber must be authenticated and belong to the correct company:

```php
// routes/channels.php
Broadcast::channel('company.{companyId}', function (User $user, string $companyId) {
    return $user->company_id === $companyId;
});

Broadcast::channel('company.{companyId}.notifications', function (User $user, string $companyId) {
    return $user->company_id === $companyId;
});
```

### Presence Channels

Track who is online in a shared context (e.g. viewing the same Kanban board):

```php
Broadcast::channel('company.{companyId}.board.{boardId}', function (User $user, string $companyId) {
    if ($user->company_id !== $companyId) return false;
    return ['id' => $user->id, 'name' => $user->full_name];
});
```

---

## Broadcast Events Per Domain

```php
// Notification received (all panels — updates bell badge)
class NotificationCreated implements ShouldBroadcast
{
    public function __construct(
        private readonly Notification $notification,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->notification->company_id}.notifications");
    }

    public function broadcastAs(): string { return 'notification.created'; }
}

// Leave request status changed (HR panel)
class LeaveRequestStatusChanged implements ShouldBroadcast
{
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->request->company_id}.hr");
    }
}

// Deal moved on pipeline board (CRM panel)
class DealStagedChanged implements ShouldBroadcast
{
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("company.{$this->deal->company_id}.crm");
    }
}
```

---

## Real-Time Features Per Domain

| Domain | Feature | Channel | Trigger |
|---|---|---|---|
| All panels | Notification badge count | `company.{id}.notifications` | Any notification created |
| HR | Leave request approval status | `company.{id}.hr` | Leave approved/rejected |
| HR | Onboarding task completion | `company.{id}.hr` | Task marked complete |
| CRM | Pipeline board live updates | `company.{id}.crm` | Deal stage changed |
| Projects | Task status changes | `company.{id}.projects` | Task updated |
| Communications | New message in inbox | `company.{id}.comms` | Message received |
| Support | New ticket assigned | `company.{id}.support` | Ticket assigned |
| Finance | Invoice payment received | `company.{id}.finance` | Payment recorded |

---

## Frontend Integration (Alpine.js + Echo.js)

In Filament panels, Echo.js connects to Reverb and updates the UI:

```javascript
// resources/js/echo.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js'; // Echo uses Pusher protocol

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
});
```

Alpine.js component for notification badge:

```html
<div x-data="{
    count: $wire.unreadCount,
    init() {
        Echo.private('company.' + companyId + '.notifications')
            .listen('notification.created', () => { this.count++ })
    }
}">
    <span x-text="count" x-show="count > 0" class="badge">0</span>
</div>
```

---

## Security

- All channels are private — unauthenticated clients cannot subscribe
- Channel authorization validates `company_id` match — no cross-tenant subscriptions possible
- Reverb uses the same Laravel session authentication — no separate auth needed
- In production, Reverb runs behind the Nginx proxy with TLS termination
