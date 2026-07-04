<x-filament-panels::page>
    @if ($this->tiles->isEmpty())
        <div class="ff-hub-empty">
            <h2>Nothing switched on yet</h2>
            @if ($this->isOwner)
                <p>Activate your first module and its workspace shows up here.</p>
                <a href="{{ url('/app/module-marketplace-page') }}" class="ff-hub-cta">Open the marketplace</a>
            @else
                <p>Ask your workspace admin to switch on the modules your team needs.</p>
            @endif
        </div>
    @else
        <div class="ff-hub-grid">
            @foreach ($this->tiles as $tile)
                <a href="{{ $tile['url'] }}" class="ff-hub-tile">
                    <span class="ff-hub-square" style="background: {{ $tile['color'] }}"></span>
                    <span class="ff-hub-name">{{ $tile['name'] }}</span>
                    <span class="ff-hub-blurb">{{ $tile['blurb'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
