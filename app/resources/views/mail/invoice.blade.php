<x-mail::message>
# Invoice {{ $invoice->period_start }} — {{ $invoice->period_end }}

| Module | Users | Unit | Total |
|:-------|------:|-----:|------:|
@foreach ($invoice->lines as $line)
| {{ $line['module_name'] }} | {{ $line['user_count'] }} | {{ number_format($line['unit_price_cents'] / 100, 2) }} | {{ number_format($line['line_total_cents'] / 100, 2) }} |
@endforeach

**Total: {{ $invoice->total_formatted }}**

Thanks,<br>
{{ $companyName }}
</x-mail::message>
