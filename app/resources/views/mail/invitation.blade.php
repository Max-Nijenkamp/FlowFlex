<x-mail::message>
# You're invited to {{ $companyName }}

You have been invited to join **{{ $companyName }}** on FlowFlex as **{{ $roleName }}**.

<x-mail::button :url="$inviteUrl">
Accept invitation
</x-mail::button>

This invitation expires in 7 days and can be used once.

Thanks,<br>
{{ $companyName }}
</x-mail::message>
