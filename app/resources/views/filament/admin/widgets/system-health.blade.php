<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">System health</x-slot>
        <x-slot name="headerEnd">
            @if ($ranAt)
                <span class="fi-ta-text text-sm text-gray-500">
                    last run {{ \Illuminate\Support\Carbon::parse($ranAt)->diffForHumans() }}
                </span>
            @endif
        </x-slot>

        @if ($checks->isEmpty())
            <p class="text-sm text-gray-500">
                No health results yet — run <code>php artisan health:check</code> (scheduled in production).
            </p>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($checks as $check)
                    <div class="rounded-lg border p-3 {{ $check->status === 'ok' ? 'border-green-200' : 'border-red-300' }}">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-2 w-2 rounded-full {{ $check->status === 'ok' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            <span class="text-sm font-semibold">{{ $check->label }}</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $check->shortSummary !== '' ? $check->shortSummary : $check->status }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
