var ProductList = {};

ProductList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: ProductList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'company_en', title: 'Company' },
                { field: 'name_en', title: 'Name' },
                { field: 'origin_en', title: 'Origin' },
                { field: 'gtin', title: 'GTIN' }],
            change: function( e )
            {
                ProductList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                ProductList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

ProductList.getDataSource = function()
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
                    url: "/admin/getProducts",
                    dataType: 'json',
                    data:
                    {
                        filters: ProductList.getFilters()
                    }
                }
            },
            schema:
            {
                model: ProductList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

ProductList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

ProductList.getFilters = function()
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

ProductList.filterGrid = function()
{
    ProductList.getGrid().dataSource.filter({});
}

ProductList.filters = function()
{
    var filters = [];

    filters.push( { app: 'products', grid: 'grid', filterName: 'search', filterValue: ProductList.getFilters().search() } );

    return filters;
}

ProductList.editProduct = function()
{
    var uid = ( ProductList.getGrid().select().data() ) ? ProductList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = ProductList.getGrid().dataSource.getByUid( uid );
        _product_id = selected.id;

        $( '#mainContentDiv' ).load( "/admin/getProductForm" );
    }
}

ProductList.addListeners = function()
{
    $( 'table' ).dblclick( ProductList.editProduct );
    $( '#searchFilter' ).keyup( ProductList.filterGrid );
    $( '#searchFilter' ).click( ProductList.filterGrid );
    $( '#addButton' ).click( ProductList.addProduct );
    $( '#editButton' ).click( ProductList.editProduct );
    $( '#deleteButton' ).click( ProductList.deleteProducts );
}

ProductList.addProduct = function()
{
    _product_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getProductForm" );
}

ProductList.setSelected = function( selectedRows )
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

ProductList.deleteProducts = function()
{
    var ids = [];
    var selected = ProductList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( ProductList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/admin/deleteProducts", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            ProductList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected products?");
}

ProductList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    ProductList.initGrid();
    ProductList.addListeners();
});