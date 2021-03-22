Hello {{ $name }}. A request was made to change the email address associated
with your {{ config('app.name') }} account. Copy and paste this link in your browser to continue the process. 

{{ config('app.client_url') }}/email/change/{{ $token }}

Regards,
{{ config('app.name') }}