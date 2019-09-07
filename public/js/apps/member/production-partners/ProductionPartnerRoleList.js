var ProductionPartnerRoleList = {
}

ProductionPartnerRoleList.initGrid = function()
{
    $( '#roleGrid' ).kendoGrid(
        {
            dataSource: ProductionPartnerRoleList.getDataSource(),
            sortable: true,
            scrollable: {
                virtual: true
            },
            selectable: 'multiple',
            columns: [{ field: 'id', title: '#' },
                { field: 'name', title: 'Name' }],
            change: function( e )
            {
                ProductionPartnerRoleList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                ProductionPartnerRoleList.setSelected( this.select() );
            }
        });
}

ProductionPartnerRoleList.getDataSource = function()
{
    var Role = kendo.data.Model.define(
        {
            id: 'id'
        });

    var dataSource = new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/member/getProductionPartnerRoleList",
                    dataType: 'json'
                }
            },
            schema:
            {
                model: Role
            },
            sort: { field: 'name', dir: 'asc' }
        });

    return dataSource;
}

ProductionPartnerRoleList.filterGrid = function()
{
    ProductionPartnerRoleList.getGrid().dataSource.filter({});
}

ProductionPartnerRoleList.addListeners = function()
{
    $( '#roleGrid table' ).dblclick( function()
    {
        ProductionPartnerRoleList.selectRoles();
    });

    $( '#selectRoles' ).click( function()
    {
        ProductionPartnerRoleList.selectRoles();
    });
}

ProductionPartnerRoleList.setSelected = function( selectedRows )
{
    if( selectedRows.length > 0 )
    {
        $( '#selectRoles' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#selectRoles' ).addClass( 'k-state-disabled' );
    }
}

ProductionPartnerRoleList.getGrid = function()
{
    return $( '#roleGrid' ).data( 'kendoGrid' );
}

ProductionPartnerRoleList.selectRoles = function ()
{
    var ids = [];
    var selected = ProductionPartnerRoleList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( ProductionPartnerRoleList.getGrid().dataItem( selected[i] )['id'] );
    }

    if( ids.length )
    {
        ProductionPartnerForm.addRoles(ids);
    }
}

$( document ).ready( function()
{
    ProductionPartnerRoleList.initGrid();
    ProductionPartnerRoleList.addListeners();
});