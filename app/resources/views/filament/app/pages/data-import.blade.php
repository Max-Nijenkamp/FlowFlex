<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Upload form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Import CSV data</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                Upload a CSV file to import records. The first row must be a header row.
                Maximum file size: 10 MB.
            </p>

            <form wire:submit.prevent="handleImport" class="space-y-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                        Entity type <span class="text-danger-500">*</span>
                    </label>
                    <select
                        wire:model="entityType"
                        class="w-full max-w-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-sm px-3 py-2 focus:ring-2 focus:ring-primary-500"
                    >
                        <option value="">— Select entity —</option>
                        @foreach ($this->getEntityOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('entityType')
                        <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">
                        CSV file <span class="text-danger-500">*</span>
                    </label>
                    <input
                        type="file"
                        wire:model="csvFile"
                        accept=".csv,.txt"
                        class="block w-full max-w-sm text-sm text-gray-700 dark:text-gray-300
                               file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                               file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700
                               dark:file:bg-primary-900/20 dark:file:text-primary-400
                               hover:file:bg-primary-100 cursor-pointer"
                    >
                    <div wire:loading wire:target="csvFile" class="mt-1 text-xs text-gray-400">Uploading…</div>
                    @error('csvFile')
                        <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-filament::button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="handleImport,csvFile"
                    >
                        <span wire:loading.remove wire:target="handleImport">Start import</span>
                        <span wire:loading wire:target="handleImport">Processing…</span>
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Recent import jobs --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Recent import jobs</h2>

            @php $jobs = $this->getRecentJobs(); @endphp

            @if ($jobs->isEmpty())
                <p class="text-sm text-gray-400 dark:text-gray-500 italic">No import jobs yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="pb-2 pr-4 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Entity</th>
                                <th class="pb-2 pr-4 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                <th class="pb-2 pr-4 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total rows</th>
                                <th class="pb-2 pr-4 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Processed</th>
                                <th class="pb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach ($jobs as $job)
                                @php
                                    $statusColor = match($job->status) {
                                        'done'       => 'text-green-700 bg-green-100 dark:text-green-400 dark:bg-green-900/30',
                                        'pending'    => 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-700/50',
                                        'mapping',
                                        'validating' => 'text-blue-700 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/30',
                                        'importing'  => 'text-yellow-700 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30',
                                        'failed'     => 'text-red-700 bg-red-100 dark:text-red-400 dark:bg-red-900/30',
                                        default      => 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-700/50',
                                    };
                                @endphp
                                <tr>
                                    <td class="py-2.5 pr-4 font-medium text-gray-900 dark:text-white capitalize">
                                        {{ $job->entity_type }}
                                    </td>
                                    <td class="py-2.5 pr-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColor }}">
                                            {{ $job->status }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 pr-4 text-gray-700 dark:text-gray-300">
                                        {{ $job->total_rows ?? '—' }}
                                    </td>
                                    <td class="py-2.5 pr-4 text-gray-700 dark:text-gray-300">
                                        {{ $job->imported_rows ?? '—' }}
                                    </td>
                                    <td class="py-2.5 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $job->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
