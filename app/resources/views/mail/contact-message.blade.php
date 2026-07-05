<x-mail::message>
# Website contact

**From:** {{ $data->name }} ({{ $data->email }})
@if ($data->company_size)
**Company size:** {{ $data->company_size }}
@endif

---

{{ $data->message }}

*Reply directly to this email to answer — reply-to is set to the sender.*
</x-mail::message>
