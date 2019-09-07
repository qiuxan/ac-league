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
                { field: 'sequence_number', title: '#', width: '60px' },
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
                    url: "/production-partner/getIngredientLotsForShipping",
                    dataType: 'json',
                    data:
                    {
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
    $( '#ingredientLotGrid table' ).dblclick( function()
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
    var selected_items = IngredientLotList.getGrid().select();
    var old_length = IngredientShipmentForm.ingredientLotsGrid().dataSource.data().length;

    var sub_total = 0;
    for(var i = 0; i < selected_items.length; i++)
    {
        var item = IngredientLotList.getGrid().dataItem( selected_items[i] );
        if(old_length > 0)
        {
            var existed = false;
            for (var j = 0; j < IngredientShipmentForm.ingredientLotsGrid().dataSource.data().length; j++)
            {
                var old_item = IngredientShipmentForm.ingredientLotsGrid().dataSource.data()[j];
                if(item.id == old_item.ingredient_lot_id)
                {
                    var existed = true;
                }
            }
            if(existed == false)
            {
                IngredientShipmentForm.ingredientLotsGrid().dataSource.add({id: 0, ingredient_lot_id: item.id, ingredient_name: item.ingredient_name, lot_code: item.lot_code });
            }
        }
        else
        {
            IngredientShipmentForm.ingredientLotsGrid().dataSource.add({id: 0, sequence_number: IngredientShipmentForm.ingredientLotsGrid().dataSource.data().length + 1, ingredient_lot_id: item.id, ingredient_name: item.ingredient_name, lot_code: item.lot_code });
        }
    }
    $( "#ingredientLotListContainer" ).data("kendoWindow").close();
}

$( document ).ready( function()
{
    IngredientLotList.initGrid();
    IngredientLotList.addListeners();
});