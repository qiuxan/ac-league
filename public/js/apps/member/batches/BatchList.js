var BatchList = {
};

BatchList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: BatchList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'batch_code', title: 'Batch #' },
                { field: 'reseller', title: 'Reseller' },
                { field: 'product_name', title: 'Product' },
                { field: 'quantity', title: 'Quantity' },
                { field: 'disposition', title: 'Disposition' },
                { field: 'created_at', title: 'Created At' }],
            change: function( e )
            {
                BatchList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                BatchList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

BatchList.getDataSource = function()
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
                        filters: BatchList.getFilters()
                    }
                }
            },
            schema:
            {
                model: BatchList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

BatchList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

BatchList.getFilters = function()
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

BatchList.filterGrid = function()
{
    BatchList.getGrid().dataSource.filter({});
}

BatchList.filters = function()
{
    var filters = [];

    filters.push( { app: 'batches', grid: 'grid', filterName: 'search', filterValue: BatchList.getFilters().search() } );

    return filters;
}

BatchList.editBatch = function()
{
    var uid = ( BatchList.getGrid().select().data() ) ? BatchList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = BatchList.getGrid().dataSource.getByUid( uid );
        _batch_id = selected.id;

        $( '#mainContentDiv' ).load( "/member/getBatchForm" );
    }
}

BatchList.addListeners = function()
{
    $( 'table' ).dblclick( BatchList.editBatch );
    $( '#searchFilter' ).keyup( BatchList.filterGrid );
    $( '#searchFilter' ).click( BatchList.filterGrid );
    $( '#addButton' ).click( BatchList.addBatch );
    $( '#editButton' ).click( BatchList.editBatch );
    $( '#deleteButton' ).click( BatchList.deleteBatches );
}

BatchList.addBatch = function()
{
    _batch_id = 0;
    $( '#mainContentDiv' ).load( "/member/getBatchForm" );
}

BatchList.setSelected = function( selectedRows )
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

BatchList.deleteBatches = function()
{
    var ids = [];
    var selected = BatchList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( BatchList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteBatches", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            BatchList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected batches? Please delete assigned rolls for this batch if need, otherwise the assigned codes will be considered as deleted.");
}

BatchList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    BatchList.initGrid();
    BatchList.addListeners();
});