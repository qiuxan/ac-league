var RoleList = {};

RoleList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: RoleList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'id', title: 'ID' },
                { field: 'name', title: 'Name' },
                { field: 'guard_name', title: 'Guard Name' }],
            change: function( e )
            {
                RoleList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                RoleList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

RoleList.getDataSource = function()
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
                    url: "/admin/getRoles",
                    dataType: 'json',
                    data:
                    {
                        filters: RoleList.getFilters()
                    }
                }
            },
            schema:
            {
                model: RoleList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

RoleList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

RoleList.getFilters = function()
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

RoleList.filterGrid = function()
{
    RoleList.getGrid().dataSource.filter({});
}

RoleList.filters = function()
{
    var filters = [];

    filters.push( { app: 'roles', grid: 'grid', filterName: 'search', filterValue: RoleList.getFilters().search() } );

    return filters;
}

RoleList.editRole = function()
{
    var uid = ( RoleList.getGrid().select().data() ) ? RoleList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = RoleList.getGrid().dataSource.getByUid( uid );
        _role_id = selected.id;

        $( '#mainContentDiv' ).load( "/admin/getRoleForm", { id: _role_id, _token: $('[name="_token"]').val() } );
    }
}

RoleList.addListeners = function()
{
    $( 'table' ).dblclick( RoleList.editRole );
    $( '#searchFilter' ).keyup( RoleList.filterGrid );
    $( '#searchFilter' ).click( RoleList.filterGrid );
    $( '#addButton' ).click( RoleList.addRole );
    $( '#editButton' ).click( RoleList.editRole );
    $( '#deleteButton' ).click( RoleList.deleteRoles );
}

RoleList.addRole = function()
{
    _role_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getRoleForm" );
}

RoleList.setSelected = function( selectedRows )
{
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

RoleList.deleteRoles = function()
{
    var ids = [];
    var selected = RoleList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( RoleList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/admin/deleteRoles", {ids: ids, _token: $('[name="_token"]').val()}, function (response) {
            response = JSON.parse(response);
            if(response.result == 1)
            {
                RoleList.filterGrid();
            }
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected roles?");
}

RoleList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    RoleList.initGrid();
    RoleList.addListeners();
});