@php
    $domainColors = [
        'hr' => '#8B5CF6', 'finance' => '#10B981', 'crm' => '#F43F5E', 'core' => '#94A3B8',
        'projects' => '#6366F1', 'support' => '#F97316', 'marketing' => '#EC4899', 'dms' => '#64748B',
    ];
    $euro = fn (int $cents): string => '€'.number_format($cents / 100, 2, ',', '.');
@endphp

<x-filament-widgets::widget>
    <div class="overflow-hidden rounded-[14px] border border-[#D8D4CA] bg-white shadow-[0_1px_2px_rgba(17,24,39,0.04)] dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between border-b border-[#E7E4DD] px-5 py-3.5 dark:border-slate-700">
            <h3 class="font-bold" style="font-family: 'Archivo', sans-serif; font-size: 14.5px; letter-spacing: -0.01em;">
                Your switchboard
            </h3>
            <a href="{{ \App\Filament\App\Pages\ModuleMarketplacePage::getUrl() }}"
                class="text-xs font-semibold text-primary-600 hover:underline dark:text-primary-400">
                Open the marketplace →
            </a>
        </div>

        @forelse ($rows as $i => $row)
            <div class="flex items-center justify-between gap-4 border-b border-[#E7E4DD] px-5 py-3 dark:border-slate-700 {{ $i % 2 === 0 ? 'bg-[#FAF9F5] dark:bg-white/5' : '' }}">
                <span class="flex items-center gap-2.5 text-sm font-semibold">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-[3px]"
                        style="background: {{ $domainColors[$row['domain']] ?? '#4F46E5' }}"></span>
                    {{ $row['name'] }}
                </span>
                <span class="flex items-center gap-3">
                    <span class="font-mono text-xs text-[#98A0AB]">
                        {{ $row['cents'] === 0 ? 'included' : $euro($row['cents']).'/user' }}
                    </span>
                    <span class="rounded-[5px] bg-primary-50 px-2 py-[3px] font-mono text-[9.5px] tracking-[0.12em] text-primary-600 dark:bg-primary-500/20 dark:text-primary-400">ON</span>
                </span>
            </div>
        @empty
            <div class="px-5 py-10 text-center">
                <p class="font-bold" style="font-family: 'Archivo', sans-serif;">Nothing switched on yet</p>
                <p class="mt-1 text-sm text-[#98A0AB]">Flip your first module in the marketplace — it's live immediately.</p>
            </div>
        @endforelse

        <div class="flex items-baseline justify-between gap-3 bg-[#111827] px-5 py-3.5 text-white">
            <span class="font-mono text-xs text-white/65">{{ $euro($perUserCents) }}/user × {{ $users }} users</span>
            <span class="font-mono text-lg font-bold">{{ $euro($monthlyCents) }}<span class="text-xs font-normal text-white/55">/month</span></span>
        </div>
    </div>
</x-filament-widgets::widget>
