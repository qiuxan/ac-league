var IngredientList = {
    categoryViewModel: null
}

IngredientList.initGrid = function()
{
    $( '#ingredientGrid' ).kendoGrid(
        {
            dataSource: IngredientList.getDataSource(),
            sortable: true,
            selectable: 'multiple',
            columns: [{ field: 'gtin', title: 'GTIN' },
                { field: 'name', title: 'Name' }],
            change: function( e )
            {
                IngredientList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                IngredientList.setSelected( this.select() );
            }
        });
}

IngredientList.getDataSource = function()
{
    var Ingredient = kendo.data.Model.define(
        {
            id: 'id'
        });

    var dataSource = new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 50,
            transport:
            {
                read:
                {
                    url: "/production-partner/getIngredients",
                    dataType: 'json',
                    data:
                    {
                        filters: IngredientList.getFilters()
                    }
                }
            },
            schema:
            {
                model: Ingredient,
                data: 'data',
                total: 'total'
            },
            sort: { field: 'name', dir: 'asc' }
        });

    return dataSource;
}

IngredientList.getFilters = function()
{
    var filters =
    {
        search : function()
        {
            return $( '#searchIngredient' ).val();
        }
    }

    return filters;
}

IngredientList.filterGrid = function()
{
    IngredientList.getGrid().dataSource.filter({});
}

IngredientList.addListeners = function()
{
    $( '#ingredientGrid table' ).dblclick( function()
    {
        IngredientList.selectIngredients();
    });

    $( '#searchIngredient' ).keyup( IngredientList.filterGrid );
    $( '#searchIngredient' ).click( IngredientList.filterGrid );

    $( '#selectIngredient' ).click( function()
    {
        IngredientList.selectIngredients();
    });
}

IngredientList.setSelected = function( selectedRows )
{
    if( selectedRows.length > 0 )
    {
        $( '#selectIngredient' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#selectIngredient' ).addClass( 'k-state-disabled' );
    }
}

IngredientList.getGrid = function()
{
    return $( '#ingredientGrid' ).data( 'kendoGrid' );
}

IngredientList.selectIngredients = function ()
{
    var ids = [];
    var selected = IngredientList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( IngredientList.getGrid().dataItem( selected[i] )['id'] );
    }

    if( ids.length )
    {
        ProductForm.addIngredients(ids);
    }

    $( "#ingredientListContainer" ).data("kendoWindow").close();
}

$( document ).ready( function()
{
    IngredientList.initGrid();
    IngredientList.addListeners();
});