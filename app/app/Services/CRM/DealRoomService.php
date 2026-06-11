<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Exceptions\CRM\RoomUnavailableException;
use App\Models\CRM\Deal;
use App\Models\CRM\DealRoom;
use Illuminate\Support\Str;

class DealRoomService
{
    public function create(string $dealId): DealRoom
    {
        $deal = Deal::query()->findOrFail($dealId);

        return DealRoom::create([
            'deal_id' => $deal->id,
            'access_token' => (string) Str::uuid(),
            'expires_at' => ($deal->expected_close_date ?? now()->addMonth())->copy()->addDays(30),
        ])->refresh();
    }

    /**
     * Public token resolution — bypasses CompanyScope (guest has no session),
     * scoped to the single matching live room. Never exposes internal CRM
     * data beyond the shared content.
     */
    public function publicView(string $token): DealRoom
    {
        $room = DealRoom::query()->withoutGlobalScopes()
            ->where('access_token', $token)
            ->with(['documents' => fn ($q) => $q->withoutGlobalScopes(),
                'actionItems' => fn ($q) => $q->withoutGlobalScopes(),
                'stakeholders' => fn ($q) => $q->withoutGlobalScopes()])
            ->first();

        if ($room === null || ! $room->isLive()) {
            throw new RoomUnavailableException;
        }

        return $room;
    }

    public function trackDocumentView(string $token, string $documentId): void
    {
        $room = $this->publicView($token);

        $room->documents()->withoutGlobalScopes()
            ->whereKey($documentId)
            ->increment('view_count', 1, ['last_viewed_at' => now()]);
    }

    /** Buyers toggle buyer-side items only (public surface). */
    public function toggleActionItem(string $token, string $itemId, string $side): void
    {
        $room = $this->publicView($token);

        $item = $room->actionItems()->withoutGlobalScopes()
            ->whereKey($itemId)
            ->when($side === 'buyer', fn ($q) => $q->where('owner_side', 'buyer'))
            ->firstOrFail();

        $item->update(['status' => $item->status === 'open' ? 'done' : 'open']);
    }

    public function revoke(string $roomId): DealRoom
    {
        $room = DealRoom::query()->findOrFail($roomId);
        $room->update(['revoked_at' => now()]);

        return $room->refresh();
    }
}
