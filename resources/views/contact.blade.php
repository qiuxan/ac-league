@extends('layouts.public-page')

@section('content')

<div class="container">
    <header class="page-header">
        <h2 class="page-title">{{__('contact.title')}}</h2>
    </header><!-- .page-header -->
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                <h3>{{__('contact.location')}}</h3>
                </div>
                <div class="panel-body"><iframe allowfullscreen="" frameborder="0" height="450" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3147.832284621736!2d145.13987141532152!3d-37.910982679734815!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad61535f17fb9e9%3A0x3d5e7a719821109f!2s758+Blackburn+Rd%2C+Clayton+VIC+3168!5e0!3m2!1szh-CN!2sau!4v1480975816448&amp;" style="width:100%;" width="600"></iframe></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 col-lg-4 col-md-4">
            <div class="panel">
                <div class="panel-heading">
                <h3>{{__('contact.our_office')}}</h3>
                </div>

                <div class="panel-body">
                <address><strong>{{__('contact.address_line1')}}</strong><br />
                {{__('contact.address_line2')}}<br />
                {{__('contact.address_line3')}}<br />
                {{__('contact.address_line4')}}</address>
                </div>
            </div>

            <div class="panel">
                <div class="panel-heading">
                    <h3>{{__('contact.business_hours')}}</h3>
                </div>

                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>{{__('contact.day')}}</th>
                                <th>{{__('contact.time')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{__('contact.monday')}}</td>
                                <td>8:30 to 17:00</td>
                            </tr>
                            <tr>
                                <td>{{__('contact.tuesday')}}</td>
                                <td>8:30 to 17:00</td>
                            </tr>
                            <tr>
                                <td>{{__('contact.wednesday')}}</td>
                                <td>8:30 to 17:00</td>
                            </tr>
                            <tr>
                                <td>{{__('contact.thursday')}}</td>
                                <td>8:30 to 17:00</td>
                            </tr>
                            <tr>
                                <td>{{__('contact.friday')}}</td>
                                <td>8:30 to 17:00</td>
                            </tr>
                            <tr>
                                <td>{{__('contact.saturday')}}</td>
                                <td>{{__('contact.day_off')}}</td>
                            </tr>
                            <tr>
                                <td>{{__('contact.sunday')}}</td>
                                <td>{{__('contact.day_off')}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- end of office and business hours -->
        <div class="col-md-8" id="form">
            <div class="panel-heading">
                <h3>{{__('contact.leave_us_a_message')}}</h3>
            </div>
            <div class="panel panel-default contactForm">
                <div class="panel-heading">{{__('contact.leave_us_a_message')}}</div>
                <div class="panel-body">
                    <form id="message_form" class="form-horizontal" role="form" method="POST" action="contact">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="full_name" class="col-md-4 control-label">{{__('contact.full_name')}}</label>

                            <div class="col-md-6">
                                <input id="full_name" type="text" class="form-control" name="full_name" required autofocus />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">{{__('contact.email')}}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="col-md-4 control-label">{{__('contact.subject')}}</label>

                            <div class="col-md-6">
                                <input id="subject" type="text" class="form-control" name="subject" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message" class="col-md-4 control-label">{{__('contact.your_message')}}</label>

                            <div class="col-md-6">
                                <textarea id="message" class="form-control" name="message" rows="6" required> </textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button class="btn" id="submit_button">
                                    {{__('contact.submit')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>        
    </div>
</div>
<script src="{{ asset('js/apps/contact.js') }}?v=1.0.0" type="text/javascript"></script>
@endsection
