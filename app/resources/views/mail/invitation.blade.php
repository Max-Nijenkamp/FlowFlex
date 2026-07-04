<x-mail::message>
# You are invited

{{ $branding['name'] }} invited you to join their FlowFlex workspace as **{{ $roleName }}**.

<x-mail::button :url="$acceptUrl">
Accept invitation
</x-mail::button>

This link is valid for 7 days. If you were not expecting this, you can ignore this email.

Thanks,<br>
{{ $branding['name'] }}
</x-mail::message>
