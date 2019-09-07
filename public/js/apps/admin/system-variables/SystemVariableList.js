var SystemVariableList = {
};

SystemVariableList.initGrid = function()
{
    $("#systemVariableFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "400px",
        height: "200px",
        title: "Variable Detail",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: SystemVariableList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: false,
            selectable: 'multiple',
            columns: [
                { field: 'id', title: '#' },
                { field: 'type', title: 'Type', values: [
                    { text: "Ver/Auth Display", value: 1 },
                    { text: "Promotion Attribute Display", value: 2 },
                    { text: "Verification Rule", value: 3 } ] },
                { field: 'variable', title: 'Variable' }
            ],
            change: function( e )
            {
                SystemVariableList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                SystemVariableList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

SystemVariableList.getDataSource = function()
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
                    url: "/admin/getSystemVariables",
                    dataType: 'json'
                }
            },
            schema:
            {
                model: SystemVariableList.getModel(),
                data: 'data',
                total: 'total'
            }
        });
}

SystemVariableList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

SystemVariableList.filterGrid = function()
{
    SystemVariableList.getGrid().dataSource.filter({});
}

SystemVariableList.editSystemVariable = function()
{
    var uid = ( SystemVariableList.getGrid().select().data() ) ? SystemVariableList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = SystemVariableList.getGrid().dataSource.getByUid( uid );
        _system_variable_id = selected.id;

        SystemVariableList.showSystemVariableForm();
    }
}

SystemVariableList.addListeners = function()
{
    $( 'table' ).dblclick( SystemVariableList.editSystemVariable );
    $( '#addButton' ).click( SystemVariableList.addSystemVariable );
    $( '#editButton' ).click( SystemVariableList.editSystemVariable );
    $( '#deleteButton' ).click( SystemVariableList.deleteSlides );
}

SystemVariableList.addSystemVariable = function()
{
    _system_variable_id = 0;
    SystemVariableList.showSystemVariableForm();
}

SystemVariableList.setSelected = function( selectedRows )
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

SystemVariableList.deleteSystemVariables = function()
{
    var ids = [];
    var selected = SystemVariableList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( SystemVariableList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/admin/deleteSystemVariables", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            SystemVariableList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected variables?");
}

SystemVariableList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

SystemVariableList.showSystemVariableForm = function(){
    $("#systemVariableFormContainer").data("kendoWindow").center();
    $("#systemVariableFormContainer").data("kendoWindow").open();
    $("#systemVariableFormContainer").load( "/admin/getSystemVariableForm");
}

SystemVariableList.refreshSystemVariableList = function() {
    $( "#systemVariableFormContainer" ).data("kendoWindow").close();
    SystemVariableList.filterGrid();
}

$( document ).ready( function()
{
    SystemVariableList.initGrid();
    SystemVariableList.addListeners();
});