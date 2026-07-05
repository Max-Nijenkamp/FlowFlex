<x-mail::message>
# Invoice {{ $number }}

Dear {{ $customerName }},

Please find invoice **{{ $number }}** attached, for a total of **{{ $total }}**, due by **{{ $dueDate }}**.

Thanks for your business,<br>
{{ $branding['name'] }}
</x-mail::message>
