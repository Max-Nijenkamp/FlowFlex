<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Notification Preferences</x-slot>
        <x-slot name="description">Choose how you receive notifications for each event type. Database notifications always appear in the bell icon. Email delivery is optional.</x-slot>

        <x-filament-panels::form wire:submit="save">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
            />
        </x-filament-panels::form>
    </x-filament::section>
</x-filament-panels::page>
