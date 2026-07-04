<x-mail::message>
# Invoice for {{ $periodLabel }}

Your FlowFlex usage for {{ $periodLabel }} comes to **{{ $formattedTotal }}**.

Payment is collected automatically. You can review the line items in your workspace under Billing.

Thanks,<br>
{{ $branding['name'] }}
</x-mail::message>
