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
                <li class="k-state-active">Message List</li>
                <li >Send Message to AUTB Administrator</li>
            </ul>
            <div>
                <div id="grid"></div>
                <div class="clearfix"></div>
            </div>
            <div class="formWrap">
                <form id="send_form" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: user_id" name="user_id" type="hidden"/>
                    <input data-bind="value: member_id" name="member_id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
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
        </div>
        <div id="MessageDetailsContainer">        
        </div>        
    </div>
</div>