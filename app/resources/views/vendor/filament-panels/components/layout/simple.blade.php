@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }

    // Switchboard+ auth shell: split brand panel on sign-in screens only;
    // forgot/reset/verify keep the centered layout (design handoff §8–11).
    $isSplit = $livewire instanceof \Filament\Auth\Pages\Login;
    $isStaff = filament()->getId() === 'admin';
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div @class(['fi-simple-layout', 'ff-auth-split' => $isSplit])>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

        @if (filament()->hasDarkMode() && ! filament()->hasDarkModeForced())
            <div class="ff-auth-theme-switcher">
                <x-filament-panels::theme-switcher />
            </div>
        @endif

        @if ($isSplit)
            <aside class="ff-auth-brand" aria-hidden="true">
                <div class="ff-auth-brand-glow"></div>

                <svg class="ff-auth-paths" viewBox="0 0 620 900" preserveAspectRatio="none">
                    @foreach ([
                        'M-20,120 C 180,120 240,250 460,250',
                        'M-20,420 C 200,420 260,330 480,330',
                        'M-20,640 C 220,640 280,480 500,480',
                    ] as $i => $d)
                        <path d="{{ $d }}" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="1.5" />
                        <path
                            d="{{ $d }}"
                            fill="none"
                            class="ff-pulse"
                            stroke="{{ $i % 2 ? 'rgba(56,189,248,0.8)' : 'rgba(139,137,255,0.8)' }}"
                            stroke-width="1.5"
                            style="animation-delay: {{ $i * 1.4 }}s"
                        />
                    @endforeach
                </svg>

                <div class="ff-auth-brand-top">
                    <span class="ff-auth-wordmark">FlowFlex</span>
                </div>

                <div class="ff-auth-brand-bottom">
                    @if ($isStaff)
                        <p class="ff-auth-kicker">FLOWFLEX STAFF &middot; /ADMIN</p>
                        <h2 class="ff-auth-display">Platform<br>operations.</h2>
                        <p class="ff-auth-sub">Module catalogue, company workspaces, billing and platform health &mdash; staff access only, fully audited.</p>
                    @else
                        <h2 class="ff-auth-display">Everything<br>flows.</h2>
                        <p class="ff-auth-sub">One login for HR, finance, CRM and every other module your team switched on.</p>
                    @endif

                </div>
            </aside>
        @endif

        <div class="fi-simple-main-ctn">
            <main
                @class([
                    'fi-simple-main',
                    ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                ])
            >
                {{ $slot }}
            </main>
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>
