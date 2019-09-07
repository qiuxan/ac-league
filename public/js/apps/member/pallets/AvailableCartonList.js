var AvailableCartonList = {};

AvailableCartonList.initGrid = function()
{
    $( '#available_carton_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#availableCartonToolbarTemplate' ).html() ),
            dataSource: AvailableCartonList.getDataSource(),
            height: 250,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'sscc2_sn', title: 'SSCC2 SN' },
                { field: 'batch_code', title: 'Batch#' }
            ],
            change: function( e )
            {
                AvailableCartonList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                AvailableCartonList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

AvailableCartonList.getDataSource = function()
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
                    url: "/member/getPalletAvailableCartons",
                    dataType: 'json',
                    data:
                    {
                        filters: AvailableCartonList.getFilters(),
                        pallet_id: _pallet_id
                    }
                }
            },
            schema:
            {
                model: AvailableCartonList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

AvailableCartonList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

AvailableCartonList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchAvailableCartonFilter' ).val();
        }
    }

    return filters;
}

AvailableCartonList.filterGrid = function()
{
    AvailableCartonList.getGrid().dataSource.filter({});
}

AvailableCartonList.addListeners = function()
{
    $( '#availableCartonsListContainer' ).dblclick( AvailableCartonList.selectCarton );
    $( '#searchAvailableCartonFilter' ).keyup( AvailableCartonList.filterGrid );
    $( '#searchAvailableCartonFilter' ).click( AvailableCartonList.filterGrid );
    $( '#availableCartonSelectButton' ).click( AvailableCartonList.selectCarton );
}

AvailableCartonList.selectCarton = function()
{
    var ids = [];
    var selected = AvailableCartonList.getGrid().select();

    if (selected.length < 1) {
        return;
    }

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( AvailableCartonList.getGrid().dataItem( selected[i] )['id'] );
    }

    $.post("/member/savePalletCarton", {ids:ids, pallet_id:_pallet_id, _token: $('[name="_token"]').val()}, function(){
        $( '#pallet_cartons_grid' ).data( 'kendoGrid' ).dataSource.filter({});
        $("#availableCartonsListContainer").data("kendoWindow").close();
    });
}

AvailableCartonList.getGrid = function()
{
    return $( '#available_carton_grid' ).data( 'kendoGrid' );
}

AvailableCartonList.setSelected = function( selectedRows )
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
    AvailableCartonList.initGrid();
    AvailableCartonList.addListeners();
});