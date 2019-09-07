var IngredientReceiptList = {
};

IngredientReceiptList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: IngredientReceiptList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'sequence_number', title: '#' },
                { field: 'tracking_code', title: 'Tracking Code' },
                { field: 'shipped_time', title: 'Shipped Time' },
                { field: 'received_time', title: 'Received Time' }],
            change: function( e )
            {
                IngredientReceiptList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                IngredientReceiptList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

IngredientReceiptList.getDataSource = function()
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
                    url: "/production-partner/getIngredientReceipts",
                    dataType: 'json',
                    data:
                    {
                        filters: IngredientReceiptList.getFilters()
                    }
                }
            },
            schema:
            {
                model: IngredientReceiptList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

IngredientReceiptList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

IngredientReceiptList.getFilters = function()
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

IngredientReceiptList.filterGrid = function()
{
    IngredientReceiptList.getGrid().dataSource.filter({});
}

IngredientReceiptList.editIngredientShipment = function()
{
    var uid = ( IngredientReceiptList.getGrid().select().data() ) ? IngredientReceiptList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = IngredientReceiptList.getGrid().dataSource.getByUid( uid );
        _ingredient_shipment_id = selected.id;

        $( '#mainContentDiv' ).load( "/production-partner/getIngredientReceiptForm" );
    }
}

IngredientReceiptList.addListeners = function()
{
    $( 'table' ).dblclick( IngredientReceiptList.editIngredientShipment );
    $( '#searchFilter' ).keyup( IngredientReceiptList.filterGrid );
    $( '#searchFilter' ).click( IngredientReceiptList.filterGrid );
    $( '#addButton' ).click( IngredientReceiptList.addIngredientShipment );
    $( '#editButton' ).click( IngredientReceiptList.editIngredientShipment );
    $( '#deleteButton' ).click( IngredientReceiptList.deleteIngredientShipments );
}

IngredientReceiptList.addIngredientShipment = function()
{
    _ingredient_shipment_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientReceiptForm" );
}

IngredientReceiptList.setSelected = function( selectedRows )
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

IngredientReceiptList.deleteIngredientShipments = function()
{
    var ids = [];
    var selected = IngredientReceiptList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( IngredientReceiptList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/production-partner/deleteIngredientReceipts", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            IngredientReceiptList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected ingredient shipments?");
}

IngredientReceiptList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    IngredientReceiptList.initGrid();
    IngredientReceiptList.addListeners();
});