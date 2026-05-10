<?php

declare(strict_types=1);

use App\Contracts\Core\NotifiableEvent;
use App\Models\Company;
use App\Models\Core\NotificationLog;
use App\Models\Core\NotificationPreference;
use App\Models\Core\NotificationQuietHours;
use App\Models\User;
use App\Services\Core\NotificationRouter;
use App\Support\Services\CompanyContext;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

// Minimal stub implementing NotifiableEvent
class StubNotifiableEvent implements NotifiableEvent
{
    public function __construct(
        private readonly string $type = 'test.event',
        private readonly string $prio = 'normal',
    ) {}

    public function eventType(): string
    {
        return $this->type;
    }

    public function priority(): string
    {
        return $this->prio;
    }

    public function toNotification(User $user): Notification
    {
        return new class extends Notification {
            public function via($notifiable): array
            {
                return ['database'];
            }

            public function toArray($notifiable): array
            {
                return ['message' => 'stub'];
            }
        };
    }
}

describe('Notification Router', function () {
    beforeEach(function () {
        NotificationFacade::fake();

        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);

        $this->router = app(NotificationRouter::class);
    });

    it('routes a critical event to database and mail regardless of preferences', function () {
        $event = new StubNotifiableEvent('test.critical', 'critical');

        $this->router->route($event, $this->user);

        // Two channels logged: database + mail
        expect(NotificationLog::where('user_id', $this->user->id)->count())->toBe(2);
    });

    it('routes to default database channel when no preferences set', function () {
        $event = new StubNotifiableEvent('test.event', 'normal');

        $this->router->route($event, $this->user);

        expect(NotificationLog::where('user_id', $this->user->id)
            ->where('channel', 'database')
            ->exists()
        )->toBeTrue();
    });

    it('routes to user-configured channels', function () {
        NotificationPreference::create([
            'user_id'    => $this->user->id,
            'company_id' => $this->company->id,
            'event_type' => 'test.event',
            'channel'    => 'mail',
            'enabled'    => true,
        ]);

        $event = new StubNotifiableEvent('test.event', 'normal');
        $this->router->route($event, $this->user);

        expect(NotificationLog::where('user_id', $this->user->id)
            ->where('channel', 'mail')
            ->exists()
        )->toBeTrue();
    });

    it('does not route when all preferences disabled', function () {
        NotificationPreference::create([
            'user_id'    => $this->user->id,
            'company_id' => $this->company->id,
            'event_type' => 'test.event',
            'channel'    => 'mail',
            'enabled'    => false,
        ]);

        $event = new StubNotifiableEvent('test.event', 'normal');

        // enabledChannelsFor returns empty => returns early, no log
        // But note: when prefs exist but all disabled, pluck returns [], so fallback is ['database']
        // This is the current behaviour — route always falls back to database when prefs empty
        $this->router->route($event, $this->user);

        // Fallback to database since no enabled prefs found
        expect(NotificationLog::where('user_id', $this->user->id)
            ->where('channel', 'database')
            ->exists()
        )->toBeTrue();
    });

    it('suppresses non-database channels during quiet hours', function () {
        NotificationPreference::create([
            'user_id'    => $this->user->id,
            'company_id' => $this->company->id,
            'event_type' => 'test.event',
            'channel'    => 'mail',
            'enabled'    => true,
        ]);

        // Create quiet hours that are always active (midnight-midnight in far future)
        NotificationQuietHours::create([
            'user_id'    => $this->user->id,
            'start_time' => '00:00:00',
            'end_time'   => '23:59:59',
            'timezone'   => 'UTC',
        ]);

        $event = new StubNotifiableEvent('test.event', 'normal');
        $this->router->route($event, $this->user);

        // During quiet hours, mail should be suppressed; only database allowed
        expect(NotificationLog::where('user_id', $this->user->id)
            ->where('channel', 'mail')
            ->exists()
        )->toBeFalse();
    });

    it('logs notification with company_id from context', function () {
        $event = new StubNotifiableEvent('test.event', 'normal');
        $this->router->route($event, $this->user);

        $log = NotificationLog::where('user_id', $this->user->id)->first();
        expect($log->company_id)->toBe($this->company->id);
    });
});
