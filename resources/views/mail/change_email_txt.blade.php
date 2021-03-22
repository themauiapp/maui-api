Hello. A request was made to change the email address associated with {{ $name }}s account on the Maui platform
to {{ $email }}. To continue with this process, click the link below. 

{{ config('app.client_url') }}/email/confirm/{{ $token }}

Regards,
{{ config('app.name') }}