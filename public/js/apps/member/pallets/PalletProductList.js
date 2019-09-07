var PalletProductList = {};

PalletProductList.initGrid = function()
{
    $( '#product_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#productToolbarTemplate' ).html() ),
            dataSource: PalletProductList.getDataSource(),
            height: 250,
            sortable: true,
            selectable: 'single',
            columns: [
                { field: 'name_en', title: 'Name' },
                { field: 'gtin', title: 'GTIN' }],
            change: function( e )
            {
                PalletProductList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                PalletProductList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

PalletProductList.getDataSource = function()
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
                        filters: PalletProductList.getFilters()
                    }
                }
            },
            schema:
            {
                model: PalletProductList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

PalletProductList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

PalletProductList.getFilters = function()
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

PalletProductList.filterGrid = function()
{
    PalletProductList.getGrid().dataSource.filter({});
}

PalletProductList.filters = function()
{
    var filters = [];

    filters.push( { app: 'products', grid: 'grid', filterName: 'search', filterValue: PalletProductList.getFilters().search() } );

    return filters;
}

PalletProductList.addListeners = function()
{
    $( 'table' ).dblclick( PalletProductList.selectProduct );
    $( '#searchProductFilter' ).keyup( PalletProductList.filterGrid );
    $( '#searchProductFilter' ).click( PalletProductList.filterGrid );
    $( '#productSelectButton' ).click( PalletProductList.selectProduct );
}

PalletProductList.selectProduct = function()
{
    var uid = ( PalletProductList.getGrid().select().data() ) ? PalletProductList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = PalletProductList.getGrid().dataSource.getByUid( uid );
        $("#product_id").val(selected.id);
        $("#product_name").val(selected.name_en);
        $("#productListContainer").data("kendoWindow").close();
        // clear batch is product is changed
        $("#batch_id").val('');
        $("#batch_code").val('');        
    }
}

PalletProductList.getGrid = function()
{
    return $( '#product_grid' ).data( 'kendoGrid' );
}

PalletProductList.setSelected = function( selectedRows )
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
    PalletProductList.initGrid();
    PalletProductList.addListeners();
});