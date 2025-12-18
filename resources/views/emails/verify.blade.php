@component('mail::message')
# Hello {{ $name }}

Thank you for registering at {{ config('app.name') }}.

Please click the button below to verify your email address:

@component('mail::button', ['url' => $verifyUrl])
Verify Email
@endcomponent

If the button does not work, simply open this link:

{{ $verifyUrl }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent



