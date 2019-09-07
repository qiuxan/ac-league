var CodeList = {
};

CodeList.initGrid = function()
{
    $("#codeFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "400px",
        height: "290px",
        title: "Code Detail",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: CodeList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'single',
            columns: [
                { field: 'batch_code', title: 'Batch' },
                { field: 'roll_code', title: 'Roll' },
                { field: 'full_code', title: 'Serial Number' },
                { field: 'disposition', title: 'Disposition' },
                { field: 'reseller', title: 'Reseller' },
                { field: 'updated_at', title: 'Updated' }],
            change: function( e )
            {
                CodeList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                CodeList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

CodeList.getDataSource = function()
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
                    url: "/admin/getCodes",
                    dataType: 'json',
                    data:
                    {
                        filters: CodeList.getFilters()
                    }
                }
            },
            schema:
            {
                model: CodeList.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

CodeList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

CodeList.getFilters = function()
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

CodeList.filterGrid = function()
{
    CodeList.getGrid().dataSource.filter({});
}

CodeList.filters = function()
{
    var filters = [];

    filters.push( { app: 'codes', grid: 'grid', filterName: 'search', filterValue: CodeList.getFilters().search() } );

    return filters;
}

CodeList.editCode = function()
{
    var uid = ( CodeList.getGrid().select().data() ) ? CodeList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = CodeList.getGrid().dataSource.getByUid( uid );
        _code_id = selected.id;

        CodeList.showCodeForm();
    }
}

CodeList.addListeners = function()
{
    $( 'table' ).dblclick( CodeList.editCode );
    $( '#searchFilter' ).keyup( CodeList.filterGrid );
    $( '#searchFilter' ).click( CodeList.filterGrid );
    $( '#editButton' ).click( CodeList.editCode );
}

CodeList.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editButton' ).addClass( 'k-state-disabled' );
    }
}

CodeList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

CodeList.showCodeForm = function(){
    $("#codeFormContainer").data("kendoWindow").center();
    $("#codeFormContainer").data("kendoWindow").open();
    $("#codeFormContainer").load( "/admin/getCodeForm");
}

CodeList.refreshCodeList = function() {
    $( "#codeFormContainer" ).data("kendoWindow").close();
    CodeList.filterGrid();
}

$( document ).ready( function()
{
    CodeList.initGrid();
    CodeList.addListeners();
});