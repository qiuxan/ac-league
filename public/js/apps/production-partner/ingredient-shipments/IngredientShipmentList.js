var IngredientShipmentList = {
};

IngredientShipmentList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: IngredientShipmentList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'sequence_number', title: '#' },
                { field: 'tracking_code', title: 'Tracking Code' },
                { field: 'destination_name', title: 'Destination' },
                { field: 'shipped_time', title: 'Time' }],
            change: function( e )
            {
                IngredientShipmentList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                IngredientShipmentList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

IngredientShipmentList.getDataSource = function()
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
                    url: "/production-partner/getIngredientShipments",
                    dataType: 'json',
                    data:
                    {
                        filters: IngredientShipmentList.getFilters()
                    }
                }
            },
            schema:
            {
                model: IngredientShipmentList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

IngredientShipmentList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

IngredientShipmentList.getFilters = function()
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

IngredientShipmentList.filterGrid = function()
{
    IngredientShipmentList.getGrid().dataSource.filter({});
}

IngredientShipmentList.editIngredientShipment = function()
{
    var uid = ( IngredientShipmentList.getGrid().select().data() ) ? IngredientShipmentList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = IngredientShipmentList.getGrid().dataSource.getByUid( uid );
        _ingredient_shipment_id = selected.id;

        $( '#mainContentDiv' ).load( "/production-partner/getIngredientShipmentForm" );
    }
}

IngredientShipmentList.addListeners = function()
{
    $( 'table' ).dblclick( IngredientShipmentList.editIngredientShipment );
    $( '#searchFilter' ).keyup( IngredientShipmentList.filterGrid );
    $( '#searchFilter' ).click( IngredientShipmentList.filterGrid );
    $( '#addButton' ).click( IngredientShipmentList.addIngredientShipment );
    $( '#editButton' ).click( IngredientShipmentList.editIngredientShipment );
    $( '#deleteButton' ).click( IngredientShipmentList.deleteIngredientShipments );
}

IngredientShipmentList.addIngredientShipment = function()
{
    _ingredient_shipment_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientShipmentForm" );
}

IngredientShipmentList.setSelected = function( selectedRows )
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

IngredientShipmentList.deleteIngredientShipments = function()
{
    var ids = [];
    var selected = IngredientShipmentList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( IngredientShipmentList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/production-partner/deleteIngredientShipments", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            IngredientShipmentList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected ingredient shipments?");
}

IngredientShipmentList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    IngredientShipmentList.initGrid();
    IngredientShipmentList.addListeners();
});