var CartonBatchList = {};

CartonBatchList.initGrid = function()
{
    $( '#batch_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#batchToolbarTemplate' ).html() ),
            dataSource: CartonBatchList.getDataSource(),
            height: 250,
            sortable: true,
            selectable: 'single',
            columns: [
                { field: 'batch_code', title: 'Batch #' },
                { field: 'reseller', title: 'Reseller' },
                { field: 'product_name', title: 'Product' },
                { field: 'disposition', title: 'Disposition' }],
            change: function( e )
            {
                CartonBatchList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                CartonBatchList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

CartonBatchList.getDataSource = function()
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
                    url: "/member/getBatches",
                    dataType: 'json',
                    data:
                    {
                        product_id: $('#product_id').val(),
                        filters: CartonBatchList.getFilters()
                    }
                }
            },
            schema:
            {
                model: CartonBatchList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

CartonBatchList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

CartonBatchList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchBatchFilter' ).val();
        }
    }

    return filters;
}

CartonBatchList.filterGrid = function()
{
    CartonBatchList.getGrid().dataSource.filter({});
}

CartonBatchList.filters = function()
{
    var filters = [];

    filters.push( { app: 'batches', grid: 'grid', filterName: 'search', filterValue: CartonBatchList.getFilters().search() } );

    return filters;
}

CartonBatchList.addListeners = function()
{
    $( 'table' ).dblclick( CartonBatchList.selectBatch );
    $( '#searchBatchFilter' ).keyup( CartonBatchList.filterGrid );
    $( '#searchBatchFilter' ).click( CartonBatchList.filterGrid );
    $( '#batchSelectButton' ).click( CartonBatchList.selectBatch );
}

CartonBatchList.selectBatch = function()
{
    var uid = ( CartonBatchList.getGrid().select().data() ) ? CartonBatchList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = CartonBatchList.getGrid().dataSource.getByUid( uid );
        $("#batch_id").val(selected.id);
        $("#batch_code").val(selected.batch_code);
        $("#batchListContainer").data("kendoWindow").close();
    }
}

CartonBatchList.getGrid = function()
{
    return $( '#batch_grid' ).data( 'kendoGrid' );
}

CartonBatchList.setSelected = function( selectedRows )
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
    CartonBatchList.initGrid();
    CartonBatchList.addListeners();
});