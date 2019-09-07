@component('mail::message')
Hello,

Please find the information of new registered member:
<table>
  <tr>
    <td>Name:</td>
    <td><strong>{{$member_name}}</strong></td>
  </tr>
  <tr>
    <td>Email:</td>
    <td>{{$member_email}}</td>
  </tr>
  <tr>
    <td>Company:</td>
    <td><strong>{{$member_company}}</strong></td>
  </tr>
  <tr>
    <td>Phone:</td>
    <td><strong>{{$member_phone}}</strong></td>
  </tr>
  <tr>
    <td>Website:</td>
    <td>{{$member_website}}</td>
  </tr>
</table>
<br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent

