<x-mail::message>
# Hello!

You got an invitation for '{{ $space_name }}' from {{ $user_name }}

<x-mail::button :url="$accept_url">
Accept Invitation
</x-mail::button> 

<x-mail::button :url="$reject_url" color="red">
Reject Invitation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
