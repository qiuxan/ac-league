var CartonList = {
};

CartonList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: CartonList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'sscc2_sn', title: 'SSCC2_SN' },
                // { field: 'production_partner_name', title: 'Production Partner' },
                { field: 'name_en', title: 'Product' },
                { field: 'created_at', title: 'Created At' }],
            change: function( e )
            {
                CartonList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                CartonList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

CartonList.getDataSource = function()
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
                    url: "/member/getCartons",
                    dataType: 'json',
                    data:
                    {
                        filters: CartonList.getFilters()
                    }
                }
            },
            schema:
            {
                model: CartonList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

CartonList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

CartonList.getFilters = function()
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

CartonList.filterGrid = function()
{
    CartonList.getGrid().dataSource.filter({});
}

CartonList.filters = function()
{
    var filters = [];

    filters.push( { app: 'cartons', grid: 'grid', filterName: 'search', filterValue: CartonList.getFilters().search() } );

    return filters;
}

CartonList.editCarton = function()
{
    var uid = ( CartonList.getGrid().select().data() ) ? CartonList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = CartonList.getGrid().dataSource.getByUid( uid );
        _carton_id = selected.id;

        $( '#mainContentDiv' ).load( "/member/getCartonForm" );
    }
}

CartonList.addListeners = function()
{
    $( 'table' ).dblclick( CartonList.editCarton );
    $( '#searchFilter' ).keyup( CartonList.filterGrid );
    $( '#searchFilter' ).click( CartonList.filterGrid );
    $( '#addButton' ).click( CartonList.addCarton );
    $( '#editButton' ).click( CartonList.editCarton );
    $( '#deleteButton' ).click( CartonList.deleteCartones );
}

CartonList.addCarton = function()
{
    _carton_id = 0;
    $( '#mainContentDiv' ).load( "/member/getCartonForm" );
}

CartonList.setSelected = function( selectedRows )
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

CartonList.deleteCartones = function()
{
    var ids = [];
    var selected = CartonList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( CartonList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteCartons", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            CartonList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected carton(s)? Please delete assigned rolls for this carton if need, otherwise the assigned codes will be considered as deleted.");
}

CartonList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

$( document ).ready( function()
{
    CartonList.initGrid();
    CartonList.addListeners();
});