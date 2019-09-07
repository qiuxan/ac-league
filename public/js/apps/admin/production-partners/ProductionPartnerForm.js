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
            company_en: '',
            name_en: '',
            name_cn: '',
            name_tr: '',
            phone: '',
            address: '',
            load: function( onComplete )
            {
                var self = this;

                if( _production_partner_id )
                {
                    $.get( '/admin/getProductionPartner', { id : _production_partner_id }, function( response )
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
    $( '#mainContentDiv' ).load( "/admin/getProductionPartnerList" );
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
    $.post( "/admin/saveProductionPartner", $( "#productionPartnerForm" ).serialize(), function( response )
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
    $("#member_id").kendoDropDownList();    
}

$( document ).ready( function()
{
    ProductionPartnerForm.loadViewModel();
    ProductionPartnerForm.addListeners();

    ProductionPartnerForm.notifier = Utils.notifier();
    ProductionPartnerForm.notifier.status( ProductionPartnerForm.status() );
});