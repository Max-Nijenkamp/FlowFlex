@php
    $user = filament()->auth()->user();
    $isStaff = filament()->getId() === 'admin';

    $name = $user instanceof \App\Models\User ? $user->full_name : ($user->name ?? '');
    $initial = mb_strtoupper(mb_substr(trim($name) !== '' ? $name : 'F', 0, 1));

    $companyName = 'FlowFlex';
    if (! $isStaff) {
        $context = app(\App\Support\Services\CompanyContext::class);
        $companyName = $context->currentId() !== null ? $context->current()->name : 'FlowFlex';
    }

    // "Your panels" chips: the panels this user can actually enter.
    $chips = [];
    foreach (filament()->getPanels() as $panel) {
        if ($user?->canAccessPanel($panel)) {
            $chips[] = [
                'id' => $panel->getId(),
                'label' => mb_strtoupper(mb_substr($panel->getId(), 0, 2)),
                'url' => $panel->getUrl(),
                'active' => $panel->getId() === filament()->getId(),
            ];
        }
    }
@endphp

<div class="ff-sidebar-foot">
    @if (count($chips) > 1)
        <div class="ff-panel-chips">
            <span class="ff-panel-chips-label">Your panels</span>
            <div class="ff-panel-chips-row">
                @foreach ($chips as $chip)
                    <a
                        href="{{ $chip['url'] }}"
                        @class(['ff-panel-chip', 'ff-on' => $chip['active']])
                        title="{{ $chip['id'] }}"
                    >{{ $chip['label'] }}</a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="ff-user-card">
        <span class="ff-user-ava">{{ $initial }}</span>
        <span class="ff-user-meta">
            <span class="ff-user-nm">{{ $name }}</span>
            <span class="ff-user-co">{{ $isStaff ? 'FlowFlex staff' : $companyName }}</span>
        </span>
    </div>
</div>
