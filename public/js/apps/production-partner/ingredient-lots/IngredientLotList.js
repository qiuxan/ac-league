var IngredientLotList = {
};

IngredientLotList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: IngredientLotList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'lot_code', title: 'Lot #' },
                { field: 'ingredient_name', title: 'Ingredient' },
                { field: 'production_date', title: 'Production Date' },
                { field: 'expiration_date', title: 'Expiration Date' }],
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
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/production-partner/getIngredientLots",
                    dataType: 'json',
                    data:
                    {
                        filters: IngredientLotList.getFilters()
                    }
                }
            },
            schema:
            {
                model: IngredientLotList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

IngredientLotList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

IngredientLotList.getFilters = function()
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

IngredientLotList.filterGrid = function()
{
    IngredientLotList.getGrid().dataSource.filter({});
}

IngredientLotList.editIngredientLot = function()
{
    var uid = ( IngredientLotList.getGrid().select().data() ) ? IngredientLotList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = IngredientLotList.getGrid().dataSource.getByUid( uid );
        _ingredient_lot_id = selected.id;

        $( '#mainContentDiv' ).load( "/production-partner/getIngredientLotForm" );
    }
}

IngredientLotList.addListeners = function()
{
    $( 'table' ).dblclick( IngredientLotList.editIngredientLot );
    $( '#searchFilter' ).keyup( IngredientLotList.filterGrid );
    $( '#searchFilter' ).click( IngredientLotList.filterGrid );
    $( '#addButton' ).click( IngredientLotList.addIngredientLot );
    $( '#editButton' ).click( IngredientLotList.editIngredientLot );
    $( '#deleteButton' ).click( IngredientLotList.deleteIngredientLots );
}

IngredientLotList.addIngredientLot = function()
{
    _ingredient_lot_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientLotForm" );
}

IngredientLotList.setSelected = function( selectedRows )
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

IngredientLotList.deleteIngredientLots = function()
{
    var ids = [];
    var selected = IngredientLotList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( IngredientLotList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/production-partner/deleteIngredientLots", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            IngredientLotList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected ingredient lots?");
}

IngredientLotList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    IngredientLotList.initGrid();
    IngredientLotList.addListeners();
});