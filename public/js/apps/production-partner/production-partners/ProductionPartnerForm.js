var ProductionPartnerForm = {
    viewModel : null,
    notifier: null
}

ProductionPartnerForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: 0,
            user_id: 0,
            name: '',
            email: '',
            avatar: '',
            name_en: '',
            name_cn: '',
            name_tr: '',
            phone: '',
            address: '',
            member_id: '',
            load: function( onComplete )
            {
                var self = this;
                $.get( '/production-partner/getProductionPartner', {}, function( production_partner )
                {
                    for( var key in production_partner )
                    {
                        self.set( key, production_partner[key] );
                    }

                    if( onComplete != undefined )
                    {
                        onComplete();
                    }
                    ProductionPartnerForm.addKendoElements();
                });
            },
            noPassword: function()
            {
                return this.get( 'fakePassword' ).length == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Production Partner';
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
    $( "#cancelButton" ).click( function(){
        javascript:history.go(-1);
    } );

    $( "#saveButton" ).click( function()
    {
        ProductionPartnerForm.validateForm( false );
    });
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
    ProductionPartnerForm.notifier.notifyProgress( 'Saving Profile...' );
    $.post( "/production-partner/saveProductionPartner", $( "#productionPartnerForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.production_partner_id) > 0 )
        {
            ProductionPartnerForm.notifier.notifyComplete( 'Profile Saved' );
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
            ProductionPartnerForm.notifier.notifyError( 'Profile could not be saved' );
        }
    });
}

ProductionPartnerForm.addKendoElements = function() {

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
}

ProductionPartnerForm.onSelect = function( e )
{
    if (e.files.length > 1) {
        alert("Please select only one file");
        e.preventDefault();
    }
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
    $( "#avatar" ).val(e.response.result.location);
}

$( document ).ready( function()
{
    ProductionPartnerForm.loadViewModel();
    ProductionPartnerForm.addListeners();

    ProductionPartnerForm.notifier = Utils.notifier();
    ProductionPartnerForm.notifier.status( ProductionPartnerForm.status() );
});