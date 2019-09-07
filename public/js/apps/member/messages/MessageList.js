var MessageList = {};

MessageList.initGrid = function(){
    $("#MessageDetailsContainer").kendoWindow({
        actions: ["Close"],
        close: MessageList.filterGrid,
        draggable: false,
        width: "500px",
        height: "200px",
        title: "Message Details",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: MessageList.getDataSource(),
            height: $( window ).height() - 220,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'type', title: 'Type', template: "#if(type==1){# Message From Administrator #}else{# System Message #}#", width:220},
                { field: 'status', title: 'Read', template: "<input type='checkbox' class='checkbox' name='status' #if(status==1){# checked='checked'#}#>", width:60},
                { field: 'message', title: 'Message' },
                { field: 'created_at', title: 'Time', width:250}
            ],
            change: function( e )
            {
                MessageList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                MessageList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        }
    );  
    
    MessageList.getGrid().table.on('change','.checkbox',function(e){
        var status = $(this).is(":checked");
        uid = $(this).closest('tr').data('uid');
        var selected = MessageList.getGrid().dataSource.getByUid( uid );
        var message_id = selected.id;
        $(".k-grid-content.k-auto-scrollable").append("<div class='k-loading-mask' style='width:100%;height:100%'><span class='k-loading-text'>Loading...</span><div class='k-loading-image'><div class='k-loading-color'></div></div></div>");
        $.post(
            "/member/updateMessageStatus",
            {
                _token: $('[name="_token"]').val(),
                message_id: message_id,
                status: status
            },
            function(response){
                response = JSON.parse(response);
                if (response.message_id!=0) {
                    $(".k-loading-mask").remove();
                }
            }
        );
    });
}

MessageList.getDataSource = function(){
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/member/getMessages",
                    dataType: 'json',
                    data:
                    {
                        filters: MessageList.getFilters()
                    }
                }
            },
            schema:
            {
                model: MessageList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

MessageList.getModel = function(){
    return kendo.data.Model.define(
        {
            id: 'id'
        });    
}

MessageList.updateMessageStatus = function(){
    uid = this.closest('tr').data('uid');
    console.log(uid);
}
MessageList.getFilters = function(){
    var filters =
    {
        search: function()
        {
            return $( '#searchFilter' ).val();
        }
    }

    return filters;    
}

MessageList.filterGrid = function(){
    MessageList.getGrid().dataSource.filter({});    
}

MessageList.filters = function(){
    var filters = [];
    filters.push( { app: 'messages', grid: 'grid', filterName: 'search', filterValue: MessageList.getFilters().search() } );
    return filters;    
}

MessageList.addListeners = function(){
    $( 'table' ).dblclick( MessageList.editMessage );
    $("#send").click( MessageList.sendMessage );
}

MessageList.setSelected = function( selectedRows ){
    if( selectedRows.length == 1 )
        {
            $( '#editButton' ).removeClass( 'k-state-disabled' );
        }
        else
        {
            $( '#editButton' ).addClass( 'k-state-disabled' );
        }

        if( selectedRows.length > 0 )
        {
            $( '#deleteButton' ).removeClass( 'k-state-disabled' );
        }
        else
        {
            $( '#deleteButton' ).addClass( 'k-state-disabled' );
        }    
}

MessageList.getGrid = function(){
    return $( '#grid' ).data( 'kendoGrid' );    
}

MessageList.addMessage = function(){
    _message_id = 0;
    $( '#mainContentDiv' ).load( "/member/getMessageForm" );    
}

MessageList.deleteMessages = function(){
    var ids = [];
    var selected = MessageList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( MessageList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteMessages", {ids: ids, _token: $('[name="_token"]').val()}, function (response) {
            response = JSON.parse(response);
            if(response.result == 1)
            {
                MessageList.filterGrid();
            }
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected message(s)?");    
}

MessageList.editMessage = function(){
    var uid = ( MessageList.getGrid().select().data() ) ? MessageList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = MessageList.getGrid().dataSource.getByUid( uid );
        _message_id = selected.id;
        
        MessageList.showMessageForm();
    }    
}

MessageList.sendMessage = function(e){
    e.preventDefault();
    $("#info").css('display','none');

    $("#img_loader").css('display','inline');
    $("#send").prop('disabled', true);
    $("#send").css('cursor', 'not-allowed');
    $.post(
        "/member/sendMessage",
        {
            _token: $('[name="_token"]').val(),
            message: $('#message').val()
        },
        function(response){
            $("#img_loader").css('display', 'none');    
            $("#send").prop('disabled', false);    
            $("#send").css('cursor', "");            
            response = JSON.parse(response);
            if (response.message_id!=0) {
                $("#info").text("Message Sent!");
                $("#info").css('display','inline');
            }            
        }
    );
}

MessageList.showMessageForm = function(){
    $("#MessageDetailsContainer").data("kendoWindow").center();
    $("#MessageDetailsContainer").data("kendoWindow").open();
    $("#MessageDetailsContainer").load("/member/getMessageForm");
}

$( document ).ready(function(){
    $( '#tabs' ).kendoTabStrip();
    MessageList.initGrid();
    MessageList.addListeners();
});