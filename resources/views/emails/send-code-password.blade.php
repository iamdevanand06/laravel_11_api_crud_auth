@component('mail::message')
<h1>{{ $email['body']['line_1'] }}</h1>
<p>{{ $email['body']['line_2'] }}</p>

@component('mail::panel')
{{ $email['body']['code'] }}
@endcomponent

<p>{{ $email['body']['line_3'] }}</p>
@endcomponent
