var UserRequestList = {
};

UserRequestList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: UserRequestList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: {
                mode: "single",
                allowUnsort: false
            },
            columns: [   
                { field: 'full_name', title: 'Full Name', width: "12%" },
                { field: 'email', title: 'Email', width: "23%" },
                { field: 'subject', title: 'Subject', width: "15%" },              
                { field: 'message', title: 'Message', width: "35%" },              
                { field: 'status', title: 'Status', editor: UserRequestList.statusDropDownEditor, template: "#=status#", width: "7%"},
                { field: 'created_at', title: 'Time', width: "8%" }
            ],
            editable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

UserRequestList.statusDropDownEditor = function(container, options){
    $('<input required name="' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            autoBind: false,
            dataTextField: "status",
            dataValueField: "status",
            dataSource: {
                transport: {
                    read:
                    {
                        url: "/staff/getAllUserRequestStatus",
                        dataType: 'json',
                        data:
                        {
                            filters: UserRequestList.getFilters()
                        }
                    }
                }
            },
            select: UserRequestList.dropDownlistSelect
        });    
}

UserRequestList.dropDownlistSelect = function(e)
{
    status_id = this.dataItem(e.item).id;
    request_id = UserRequestList.getGrid().dataItem(this.wrapper.closest("tr")).id;
    _token = $("input[name='_token']").val();
   
    $.ajax({
        type: "POST",
        url: "/staff/updateUserRequestStatus",
        data: {
            'id': request_id,
            'status_id': status_id,
            '_token': _token
        },
        success: function(response) {},
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(xhr.responseText);
        }
    });    
}

UserRequestList.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: false,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/staff/getUserRequests",
                    dataType: 'json',
                    data:
                    {
                        filters: UserRequestList.getFilters()
                    }
                }
            },
            schema:
            {
                model: UserRequestList.getModel(),
                data: 'data',
                total: 'total'
            }
        });
}

UserRequestList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id',
            fields: {
                full_name: { editable: false, type: "string" },
                email: {editable: false, type: "string" },
                subject: {editable: false, type: "string" },
                message: {editable: false, type: "string" },
                status: {type: "string" },
            }            
        });
}

UserRequestList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchFilter' ).val();
        }
    }

    return filters;
}

UserRequestList.filterGrid = function()
{
    UserRequestList.getGrid().dataSource.filter({});
}

UserRequestList.filters = function()
{
    var filters = [];
    filters.push( { app: 'user_requests', grid: 'grid', filterName: 'search', filterValue: UserRequestList.getFilters().search() } );

    return filters;
}

UserRequestList.addListeners = function()
{
    $( '#searchFilter' ).keyup( UserRequestList.filterGrid );
    $( '#searchFilter' ).click( UserRequestList.filterGrid );
}

UserRequestList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    UserRequestList.initGrid();
    UserRequestList.addListeners();
});