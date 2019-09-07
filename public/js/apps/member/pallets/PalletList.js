var PalletList = {
};

PalletList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: PalletList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'sscc3_sn', title: 'SSCC3_SN' },
                // { field: 'production_partner_name', title: 'Production Partner' },
                { field: 'name_en', title: 'Product' },
                { field: 'created_at', title: 'Created At' }],
            change: function( e )
            {
                PalletList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                PalletList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

PalletList.getDataSource = function()
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
                    url: "/member/getPallets",
                    dataType: 'json',
                    data:
                    {
                        filters: PalletList.getFilters()
                    }
                }
            },
            schema:
            {
                model: PalletList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

PalletList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

PalletList.getFilters = function()
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

PalletList.filterGrid = function()
{
    PalletList.getGrid().dataSource.filter({});
}

PalletList.filters = function()
{
    var filters = [];

    filters.push( { app: 'pallets', grid: 'grid', filterName: 'search', filterValue: PalletList.getFilters().search() } );

    return filters;
}

PalletList.editPallet = function()
{
    var uid = ( PalletList.getGrid().select().data() ) ? PalletList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = PalletList.getGrid().dataSource.getByUid( uid );
        _pallet_id = selected.id;

        $( '#mainContentDiv' ).load( "/member/getPalletForm" );
    }
}

PalletList.addListeners = function()
{
    $( 'table' ).dblclick( PalletList.editPallet );
    $( '#searchFilter' ).keyup( PalletList.filterGrid );
    $( '#searchFilter' ).click( PalletList.filterGrid );
    $( '#addButton' ).click( PalletList.addPallet );
    $( '#editButton' ).click( PalletList.editPallet );
    $( '#deleteButton' ).click( PalletList.deletePalletes );
}

PalletList.addPallet = function()
{
    _pallet_id = 0;
    $( '#mainContentDiv' ).load( "/member/getPalletForm" );
}

PalletList.setSelected = function( selectedRows )
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

PalletList.deletePalletes = function()
{
    var ids = [];
    var selected = PalletList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( PalletList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deletePallets", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            PalletList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected pallet(s)? Please delete assigned rolls for this pallet if need, otherwise the assigned codes will be considered as deleted.");
}

PalletList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    PalletList.initGrid();
    PalletList.addListeners();
});