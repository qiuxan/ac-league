var FactoryBatchList = {
    STATUS_NOT_STARTED: 0,
    STATUS_IN_PROGRESS: 1,
    STATUS_DONE: 2
};

FactoryBatchList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: FactoryBatchList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'batch_code', title: 'Factory Batch Code' },
                { field: 'quantity', title: 'Quantity' },
                { field: 'description', title: 'Description' },
                { field: 'status', title: 'Status',  values: [
                    { text: "Not Started", value: FactoryBatchList.STATUS_NOT_STARTED },
                    { text: "In Progress", value: FactoryBatchList.STATUS_IN_PROGRESS },
                    { text: "Done", value: FactoryBatchList.STATUS_DONE } ] }],
            change: function( e )
            {
                FactoryBatchList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                FactoryBatchList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

FactoryBatchList.getDataSource = function()
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
                    url: "/admin/getFactoryBatches",
                    dataType: 'json',
                    data:
                    {
                        filters: FactoryBatchList.getFilters()
                    }
                }
            },
            schema:
            {
                model: FactoryBatchList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

FactoryBatchList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

FactoryBatchList.getFilters = function()
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

FactoryBatchList.filterGrid = function()
{
    FactoryBatchList.getGrid().dataSource.filter({});
}

FactoryBatchList.filters = function()
{
    var filters = [];

    filters.push( { app: 'factory_batches', grid: 'grid', filterName: 'search', filterValue: FactoryBatchList.getFilters().search() } );

    return filters;
}

FactoryBatchList.editFactoryBatch = function()
{
    var uid = ( FactoryBatchList.getGrid().select().data() ) ? FactoryBatchList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = FactoryBatchList.getGrid().dataSource.getByUid( uid );
        _factory_batch_id = selected.id;

        $( '#mainContentDiv' ).load( "/admin/getFactoryBatchForm" );
    }
}

FactoryBatchList.addListeners = function()
{
    $( 'table' ).dblclick( FactoryBatchList.editFactoryBatch );
    $( '#searchFilter' ).keyup( FactoryBatchList.filterGrid );
    $( '#searchFilter' ).click( FactoryBatchList.filterGrid );
    $( '#addButton' ).click( FactoryBatchList.addFactoryBatch );
    $( '#editButton' ).click( FactoryBatchList.editFactoryBatch );
    $( '#deleteButton' ).click( FactoryBatchList.deleteFactoryBatches );
    $( '#exportButton' ).click( FactoryBatchList.export );
    $( '#importButton' ).click( FactoryBatchList.import );
}

FactoryBatchList.addFactoryBatch = function()
{
    _factory_batch_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getFactoryBatchForm" );
}

FactoryBatchList.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editButton' ).removeClass( 'k-state-disabled' );
        $( '#exportButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editButton' ).addClass( 'k-state-disabled' );
        $( '#exportButton' ).addClass( 'k-state-disabled' );
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

FactoryBatchList.deleteFactoryBatches = function()
{
    var ids = [];
    var selected = FactoryBatchList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( FactoryBatchList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/admin/deleteFactoryBatches", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            FactoryBatchList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected factory_batches?");
}

FactoryBatchList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

FactoryBatchList.export = function() {
    var uid = ( FactoryBatchList.getGrid().select().data() ) ? FactoryBatchList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = FactoryBatchList.getGrid().dataSource.getByUid( uid );
        factory_batch_id = selected.id;
        window.open( '/admin/exportFactoryBatch/' + factory_batch_id );
    }
}

FactoryBatchList.import = function() {
    ImportFactoryBatches
        .onClose( FactoryBatchList.filterGrid );

    ImportFactoryBatches.showWindow();
}


$( document ).ready( function()
{
    FactoryBatchList.initGrid();
    FactoryBatchList.addListeners();
});