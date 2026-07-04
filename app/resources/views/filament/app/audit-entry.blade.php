{{-- Audit entry detail (core.audit-log/log-browser modal) — Switchboard
     description-list styling instead of stacked infolist entries. --}}
@php
    /** @var \App\Models\Activity $record */
    $props = $record->properties?->toArray() ?? [];
    $new = $props['attributes'] ?? null;
    $old = $props['old'] ?? null;
    $rest = collect($props)->except(['attributes', 'old']);

    $fmt = function ($value): string {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '—';
    };
@endphp

<div class="ff-audit">
    <div class="ff-audit-top">
        <span class="ff-audit-event">{{ $record->event ?? $record->description }}</span>
        <span class="ff-audit-when">{{ $record->created_at?->format('d M Y · H:i:s') }}</span>
    </div>

    <div class="ff-audit-meta">
        <div class="ff-audit-cell">
            <span class="ff-audit-label">By</span>
            <span class="ff-audit-value">{{ $record->causer?->full_name ?? 'System' }}</span>
        </div>
        <div class="ff-audit-cell">
            <span class="ff-audit-label">Domain</span>
            <span class="ff-audit-value">{{ $record->log_name ?? '—' }}</span>
        </div>
        <div class="ff-audit-cell">
            <span class="ff-audit-label">Subject</span>
            <span class="ff-audit-value">
                {{ $record->subject_type === null ? '—' : class_basename($record->subject_type).' · '.substr((string) $record->subject_id, -6) }}
            </span>
        </div>
    </div>

    @if ($new !== null || $old !== null)
        <div class="ff-audit-block">
            <span class="ff-audit-block-title">Changes</span>
            <div class="ff-audit-rows">
                @foreach (collect($new ?? $old)->keys()->merge(collect($old ?? [])->keys())->unique() as $key)
                    <div class="ff-audit-row">
                        <span class="ff-audit-key">{{ str($key)->headline() }}</span>
                        <span class="ff-audit-val">
                            @if ($old !== null && array_key_exists($key, $old))
                                <s>{{ $fmt($old[$key]) }}</s>
                            @endif
                            @if ($new !== null && array_key_exists($key, $new))
                                <strong>{{ $fmt($new[$key]) }}</strong>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($rest->isNotEmpty())
        <div class="ff-audit-block">
            <span class="ff-audit-block-title">Details</span>
            <div class="ff-audit-rows">
                @foreach ($rest as $key => $value)
                    <div class="ff-audit-row">
                        <span class="ff-audit-key">{{ str((string) $key)->headline() }}</span>
                        <span class="ff-audit-val">{{ $fmt($value) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($rest->isEmpty() && $new === null && $old === null)
        <p class="ff-audit-none">No extra detail recorded for this entry.</p>
    @endif
</div>
