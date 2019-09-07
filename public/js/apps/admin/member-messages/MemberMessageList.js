var MemberMessageList = {};

MemberMessageList.initGrid = function(){
    $("#MemberMessageDetailsContainer").kendoWindow({
        actions: ["Close"],
        close: MemberMessageList.filterGrid,
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
            dataSource: MemberMessageList.getDataSource(),
            height: $( window ).height() - 220,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'company_name', title: 'Company Name', width:220},
                { field: 'status', title: 'Read', template: "<input type='checkbox' class='checkbox' name='status' #if(status==1){# checked='checked'#}#>", width:60},
                { field: 'message', title: 'Message' },
                { field: 'created_at', title: 'Time', width:250}
            ],
            change: function( e )
            {
                MemberMessageList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                MemberMessageList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        }
    );  

    $( '#sent_grid' ).kendoGrid(
        {
            dataSource: MemberMessageList.getSentMessagesDataSource(),
            height: $( window ).height() - 220,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'company_name', title: 'Company Name', width:220},
                { field: 'status', title: 'Read', template: "#if(status==1){# YES #}else{# NO #}#", width:60},
                { field: 'message', title: 'Message' },
                { field: 'created_at', title: 'Time', width:250}
            ],
            change: function( e )
            {
                MemberMessageList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                MemberMessageList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        }
    );

    MemberMessageList.getGrid().table.on('change','.checkbox',function(e){
        var status = $(this).is(":checked");
        uid = $(this).closest('tr').data('uid');
        var selected = MemberMessageList.getGrid().dataSource.getByUid( uid );
        var message_id = selected.id;
        $(".k-grid-content.k-auto-scrollable").append("<div class='k-loading-mask' style='width:100%;height:100%'><span class='k-loading-text'>Loading...</span><div class='k-loading-image'><div class='k-loading-color'></div></div></div>");
        $.post(
            "/admin/updateMemberMessageStatus",
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

MemberMessageList.getDataSource = function(){
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/admin/getMemberMessages",
                    dataType: 'json',
                    data:
                    {
                        filters: MemberMessageList.getFilters()
                    }
                }
            },
            schema:
            {
                model: MemberMessageList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

MemberMessageList.getSentMessagesDataSource = function(){
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/admin/getSentMemberMessages",
                    dataType: 'json',
                    data:
                    {
                        filters: MemberMessageList.getFilters()
                    }
                }
            },
            schema:
            {
                model: MemberMessageList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

MemberMessageList.getModel = function(){
    return kendo.data.Model.define(
        {
            id: 'id'
        });    
}

MemberMessageList.getFilters = function(){
    var filters =
    {
        search: function()
        {
            return $( '#searchFilter' ).val();
        }
    }

    return filters;    
}

MemberMessageList.filterGrid = function(){
    MemberMessageList.getGrid().dataSource.filter({});    
}

MemberMessageList.filters = function(){
    var filters = [];
    filters.push( { app: 'messages', grid: 'grid', filterName: 'search', filterValue: MemberMessageList.getFilters().search() } );
    return filters;    
}

MemberMessageList.addListeners = function(){
    $( 'table' ).dblclick( MemberMessageList.editMemberMessage );
    $("#send").click( MemberMessageList.sendMessage );
}

MemberMessageList.setSelected = function( selectedRows ){
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

MemberMessageList.getGrid = function(){
    return $( '#grid' ).data( 'kendoGrid' );    
}

MemberMessageList.addMemberMessage = function(){
    _message_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getMemberMessageForm" );    
}

MemberMessageList.deleteMemberMessages = function(){
    var ids = [];
    var selected = MemberMessageList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( MemberMessageList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/admin/deleteMemberMessages", {ids: ids, _token: $('[name="_token"]').val()}, function (response) {
            response = JSON.parse(response);
            if(response.result == 1)
            {
                MemberMessageList.filterGrid();
            }
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected message(s)?");    
}

MemberMessageList.editMemberMessage = function(){
    var uid = ( MemberMessageList.getGrid().select().data() ) ? MemberMessageList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = MemberMessageList.getGrid().dataSource.getByUid( uid );
        _message_id = selected.id;

        MemberMessageList.showMessageForm();
    }    
}

MemberMessageList.showMessageForm = function(){
    $("#MemberMessageDetailsContainer").data("kendoWindow").center();
    $("#MemberMessageDetailsContainer").data("kendoWindow").open();
    $("#MemberMessageDetailsContainer").load("/admin/getMemberMessageForm");
}

MemberMessageList.sendMessage = function(e){
    e.preventDefault();
    $("#info").css('display','none');

    $("#img_loader").css('display','inline');
    $("#send").prop('disabled', true);
    $("#send").css('cursor', 'not-allowed');
    $.post(
        "/admin/sendMemberMessage",
        {
            _token: $('[name="_token"]').val(),
            message: $('#message').val(),
            member_id: $("#company").val()
        },
        function(response){
            $("#img_loader").css('display', 'none');    
            $("#send").prop('disabled', false);    
            $("#send").css('cursor', "");            
            response = JSON.parse(response);
            if (response.success=="true") {
                $("#info").text("Message Sent!");
                $("#info").css('display','inline');
            }            
        }
    );
}

$( document ).ready(function(){
    $( '#tabs' ).kendoTabStrip();
    $( '#company' ).kendoDropDownList();
    MemberMessageList.initGrid();
    MemberMessageList.addListeners();
});