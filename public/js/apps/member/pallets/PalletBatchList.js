var PalletBatchList = {};

PalletBatchList.initGrid = function()
{
    $( '#batch_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#batchToolbarTemplate' ).html() ),
            dataSource: PalletBatchList.getDataSource(),
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
                PalletBatchList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                PalletBatchList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

PalletBatchList.getDataSource = function()
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
                        product_id: $("#product_id").val(),
                        filters: PalletBatchList.getFilters()
                    }
                }
            },
            schema:
            {
                model: PalletBatchList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

PalletBatchList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

PalletBatchList.getFilters = function()
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

PalletBatchList.filterGrid = function()
{
    PalletBatchList.getGrid().dataSource.filter({});
}

PalletBatchList.filters = function()
{
    var filters = [];

    filters.push( { app: 'batches', grid: 'grid', filterName: 'search', filterValue: PalletBatchList.getFilters().search() } );

    return filters;
}

PalletBatchList.addListeners = function()
{
    $( 'table' ).dblclick( PalletBatchList.selectBatch );
    $( '#searchBatchFilter' ).keyup( PalletBatchList.filterGrid );
    $( '#searchBatchFilter' ).click( PalletBatchList.filterGrid );
    $( '#batchSelectButton' ).click( PalletBatchList.selectBatch );
}

PalletBatchList.selectBatch = function()
{
    var uid = ( PalletBatchList.getGrid().select().data() ) ? PalletBatchList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = PalletBatchList.getGrid().dataSource.getByUid( uid );
        $("#batch_id").val(selected.id);
        $("#batch_code").val(selected.batch_code);
        $("#batchListContainer").data("kendoWindow").close();
    }
}

PalletBatchList.getGrid = function()
{
    return $( '#batch_grid' ).data( 'kendoGrid' );
}

PalletBatchList.setSelected = function( selectedRows )
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
    PalletBatchList.initGrid();
    PalletBatchList.addListeners();
});