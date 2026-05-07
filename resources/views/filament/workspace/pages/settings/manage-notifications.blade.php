<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Notification Preferences</x-slot>
        <x-slot name="description">Choose how you receive notifications for each event type. Database notifications always appear in the bell icon. Email delivery is optional.</x-slot>

        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <x-filament::actions :actions="$this->getFormActions()" />
        </form>
    </x-filament::section>
</x-filament-panels::page>
