var AvailableCodeList = {};

AvailableCodeList.initGrid = function()
{
    $( '#available_code_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#availableCodeToolbarTemplate' ).html() ),
            dataSource: AvailableCodeList.getDataSource(),
            height: 250,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'full_code', title: 'Code' },
                { field: 'batch_code', title: 'Batch#' }
            ],
            change: function( e )
            {
                AvailableCodeList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                AvailableCodeList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

AvailableCodeList.getDataSource = function()
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
                    url: "/member/getPalletAvailableCodes",
                    dataType: 'json',
                    data:
                    {
                        filters: AvailableCodeList.getFilters(),
                        pallet_id: _pallet_id
                    }
                }
            },
            schema:
            {
                model: AvailableCodeList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

AvailableCodeList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

AvailableCodeList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchAvailableCodeFilter' ).val();
        }
    }

    return filters;
}

AvailableCodeList.filterGrid = function()
{
    AvailableCodeList.getGrid().dataSource.filter({});
}

AvailableCodeList.addListeners = function()
{
    $( '#availableCodesListContainer' ).dblclick( AvailableCodeList.selectCode );
    $( '#searchAvailableCodeFilter' ).keyup( AvailableCodeList.filterGrid );
    $( '#searchAvailableCodeFilter' ).click( AvailableCodeList.filterGrid );
    $( '#availableCodeSelectButton' ).click( AvailableCodeList.selectCode );
}

AvailableCodeList.selectCode = function()
{
    var ids = [];
    var selected = AvailableCodeList.getGrid().select();

    if (selected.length < 1) {
        return;
    }

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( AvailableCodeList.getGrid().dataItem( selected[i] )['id'] );
    }

    $.post("/member/savePalletItem", {ids:ids, pallet_id:_pallet_id, _token: $('[name="_token"]').val()}, function(){
        $( '#pallet_items_grid' ).data( 'kendoGrid' ).dataSource.filter({});
        $("#availableCodesListContainer").data("kendoWindow").close();
    });
}

AvailableCodeList.getGrid = function()
{
    return $( '#available_code_grid' ).data( 'kendoGrid' );
}

AvailableCodeList.setSelected = function( selectedRows )
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
    AvailableCodeList.initGrid();
    AvailableCodeList.addListeners();
});