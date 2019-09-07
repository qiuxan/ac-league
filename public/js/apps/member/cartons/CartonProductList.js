var CartonProductList = {};

CartonProductList.initGrid = function()
{
    $( '#product_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#productToolbarTemplate' ).html() ),
            dataSource: CartonProductList.getDataSource(),
            height: 250,
            sortable: true,
            selectable: 'single',
            columns: [
                { field: 'name_en', title: 'Name' },
                { field: 'gtin', title: 'GTIN' }],
            change: function( e )
            {
                CartonProductList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                CartonProductList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

CartonProductList.getDataSource = function()
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
                    url: "/member/getProducts",
                    dataType: 'json',
                    data:
                    {
                        filters: CartonProductList.getFilters()
                    }
                }
            },
            schema:
            {
                model: CartonProductList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

CartonProductList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

CartonProductList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchProductFilter' ).val();
        }
    }

    return filters;
}

CartonProductList.filterGrid = function()
{
    CartonProductList.getGrid().dataSource.filter({});
}

CartonProductList.filters = function()
{
    var filters = [];

    filters.push( { app: 'products', grid: 'grid', filterName: 'search', filterValue: CartonProductList.getFilters().search() } );

    return filters;
}

CartonProductList.addListeners = function()
{
    $( 'table' ).dblclick( CartonProductList.selectProduct );
    $( '#searchProductFilter' ).keyup( CartonProductList.filterGrid );
    $( '#searchProductFilter' ).click( CartonProductList.filterGrid );
    $( '#productSelectButton' ).click( CartonProductList.selectProduct );
}

CartonProductList.selectProduct = function()
{
    var uid = ( CartonProductList.getGrid().select().data() ) ? CartonProductList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = CartonProductList.getGrid().dataSource.getByUid( uid );
        $("#product_id").val(selected.id);
        $("#product_name").val(selected.name_en);
        $("#productListContainer").data("kendoWindow").close();
        // clear batch is product is changed
        $("#batch_id").val('');
        $("#batch_code").val('');
    }
}

CartonProductList.getGrid = function()
{
    return $( '#product_grid' ).data( 'kendoGrid' );
}

CartonProductList.setSelected = function( selectedRows )
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

$( document ).ready( function()
{
    CartonProductList.initGrid();
    CartonProductList.addListeners();
});