<x-filament-panels::page>
    @php
        $steps      = collect($this->getSteps())->filter(fn($s) => $s !== 'done')->values();
        $config     = $this->getStepConfig();
        $totalSteps = $steps->count();
        $activeIndex = $steps->search($currentStep);
        $isDone     = $currentStep === 'done';
    @endphp

    <div class="max-w-2xl mx-auto space-y-8">

        {{-- ─── Step progress bar ─── --}}
        @unless($isDone)
        <div class="flex items-center">
            @foreach($steps as $i => $step)
                @php
                    $isCompleted = in_array($step, $completedSteps);
                    $isActive    = $currentStep === $step;
                @endphp

                {{-- Circle + label --}}
                <div class="flex flex-col items-center gap-1.5 relative z-10">
                    <div @class([
                        'w-10 h-10 rounded-full flex items-center justify-center ring-2 ring-offset-2 transition-all duration-200',
                        'bg-success-500 ring-success-500 text-white'  => $isCompleted,
                        'bg-primary-600 ring-primary-600 text-white shadow-lg shadow-primary-200' => $isActive && !$isCompleted,
                        'bg-white ring-gray-200 text-gray-400 dark:bg-gray-800 dark:ring-gray-600' => !$isActive && !$isCompleted,
                    ])>
                        @if($isCompleted)
                            <x-heroicon-s-check class="w-5 h-5" />
                        @else
                            <span class="text-sm font-semibold">{{ $i + 1 }}</span>
                        @endif
                    </div>
                    <span @class([
                        'text-xs font-medium whitespace-nowrap',
                        'text-success-600 dark:text-success-400' => $isCompleted,
                        'text-primary-600 dark:text-primary-400' => $isActive && !$isCompleted,
                        'text-gray-400 dark:text-gray-500'       => !$isActive && !$isCompleted,
                    ])>{{ $config[$step]['label'] }}</span>
                </div>

                {{-- Connector line --}}
                @if(!$loop->last)
                    <div class="flex-1 mx-1 -mt-5">
                        <div @class([
                            'h-0.5 w-full transition-all duration-300',
                            'bg-success-400' => in_array($steps[$i + 1] ?? '', $completedSteps) || $isCompleted,
                            'bg-gray-200 dark:bg-gray-700' => !($isCompleted),
                        ])></div>
                    </div>
                @endif
            @endforeach
        </div>
        @endunless

        {{-- ─── Step content card ─── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

            @if($isDone)
                {{-- Done state --}}
                <div class="flex flex-col items-center text-center px-10 py-16 gap-4">
                    <div class="w-20 h-20 rounded-full bg-success-50 dark:bg-success-900/30 flex items-center justify-center mb-2">
                        <x-heroicon-s-check-circle class="w-12 h-12 text-success-500" />
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">You're all set!</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                        Your workspace is ready to go. This wizard won't appear again — you can always find settings in the sidebar.
                    </p>
                    <div class="mt-2">
                        <x-filament::button
                            href="{{ route('filament.app.pages.dashboard') }}"
                            tag="a"
                            size="lg"
                            icon="heroicon-o-arrow-right"
                            icon-position="after"
                        >
                            Go to dashboard
                        </x-filament::button>
                    </div>
                </div>

            @elseif(isset($config[$currentStep]))
                @php $step = $config[$currentStep]; @endphp

                {{-- Icon banner --}}
                <div class="bg-gradient-to-br from-primary-50 to-primary-100/50 dark:from-primary-900/20 dark:to-primary-800/10 px-8 pt-8 pb-6 flex items-start gap-5">
                    <div class="w-14 h-14 rounded-xl bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center flex-shrink-0">
                        <x-filament::icon
                            :icon="$step['icon']"
                            class="w-7 h-7 text-primary-600 dark:text-primary-400"
                        />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-primary-500 dark:text-primary-400 mb-1">
                            Step {{ $activeIndex + 1 }} of {{ $totalSteps }}
                        </p>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-snug">
                            {{ $step['title'] }}
                        </h2>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-8 py-6 space-y-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                        {{ $step['description'] }}
                    </p>

                    {{-- Step-specific content --}}
                    @if($currentStep === 'company')
                        <a href="{{ route('filament.app.pages.company-settings') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-200 dark:hover:border-primary-700 transition-colors group">
                            <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 text-gray-400 group-hover:text-primary-500 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-primary-600">Open Company Settings</p>
                                <p class="text-xs text-gray-400">Name, timezone, currency, logo</p>
                            </div>
                        </a>

                    @elseif($currentStep === 'team')
                        <a href="{{ route('filament.app.resources.users.index') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-200 dark:hover:border-primary-700 transition-colors group">
                            <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 text-gray-400 group-hover:text-primary-500 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-primary-600">Go to Users</p>
                                <p class="text-xs text-gray-400">Invite colleagues by email</p>
                            </div>
                        </a>

                    @elseif($currentStep === 'modules')
                        <a href="{{ route('filament.app.pages.module-marketplace') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-200 dark:hover:border-primary-700 transition-colors group">
                            <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 text-gray-400 group-hover:text-primary-500 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-primary-600">Open Module Marketplace</p>
                                <p class="text-xs text-gray-400">HR, Finance, CRM, Projects and more</p>
                            </div>
                        </a>

                    @elseif($currentStep === 'branding')
                        <a href="{{ route('filament.app.pages.company-settings') }}"
                           class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-200 dark:hover:border-primary-700 transition-colors group">
                            <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 text-gray-400 group-hover:text-primary-500 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-primary-600">Open Branding Settings</p>
                                <p class="text-xs text-gray-400">Logo, colours, workspace name</p>
                            </div>
                        </a>
                    @endif

                    {{-- CTA row --}}
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-xs text-gray-400">
                            {{ count($completedSteps) }} of {{ $totalSteps }} steps completed
                        </span>
                        <x-filament::button
                            wire:click="completeStep('{{ $currentStep }}')"
                            wire:loading.attr="disabled"
                            size="lg"
                            icon="heroicon-o-arrow-right"
                            icon-position="after"
                        >
                            @if($currentStep === 'welcome')
                                Get started
                            @elseif($currentStep === 'branding')
                                Finish setup
                            @else
                                Continue
                            @endif
                        </x-filament::button>
                    </div>
                </div>
            @endif

        </div>

        {{-- ─── Quick nav for completed steps ─── --}}
        @if(count($completedSteps) > 0 && !$isDone)
        <p class="text-center text-xs text-gray-400 dark:text-gray-500">
            Completed steps are saved automatically.
        </p>
        @endif

    </div>
</x-filament-panels::page>
