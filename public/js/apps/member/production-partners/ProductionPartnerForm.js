var ProductionPartnerForm = {
    viewModel : null,
    notifier: null
}

ProductionPartnerForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _production_partner_id,
            name_en: '',
            name_cn: '',
            name_tr: '',
            phone: '',
            address: '',
            avatar: '',
            user_id: 0,
            load: function( onComplete )
            {
                var self = this;

                if( _production_partner_id )
                {
                    $.get( '/member/getProductionPartner', { id : _production_partner_id }, function( response )
                    {
                        for( var key in response )
                        {
                            self.set( key, response[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        ProductionPartnerForm.addKendoElements();
                    });
                }
                else
                {
                    ProductionPartnerForm.addKendoElements();
                }
            },
            isAccount: function()
            {
                return this.get( 'id' ) > 0 && this.get('user_id') > 0;
            }
        });

    return viewModel;
}

ProductionPartnerForm.loadViewModel = function()
{
    ProductionPartnerForm.viewModel = ProductionPartnerForm.getViewModel();
    kendo.bind( $( '#productionPartnerFormDiv' ), ProductionPartnerForm.viewModel );
    ProductionPartnerForm.viewModel.load();
}

ProductionPartnerForm.addListeners = function()
{
    $( "#cancelButton" ).click( ProductionPartnerForm.showProductionPartnerList );

    $( "#saveButton" ).click( function()
    {
        ProductionPartnerForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        ProductionPartnerForm.validateForm( true );
    });
}

ProductionPartnerForm.showProductionPartnerList = function()
{
    _production_partner_id = 0;
    $( '#mainContentDiv' ).load( "/member/getProductionPartnerList" );
}

ProductionPartnerForm.validator = function()
{
    return $( "#productionPartnerForm" ).kendoValidator().data( "kendoValidator" );
}

ProductionPartnerForm.status = function()
{
    return $( "span.status" );
}

ProductionPartnerForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

ProductionPartnerForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

ProductionPartnerForm.validateForm = function( returnToList )
{
    if( ProductionPartnerForm.validator().validate() )
    {
        ProductionPartnerForm.save( returnToList );
    }
    else
    {
        ProductionPartnerForm.notifier.notifyError( 'Please complete all required fields.' );
        ProductionPartnerForm.enableSaveButtons();
    }
}

ProductionPartnerForm.save = function( returnToList, onComplete )
{
    ProductionPartnerForm.notifier.notifyProgress( 'Saving Production Partner...' );
    $.post( "/member/saveProductionPartner", $( "#productionPartnerForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.production_partner_id) > 0 )
        {
            if( _production_partner_id == 0 )
            {
                _production_partner_id = response.production_partner_id;
            }

            ProductionPartnerForm.notifier.notifyComplete( 'Production Partner Saved' );
            ProductionPartnerForm.viewModel.set( 'id', response.production_partner_id );

            if( returnToList )
            {
                ProductionPartnerForm.showProductionPartnerList();
            }
            else
            {
                ProductionPartnerForm.viewModel.load( onComplete );
            }
        }
        else
        {
            ProductionPartnerForm.notifier.notifyError( 'Production Partner could not be saved' );
        }
    });
}

ProductionPartnerForm.addKendoElements = function() {
    $( '#tabs' ).kendoTabStrip();

    $("#role_id").kendoDropDownList();

    if (!$("#avatar_file").data('kendoUpload')) {
        $("#avatar_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: ProductionPartnerForm.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: ProductionPartnerForm.avatarUploadSuccess,
            upload: ProductionPartnerForm.onUpload
        });
    }
    ProductionPartnerForm.loadRolesGrid();
}

ProductionPartnerForm.onUpload = function( e )
{
    var files = e.files;
    e.data = { '_token': $('[name="_token"]').val() };
    $.each(files, function ()
    {
        if( this.extension.toLowerCase() != ".jpg" && this.extension.toLowerCase() != ".png" && this.extension.toLowerCase() != ".gif" && this.extension.toLowerCase() != ".jpeg" )
        {
            alert( "Only .jpg, .jpeg, .png or .gif images can be uploaded" );
            e.preventDefault();
        }
    });
}

