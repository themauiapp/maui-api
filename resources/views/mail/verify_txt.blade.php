Hello, {{ explode(' ', $user->name, 2)[1] }}. Welcome to Maui, the trusted tool for keeping track of your expenses. One more step
to complete your registration. Copy and paste this link in your browser to verify your email.

{{ config('app.client_url') }}/email/verify/{{ $user->activation_token }}

Regards,
Maui