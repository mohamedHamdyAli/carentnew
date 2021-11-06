@component('mail::message')
# {{ __('mail.email_otp.body.title') }}

{{ __('mail.email_otp.body.line1') }}
{{ __('mail.email_otp.body.line2') }}

<div class="row-center">
    <span class="otp">{{$otp}}</span>
</div>

{{ __('mail.email_otp.body.line3') }}
{{ __('mail.email_otp.body.line4') }}

{{ __('mail.email_otp.body.line5') }},<br>
{{ __('app.title') }}
@endcomponent
