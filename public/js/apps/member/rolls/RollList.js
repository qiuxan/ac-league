var RollList = {
};

RollList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: RollList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'roll_code', title: 'Roll code' },
                { field: 'quantity', title: 'Quantity' },
                { field: 'finished', title: 'Finished', values: [
                    { text: "No", value: 0 },
                    { text: "Yes", value: 1 } ] }],
            change: function( e )
            {
                RollList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                RollList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

RollList.getDataSource = function()
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
                    url: "/member/getRolls",
                    dataType: 'json',
                    data:
                    {
                        filters: RollList.getFilters()
                    }
                }
            },
            schema:
            {
                model: RollList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

RollList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

RollList.getFilters = function()
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

RollList.filterGrid = function()
{
    RollList.getGrid().dataSource.filter({});
}

RollList.filters = function()
{
    var filters = [];

    filters.push( { app: 'rolls', grid: 'grid', filterName: 'search', filterValue: RollList.getFilters().search() } );

    return filters;
}

RollList.editRoll = function()
{
    var uid = ( RollList.getGrid().select().data() ) ? RollList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = RollList.getGrid().dataSource.getByUid( uid );
        _roll_id = selected.id;

        $( '#mainContentDiv' ).load( "/member/getRollForm" );
    }
}

RollList.addListeners = function()
{
    $( 'table' ).dblclick( RollList.editRoll );
    $( '#searchFilter' ).keyup( RollList.filterGrid );
    $( '#searchFilter' ).click( RollList.filterGrid );
    $( '#editButton' ).click( RollList.editRoll );
    $( '#deleteButton' ).click( RollList.deleteRolls );
    $( '#importButton' ).hide();
    $( '#importFromURLsButton' ).hide();
}

RollList.addRoll = function()
{
    _roll_id = 0;
    $( '#mainContentDiv' ).load( "/member/getRollForm" );
}

RollList.setSelected = function( selectedRows )
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

RollList.deleteRolls = function()
{
    var ids = [];
    var selected = RollList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( RollList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteRolls", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            RollList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected rolls?");
}

RollList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    RollList.initGrid();
    RollList.addListeners();
});