ProductionPartnerForm.avatarUploadSuccess = function( e )
{
    ProductionPartnerForm.viewModel.set( 'avatar', e.response.result.location );
}

/*** role list ***/

ProductionPartnerForm.loadRolesGrid = function()
{
    $("#roleListContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "400px",
        height: "200px",
        title: 'Select Roles',
        resizable: true,
        modal: true,
        visible: false
    });

    if( !$( '#grid' ).data( 'kendoGrid' ) )
    {
        $( '#grid' ).kendoGrid(
            {
                toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
                dataSource: ProductionPartnerForm.getRolesDataSource(),
                scrollable: true,
                height: 200,
                sortable: true,
                selectable: 'multiple',
                columns: [
                    { field: 'id', title: '#', width: '60px' },
                    { field: 'name', title: 'Name' }],
                change: function( e )
                {
                    ProductionPartnerForm.setRoleSelected( this.select() );
                }
            });
        ProductionPartnerForm.addRoleGridListeners();
    }
}

ProductionPartnerForm.getRolesDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            transport:
            {
                read:
                {
                    url: "/member/getProductionPartnerRoles",
                    dataType: 'json',
                    data: {
                        production_partner_id: ProductionPartnerForm.getProductionPartnerId
                    }
                }
            },
            schema:
            {
                model: ProductionPartnerForm.getRoleModel()
            },
            sort: { field: 'id', dir: 'asc' }
        });
}

ProductionPartnerForm.getProductionPartnerId = function()
{
    return ProductionPartnerForm.viewModel.get('id');
}

ProductionPartnerForm.getRoleModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

ProductionPartnerForm.setRoleSelected = function( selectedRows )
{
    if( selectedRows.length > 0 )
    {
        $( '#deleteButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deleteButton' ).addClass( 'k-state-disabled' );
    }
}

ProductionPartnerForm.addRoleGridListeners = function ()
{
    $( '#deleteButton' ).click( function()
    {
        ProductionPartnerForm.deleteRoles();
    });

    $( '#addButton' ).click( function ()
    {
        ProductionPartnerForm.showRoleList();
    });
}

ProductionPartnerForm.deleteRoles = function()
{
    var ids = [];
    var selected = ProductionPartnerForm.rolesGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( ProductionPartnerForm.rolesGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function(){
        $.post( '/member/deleteProductionPartnerRoles', {production_partner_id: _production_partner_id, ids: ids, _token: $('[name="_token"]').val()}, function( response )
        {
            response = JSON.parse(response);
            if(parseInt(response.result) > 0)
            {
                ProductionPartnerForm.filterGrid();
            }
        });
    }).show('Remove Roles', 'Are you sure you want to remove selected roles?');
}

ProductionPartnerForm.rolesGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

ProductionPartnerForm.filterGrid = function()
{
    ProductionPartnerForm.rolesGrid().dataSource.filter({});
}

ProductionPartnerForm.showRoleList = function ()
{
    $( "#roleListContainer" ).data("kendoWindow").center();
    $( "#roleListContainer" ).data("kendoWindow").open();
    $( "#roleListContainer" ).load( "/member/getRoleList");
}

ProductionPartnerForm.addRoles = function ( ids )
{
    $( "#roleListContainer" ).data("kendoWindow").close();
    $.post( '/member/addProductionPartnerRoles', { production_partner_id: _production_partner_id, ids : ids, _token: $('[name="_token"]').val()}, function( response )
    {
        response = JSON.parse(response);
        if(parseInt(response.result) > 0)
        {
            ProductionPartnerForm.filterGrid();
        }
    });
}

/*** end role list ***/

$( document ).ready( function()
{
    ProductionPartnerForm.loadViewModel();
    ProductionPartnerForm.addListeners();

    ProductionPartnerForm.notifier = Utils.notifier();
    ProductionPartnerForm.notifier.status( ProductionPartnerForm.status() );
});