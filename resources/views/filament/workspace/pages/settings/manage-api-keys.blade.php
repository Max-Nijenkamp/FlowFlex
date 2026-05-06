<x-filament-panels::page>
    @if($this->newlyCreatedKey)
        <x-filament::section
            icon="heroicon-o-exclamation-triangle"
            icon-color="warning"
            color="warning"
        >
            <x-slot name="heading">Store your API key now</x-slot>
            <x-slot name="description">
                This key will only be shown once. Copy it and store it in a secure location — you will not be able to see it again.
            </x-slot>

            <div class="flex items-center gap-3 mt-2">
                <code
                    class="flex-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 font-mono text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 break-all"
                    x-data
                    id="api-key-display"
                >{{ $this->newlyCreatedKey }}</code>

                <button
                    type="button"
                    x-data="{ copied: false }"
                    x-on:click="
                        navigator.clipboard.writeText('{{ $this->newlyCreatedKey }}');
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                    class="shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-700"
                >
                    <span x-show="!copied">
                        <x-heroicon-o-clipboard-document class="h-4 w-4" />
                    </span>
                    <span x-show="copied">
                        <x-heroicon-o-check class="h-4 w-4 text-success-500" />
                    </span>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </x-filament::section>
    @endif

    <x-filament::section>
        <x-slot name="heading">API Keys</x-slot>
        <x-slot name="description">
            API keys allow external services to authenticate with the FlowFlex API. Keys are shown only once at creation time.
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
