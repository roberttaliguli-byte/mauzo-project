@component('mail::message')
# Verify Your Email Address

Hello {{ $name }},

Please click the button below to verify your email address:

@component('mail::button', ['url' => $verifyUrl])
Verify Email Address
@endcomponent

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent