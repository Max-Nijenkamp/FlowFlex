<x-filament-panels::page>
    <form class="ff-settings-form" wire:submit="save">
        {{ $this->form }}
        <div>
            <x-filament::button type="submit">Save preferences</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
