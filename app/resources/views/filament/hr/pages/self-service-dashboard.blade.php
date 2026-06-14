<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.stat-cards :count="3" />
        @else
            @php
                $tiles = $this->getTiles();
            @endphp

            @if ($tiles['employee'] === null)
                <x-filament::section>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No employee record is linked to your account yet — ask HR to connect your profile.
                    </p>
                </x-filament::section>
            @else
                @php
                    $initials = collect(explode(' ', trim($tiles['employee']->full_name)))
                        ->filter()
                        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
                        ->take(2)
                        ->implode('');
                @endphp

                <div class="space-y-6">
                    <x-filament::section>
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 font-semibold text-primary-700 dark:bg-primary-400/10 dark:text-primary-400">
                                {{ $initials }}
                            </div>

                            <div class="min-w-0">
                                <div class="truncate text-lg font-bold text-gray-950 dark:text-white">
                                    Welcome back, {{ $tiles['employee']->first_name }}
                                </div>
                                <div class="truncate text-sm text-gray-500 dark:text-gray-400">
                                    {{ $tiles['employee']->full_name }} — {{ $tiles['employee']->job_title }}
                                </div>
                            </div>
                        </div>
                    </x-filament::section>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @isset($tiles['leave_remaining'])
                            <x-filament::section>
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-700 dark:bg-primary-400/10 dark:text-primary-400">
                                        <x-filament::icon icon="heroicon-o-sun" class="h-6 w-6" />
                                    </div>

                                    <div class="min-w-0">
                                        <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                            {{ $tiles['leave_remaining'] }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Days of leave remaining</div>
                                    </div>
                                </div>
                            </x-filament::section>
                        @endisset

                        @isset($tiles['open_tasks'])
                            <x-filament::section>
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-700 dark:bg-primary-400/10 dark:text-primary-400">
                                        <x-filament::icon icon="heroicon-o-clipboard-document-check" class="h-6 w-6" />
                                    </div>

                                    <div class="min-w-0">
                                        <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                            {{ $tiles['open_tasks'] }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Open onboarding tasks</div>
                                    </div>
                                </div>
                            </x-filament::section>
                        @endisset
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
