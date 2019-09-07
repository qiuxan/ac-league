var MemberConfigurationList = {
};

MemberConfigurationList.initGrid = function()
{
    $("#memberConfigurationFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "400px",
        height: "250px",
        title: "Member Configuration",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: MemberConfigurationList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: false,
            selectable: 'multiple',
            columns: [
                { field: 'id', title: '#' },
                { field: 'company_en', title: 'Member' },
                { field: 'type', title: 'Type', values: [
                    { text: "Ver/Auth Display", value: 1 },
                    { text: "Promotion Attribute Display", value: 2 },
                    { text: "Verification Rule", value: 3 } ] },
                { field: 'variable', title: 'Variable' }
            ],
            change: function( e )
            {
                MemberConfigurationList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                MemberConfigurationList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
}

MemberConfigurationList.getDataSource = function()
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
                    url: "/admin/getMemberConfigurations",
                    dataType: 'json'
                }
            },
            schema:
            {
                model: MemberConfigurationList.getModel(),
                data: 'data',
                total: 'total'
            }
        });
}

MemberConfigurationList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

MemberConfigurationList.filterGrid = function()
{
    MemberConfigurationList.getGrid().dataSource.filter({});
}

MemberConfigurationList.editMemberConfiguration = function()
{
    var uid = ( MemberConfigurationList.getGrid().select().data() ) ? MemberConfigurationList.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = MemberConfigurationList.getGrid().dataSource.getByUid( uid );
        _member_configuration_id = selected.id;

        MemberConfigurationList.showMemberConfigurationForm();
    }
}

MemberConfigurationList.addListeners = function()
{
    $( 'table' ).dblclick( MemberConfigurationList.editMemberConfiguration );
    $( '#addButton' ).click( MemberConfigurationList.addMemberConfiguration );
    $( '#editButton' ).click( MemberConfigurationList.editMemberConfiguration );
    $( '#deleteButton' ).click( MemberConfigurationList.deleteSlides );
}

MemberConfigurationList.addMemberConfiguration = function()
{
    _member_configuration_id = 0;
    MemberConfigurationList.showMemberConfigurationForm();
}

MemberConfigurationList.setSelected = function( selectedRows )
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

MemberConfigurationList.deleteMemberConfigurations = function()
{
    var ids = [];
    var selected = MemberConfigurationList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( MemberConfigurationList.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/admin/deleteMemberConfigurations", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            MemberConfigurationList.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected configurations?");
}

MemberConfigurationList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

MemberConfigurationList.showMemberConfigurationForm = function(){
    $("#memberConfigurationFormContainer").data("kendoWindow").center();
    $("#memberConfigurationFormContainer").data("kendoWindow").open();
    $("#memberConfigurationFormContainer").load( "/admin/getMemberConfigurationForm");
}

MemberConfigurationList.refreshMemberConfigurationList = function() {
    $( "#memberConfigurationFormContainer" ).data("kendoWindow").close();
    MemberConfigurationList.filterGrid();
}

$( document ).ready( function()
{
    MemberConfigurationList.initGrid();
    MemberConfigurationList.addListeners();
});