<x-mail::layout>
{{-- Header: text wordmark (SVG logos are blocked by most email clients). --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name', 'FlowFlex') }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer: mono trust strip, brand voice. --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} FlowFlex — everything flows · EU-hosted · GDPR-first
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
