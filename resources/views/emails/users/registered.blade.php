@component('mail::message')
# Hi there,

Welcome to Dishtansya and Thanks for Signing Up!

Click the button below to login with you account.

@component('mail::button', ['url' => route('login_page')])
Login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
