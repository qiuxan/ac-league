var IngredientList = {};

IngredientList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: IngredientList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'id', title: '#' },
                { field: 'name', title: 'Name' },
                { field: 'gtin', title: 'GTIN' },
                { field: 'origin', title: 'Origin' }],
            change: function( e )
            {
                IngredientList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                IngredientList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

IngredientList.getDataSource = function()
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
                model: IngredientList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

IngredientList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

IngredientList.getFilters = function()
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

IngredientList.filterGrid = function()
{
    IngredientList.getGrid().dataSource.filter({});
}

IngredientList.filters = function()
{
    var filters = [];

    filters.push( { app: 'ingredients', grid: 'grid', filterName: 'search', filterValue: IngredientList.getFilters().search() } );

    return filters;
}

IngredientList.editIngredient = function()
{
    var uid = ( IngredientList.getGrid().select().data() ) ? IngredientList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = IngredientList.getGrid().dataSource.getByUid( uid );
        _ingredient_id = selected.id;

        $( '#mainContentDiv' ).load( "/production-partner/getIngredientForm" );
    }
}

IngredientList.addListeners = function()
{
    $( 'table' ).dblclick( IngredientList.editIngredient );
    $( '#searchFilter' ).keyup( IngredientList.filterGrid );
    $( '#searchFilter' ).click( IngredientList.filterGrid );
    $( '#addButton' ).click( IngredientList.addIngredient );
    $( '#editButton' ).click( IngredientList.editIngredient );
    $( '#deleteButton' ).click( IngredientList.deleteIngredients );
}

IngredientList.addIngredient = function()
{
    _ingredient_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientForm" );
}

IngredientList.setSelected = function( selectedRows )
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

IngredientList.deleteIngredients = function()
{
    var ids = [];
    var selected = IngredientList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( IngredientList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/production-partner/deleteIngredients", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            IngredientList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected ingredients?");
}

IngredientList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    IngredientList.initGrid();
    IngredientList.addListeners();
});