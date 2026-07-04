<x-filament-panels::page>
    <div x-data="{ tab: 'identity' }" class="ff-settings-tabs-wrp">
        <nav class="ff-settings-tabs">
            <button type="button" x-on:click="tab = 'identity'" x-bind:class="{ 'ff-on': tab === 'identity' }">Identity</button>
            <button type="button" x-on:click="tab = 'locale'" x-bind:class="{ 'ff-on': tab === 'locale' }">Locale</button>
            <button type="button" x-on:click="tab = 'business'" x-bind:class="{ 'ff-on': tab === 'business' }">Business</button>
            <button type="button" x-on:click="tab = 'privacy'" x-bind:class="{ 'ff-on': tab === 'privacy' }">Privacy</button>
        </nav>

        <section x-show="tab === 'identity'">
            <form class="ff-settings-form" wire:submit="saveIdentity">
                {{ $this->identityForm }}
                <div>
                    <x-filament::button type="submit">Save identity</x-filament::button>
                </div>
            </form>
        </section>

        <section x-show="tab === 'locale'" x-cloak>
            <form class="ff-settings-form" wire:submit="saveLocale">
                {{ $this->localeForm }}
                <div>
                    <x-filament::button type="submit">Save locale</x-filament::button>
                </div>
            </form>
        </section>

        <section x-show="tab === 'business'" x-cloak>
            <form class="ff-settings-form" wire:submit="saveBusiness">
                {{ $this->businessForm }}
                <div>
                    <x-filament::button type="submit">Save business settings</x-filament::button>
                </div>
            </form>
        </section>

        <section x-show="tab === 'privacy'" x-cloak>
            <form class="ff-settings-form" wire:submit="savePrivacy">
                {{ $this->privacyForm }}
                <div>
                    <x-filament::button type="submit">Save privacy settings</x-filament::button>
                </div>
            </form>
        </section>
    </div>
</x-filament-panels::page>
