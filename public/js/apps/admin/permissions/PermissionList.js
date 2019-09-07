var PermissionList = {
};

PermissionList.initGrid = function()
{
    $("#permissionFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "400px",
        height: "200px",
        title: "Permission Detail",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: PermissionList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'single',
            columns: [
                { field: 'id', title: 'ID' },
                { field: 'name', title: 'Name' },
                { field: 'guard_name', title: 'Guard Name' }],
            change: function( e )
            {
                PermissionList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                PermissionList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });

    $( '#grid' ).data( 'kendoGrid' ).table.kendoSortable({
        filter: ">tbody >tr",
        hint: $.noop,
        cursor: "move",
        placeholder: function(element) {
            return element.clone().addClass("k-state-hover").css("opacity", 0.65);
        },
        container: "#grid tbody",
        change: function(e) {
            var skip = $( '#grid' ).data( 'kendoGrid' ).dataSource.skip(),
                oldIndex = e.oldIndex + skip,
                newIndex = e.newIndex + skip,
                data = $( '#grid' ).data( 'kendoGrid' ).dataSource.data(),
                dataItem = $( '#grid' ).data( 'kendoGrid' ).dataSource.getByUid(e.item.data("uid"));

            $( '#grid' ).data( 'kendoGrid' ).dataSource.remove(dataItem);
            $( '#grid' ).data( 'kendoGrid' ).dataSource.insert(newIndex, dataItem);

            // update slides priorities
            dataItems = $( '#grid' ).data( 'kendoGrid' ).dataSource.view();
            var permission_priorities = [];
            for (i=0; i<dataItems.length; i++) {
                dataItems[i].set('priority', i+1);
                permission_priority = {
                    "id": dataItems[i]['id'],
                    "priority": dataItems[i]['priority']
                };
                permission_priorities.push(permission_priority);
            }
            permission_priorities = {
                permission_priorities: permission_priorities
            };
            PermissionList.updatePriorities(permission_priorities);
        }
    });
}

PermissionList.updatePriorities = function(permission_priorities)
{
    permission_priorities._token = $('[name="_token"]').val();
    $.post("updatePermissionPriorities", permission_priorities, function(response){
        PermissionList.filterGrid();
    });
}

PermissionList.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/admin/getPermissions",
                    dataType: 'json',
                    data:
                    {
                        filters: PermissionList.getFilters()
                    }
                }
            },
            schema:
            {
                model: PermissionList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

PermissionList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

PermissionList.getFilters = function()
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

PermissionList.filterGrid = function()
{
    PermissionList.getGrid().dataSource.filter({});
}

PermissionList.filters = function()
{
    var filters = [];

    filters.push( { app: 'permissions', grid: 'grid', filterName: 'search', filterValue: PermissionList.getFilters().search() } );

    return filters;
}

PermissionList.editPermission = function()
{
    var uid = ( PermissionList.getGrid().select().data() ) ? PermissionList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = PermissionList.getGrid().dataSource.getByUid( uid );
        _permission_id = selected.id;

        PermissionList.showPermissionForm();
    }
}

PermissionList.addPermission = function()
{
    _permission_id = 0;

    PermissionList.showPermissionForm();
}

PermissionList.addListeners = function()
{
    $( 'table' ).dblclick( PermissionList.editPermission );
    $( '#searchFilter' ).keyup( PermissionList.filterGrid );
    $( '#searchFilter' ).click( PermissionList.filterGrid );
    $( '#editButton' ).click( PermissionList.editPermission );
    $( '#addButton' ).click( PermissionList.addPermission );
}

PermissionList.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editButton' ).addClass( 'k-state-disabled' );
    }
}

PermissionList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

PermissionList.showPermissionForm = function(){
    $("#permissionFormContainer").data("kendoWindow").center();
    $("#permissionFormContainer").data("kendoWindow").open();
    $("#permissionFormContainer").load( "/admin/getPermissionForm");
}

PermissionList.refreshPermissionList = function() {
    $( "#permissionFormContainer" ).data("kendoWindow").close();
    PermissionList.filterGrid();
}

$( document ).ready( function()
{
    PermissionList.initGrid();
    PermissionList.addListeners();
});