@component('mail::message')
Hello,

Please find the information of new user request below:

Full Name: <strong>{{$request_full_name}}</strong>

Email: {{$request_email}}

Subject: <strong>{{$request_subject}}</strong>

Message: <strong>{{$request_message}}</strong>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
