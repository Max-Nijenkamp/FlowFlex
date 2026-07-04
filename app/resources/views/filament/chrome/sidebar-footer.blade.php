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

    $profileUrl = filament()->getProfileUrl();
    $logoutUrl = filament()->getLogoutUrl();

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

    {{-- User card doubles as the account menu (topbar user menu is hidden —
         profile and sign-out live here, per the handoff sidebar footer). --}}
    <div class="ff-user-menu" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape="open = false">
        <div class="ff-user-menu-panel" x-show="open" x-cloak>
            {{-- Theme switcher (mirrors Filament's, sans dropdown close()) --}}
            <div
                class="ff-theme-row"
                x-data="{ theme: null }"
                x-init="
                    $watch('theme', () => $dispatch('theme-changed', theme))
                    theme = localStorage.getItem('theme') || @js(filament()->getDefaultThemeMode()->value)
                "
            >
                <button type="button" title="Light" x-on:click="theme = 'light'" x-bind:class="{ 'ff-on': theme === 'light' }">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><circle cx="10" cy="10" r="3.5"></circle><path d="M10 2.5v2M10 15.5v2M2.5 10h2M15.5 10h2M4.7 4.7l1.4 1.4M13.9 13.9l1.4 1.4M15.3 4.7l-1.4 1.4M6.1 13.9l-1.4 1.4"></path></svg>
                </button>
                <button type="button" title="Dark" x-on:click="theme = 'dark'" x-bind:class="{ 'ff-on': theme === 'dark' }">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M16.5 11.5A7 7 0 0 1 8.5 3.5a7 7 0 1 0 8 8Z"></path></svg>
                </button>
                <button type="button" title="System" x-on:click="theme = 'system'" x-bind:class="{ 'ff-on': theme === 'system' }">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><rect x="2.5" y="4" width="15" height="10" rx="1.5"></rect><path d="M7.5 17h5M10 14v3"></path></svg>
                </button>
            </div>
            <a href="{{ $profileUrl }}" class="ff-user-menu-item">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><circle cx="10" cy="6.5" r="3"></circle><path d="M4 17c.7-3.5 3-5.25 6-5.25s5.3 1.75 6 5.25"></path></svg>
                Profile
            </a>
            <form method="POST" action="{{ $logoutUrl }}">
                @csrf
                <button type="submit" class="ff-user-menu-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M12.5 6.5V4.75A1.75 1.75 0 0 0 10.75 3h-4A1.75 1.75 0 0 0 5 4.75v10.5A1.75 1.75 0 0 0 6.75 17h4a1.75 1.75 0 0 0 1.75-1.75V13.5M8.5 10h8.5m0 0-2.5-2.5M17 10l-2.5 2.5"></path></svg>
                    Sign out
                </button>
            </form>
        </div>
        <button type="button" class="ff-user-card" x-on:click="open = ! open">
            <span class="ff-user-ava">{{ $initial }}</span>
            <span class="ff-user-meta">
                <span class="ff-user-nm">{{ $name }}</span>
                <span class="ff-user-co">{{ $isStaff ? 'FlowFlex staff' : $companyName }}</span>
            </span>
            <svg class="ff-user-caret" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="14" height="14"><path d="M6.5 12.5 10 9l3.5 3.5"></path></svg>
        </button>
    </div>
</div>
