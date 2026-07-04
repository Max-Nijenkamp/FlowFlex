<x-filament-panels::page>
    <div x-data="{ tab: 'identity' }" class="ff-settings-tabs-wrp">
        <nav class="ff-settings-tabs">
            <button type="button" x-on:click="tab = 'identity'" x-bind:class="{ 'ff-on': tab === 'identity' }">Identity</button>
            <button type="button" x-on:click="tab = 'locale'" x-bind:class="{ 'ff-on': tab === 'locale' }">Locale</button>
            <button type="button" x-on:click="tab = 'business'" x-bind:class="{ 'ff-on': tab === 'business' }">Business</button>
            <button type="button" x-on:click="tab = 'privacy'" x-bind:class="{ 'ff-on': tab === 'privacy' }">Privacy</button>
        </nav>

        {{-- Each tab is a Section card with its own footer save action (profile pattern) --}}
        <section x-show="tab === 'identity'" class="ff-settings-form">
            {{ $this->identityForm }}
        </section>

        <section x-show="tab === 'locale'" x-cloak class="ff-settings-form">
            {{ $this->localeForm }}
        </section>

        <section x-show="tab === 'business'" x-cloak class="ff-settings-form">
            {{ $this->businessForm }}
        </section>

        <section x-show="tab === 'privacy'" x-cloak class="ff-settings-form">
            {{ $this->privacyForm }}
        </section>
    </div>
</x-filament-panels::page>
