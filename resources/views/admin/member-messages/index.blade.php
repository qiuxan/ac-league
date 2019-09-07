@extends('layouts.admin')

@section('content')

    <div class="x_panel">
        <div class="x_title">
            <h2>Message</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            {!! csrf_field() !!}
            <script type="text/x-kendo-template" id="toolbarTemplate">
            </script>
            <div id="tabs">
                <ul>
                    <li class="k-state-active">
                        Message List
                    </li>
                    <li>
                        Sent Messages
                    </li>
                    <li>
                        Send Message to Member
                    </li>

                </ul>
                <div>
                    <div id="grid"></div>
                    <div class="clearfix"></div>                                
                </div>
                <div>
                <div id="sent_grid"></div>
                    <div class="clearfix"></div>
                </div>                            
                <div class="formWrap">
                    <form id="productForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input data-bind="value: id" name="id" type="hidden"/>
                        <input data-bind="value: user_id" name="user_id" type="hidden"/>
                        <input data-bind="value: member_id" name="member_id" type="hidden"/>
                        <fieldset class="pull-left">
                            <ul>
                                <li>
                                    Company:
                                </li>                            
                                <li>
                                    <select id="company">
                                    @foreach($members as $member)
                                        <option value="{{$member->id}}">{{$member->company_en}}</option>
                                    @endforeach
                                        <option value="-1"> All Companies </option>
                                    </select>
                                </li>                            
                                <li>
                                    Message:
                                </li>
                                <li>
                                    <textarea id="message" rows="4" cols="50" class="k-textbox" style="width:100%"></textarea>
                                </li>
                                <li>
                                    <button id="send">Send</button>
                                    <img id="img_loader" src="{{asset('css/Silver/loading.gif')}}" style="padding-left:5px;display:none"></img>
                                    <span id="info" style="display:none"></span>                                    
                                </li>
                            </ul>
                        </fieldset>
                    </form>
                </div>
                <div id="MemberMessageDetailsContainer">        
                </div>                        
            </div>            
        </div>
    </div>

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/admin/member-messages/MemberMessageList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection