var IngredientLotList = {
}

IngredientLotList.initGrid = function()
{
    $( '#ingredientLotListGrid' ).kendoGrid(
        {
            dataSource: IngredientLotList.getDataSource(),
            sortable: true,
            height: 350,
            selectable: 'single',
            columns: [
                { field: 'id', title: '#', width: '60px' },
                { field: 'lot_code', title: 'Lot Code' },
                { field: 'ingredient_name', title: 'Name' }],
            change: function( e )
            {
                IngredientLotList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                IngredientLotList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

IngredientLotList.getDataSource = function()
{
    var IngredientLot = kendo.data.Model.define(
        {
            id: 'id'
        });

    var dataSource = new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/production-partner/getIngredientLotsForBatch",
                    dataType: 'json',
                    data:
                    {
                        batch_id: _batch_id,
                        filters: IngredientLotList.getFilters()
                    }
                }
            },
            schema:
            {
                model: IngredientLot,
                data: 'data',
                total: 'total'
            },
            sort: { field: 'name', dir: 'asc' }
        });

    return dataSource;
}

IngredientLotList.getFilters = function()
{
    var filters =
    {
        search : function()
        {
            return $( '#searchIngredientLot' ).val();
        }
    }

    return filters;
}

IngredientLotList.filterGrid = function()
{
    IngredientLotList.getGrid().dataSource.filter({});
}

IngredientLotList.addListeners = function()
{
    $( '#ingredientLotListGrid table' ).dblclick( function()
    {
        IngredientLotList.selectIngredientLots();
    });

    $( '#searchIngredientLot' ).keyup( IngredientLotList.filterGrid );
    $( '#searchIngredientLot' ).click( IngredientLotList.filterGrid );

    $( '#selectIngredientLot' ).click( function()
    {
        IngredientLotList.selectIngredientLots();
    });
}

IngredientLotList.setSelected = function( selectedRows )
{
    if( selectedRows.length > 0 )
    {
        $( '#selectIngredientLot' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#selectIngredientLot' ).addClass( 'k-state-disabled' );
    }
}

IngredientLotList.getGrid = function()
{
    return $( '#ingredientLotListGrid' ).data( 'kendoGrid' );
}

IngredientLotList.selectIngredientLots = function ()
{
    var selected = IngredientLotList.getGrid().select();

    var ids = [];

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( IngredientLotList.getGrid().dataItem( selected[i] )['id'] );
    }

    $.post("/production-partner/addBatchIngredients", {ids: ids, batch_id: _batch_id, _token: $('[name="_token"]').val()}, function () {
        $( "#batchIngredientListContainer" ).data("kendoWindow").close();
        BatchForm.filterIngredientGrid();
    });
}

$( document ).ready( function()
{
    IngredientLotList.initGrid();
    IngredientLotList.addListeners();
});