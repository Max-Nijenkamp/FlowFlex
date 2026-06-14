@php
    $domainColors = [
        'hr' => '#8B5CF6', 'finance' => '#10B981', 'crm' => '#F43F5E', 'core' => '#94A3B8',
        'billing' => '#38BDF8', 'auth' => '#818CF8',
    ];
@endphp

<x-filament-widgets::widget>
    <div class="overflow-hidden rounded-[14px] border border-[#D8D4CA] bg-white shadow-[0_1px_2px_rgba(17,24,39,0.04)] dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between border-b border-[#E7E4DD] px-5 py-3.5 dark:border-slate-700">
            <h3 class="font-bold" style="font-family: 'Archivo', sans-serif; font-size: 14.5px; letter-spacing: -0.01em;">
                Recent activity
            </h3>
            <a href="{{ \App\Filament\App\Resources\AuditLogResource::getUrl() }}"
                class="text-xs font-semibold text-primary-600 hover:underline dark:text-primary-400">
                Audit log →
            </a>
        </div>

        @forelse ($entries as $entry)
            <div class="flex items-baseline gap-3 border-b border-[#E7E4DD] px-5 py-2.5 text-[13px] last:border-b-0 dark:border-slate-700">
                <span class="shrink-0 font-mono text-[11px] text-[#98A0AB]">{{ $entry->created_at->format('H:i') }}</span>
                <span class="relative -top-px h-[7px] w-[7px] shrink-0 rounded-[2px]"
                    style="background: {{ $domainColors[$entry->log_name] ?? '#4F46E5' }}"></span>
                <span class="text-[#4B5563] dark:text-slate-300">
                    <b class="font-semibold text-[#111827] dark:text-white">{{ $entry->causer?->email ?? 'System' }}</b>
                    {{ $entry->description }}
                </span>
            </div>
        @empty
            <div class="px-5 py-10 text-center">
                <p class="font-bold" style="font-family: 'Archivo', sans-serif;">Quiet so far</p>
                <p class="mt-1 text-sm text-[#98A0AB]">Every change in the workspace lands here — who, what and when.</p>
            </div>
        @endforelse
    </div>
</x-filament-widgets::widget>
