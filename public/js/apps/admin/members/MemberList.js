var MemberList = {};

MemberList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: MemberList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'company_en', title: 'Name' },
                { field: 'company_email', title: 'Email' },
                { field: 'website', title: 'Website' },
                { field: 'country_en', title: 'Country' },
                { field: 'status', title: 'Status',  values: [
                    { text: "Inactive", value: 0 },
                    { text: "Active", value: 1 } ] }],
            change: function( e )
            {
                MemberList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                MemberList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

MemberList.getDataSource = function()
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
                    url: "/admin/getMembers",
                    dataType: 'json',
                    data:
                    {
                        filters: MemberList.getFilters()
                    }
                }
            },
            schema:
            {
                model: MemberList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

MemberList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

MemberList.getFilters = function()
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

MemberList.filterGrid = function()
{
    MemberList.getGrid().dataSource.filter({});
}

MemberList.filters = function()
{
    var filters = [];

    filters.push( { app: 'members', grid: 'grid', filterName: 'search', filterValue: MemberList.getFilters().search() } );

    return filters;
}

MemberList.editMember = function()
{
    var uid = ( MemberList.getGrid().select().data() ) ? MemberList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = MemberList.getGrid().dataSource.getByUid( uid );
        _member_id = selected.id;

        $( '#mainContentDiv' ).load( "/admin/getMemberForm", { id: _member_id, _token: $('[name="_token"]').val() } );
    }
}

MemberList.addListeners = function()
{
    $( 'table' ).dblclick( MemberList.editMember );
    $( '#searchFilter' ).keyup( MemberList.filterGrid );
    $( '#searchFilter' ).click( MemberList.filterGrid );
    $( '#addButton' ).click( MemberList.addMember );
    $( '#editButton' ).click( MemberList.editMember );
    $( '#deleteButton' ).click( MemberList.deleteMembers );
}

MemberList.addMember = function()
{
    _member_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getMemberForm" );
}

MemberList.setSelected = function( selectedRows )
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

MemberList.deleteMembers = function()
{
    var ids = [];
    var selected = MemberList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( MemberList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/admin/deleteMembers", {ids: ids, _token: $('[name="_token"]').val()}, function (response) {
            response = JSON.parse(response);
            if(response.result == 1)
            {
                MemberList.filterGrid();
            }
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected members?");
}

MemberList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    MemberList.initGrid();
    MemberList.addListeners();
});