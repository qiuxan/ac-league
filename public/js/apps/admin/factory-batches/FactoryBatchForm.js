var FactoryBatchForm = {
    viewModel : null,
    notifier: null
}

FactoryBatchForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _factory_batch_id,
            factory_batch_code: '',
            quantity: '',
            status: '',
            description: '',
            load: function( onComplete )
            {
                var self = this;

                if( _factory_batch_id )
                {
                    $.get( '/admin/getFactoryBatch', { id : _factory_batch_id }, function( factory_batch )
                    {
                        for( var key in factory_batch )
                        {
                            self.set( key, factory_batch[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        FactoryBatchForm.addKendoElements();
                    });
                }
                else
                {
                    FactoryBatchForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Factory Batch';
            }
        });

    return viewModel;
}

FactoryBatchForm.loadViewModel = function()
{
    FactoryBatchForm.viewModel = FactoryBatchForm.getViewModel();
    kendo.bind( $( '#factoryBatchFormDiv' ), FactoryBatchForm.viewModel );
    FactoryBatchForm.viewModel.load();
}

FactoryBatchForm.addListeners = function()
{
    $( "#cancelButton" ).click( FactoryBatchForm.showFactoryBatchList );

    $( "#saveButton" ).click( function()
    {
        FactoryBatchForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        FactoryBatchForm.validateForm( true );
    });
}

FactoryBatchForm.showFactoryBatchList = function()
{
    _factory_batch_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getFactoryBatchList" );
}

FactoryBatchForm.validator = function()
{
    return $( "#factoryBatchForm" ).kendoValidator().data( "kendoValidator" );
}

FactoryBatchForm.status = function()
{
    return $( "span.status" );
}

FactoryBatchForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

FactoryBatchForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

FactoryBatchForm.validateForm = function( returnToList )
{
    if( FactoryBatchForm.validator().validate() )
    {
        FactoryBatchForm.save( returnToList );
    }
    else
    {
        FactoryBatchForm.notifier.notifyError( 'Please complete all required fields.' );
        FactoryBatchForm.enableSaveButtons();
    }
}

FactoryBatchForm.save = function( returnToList, onComplete )
{
    FactoryBatchForm.notifier.notifyProgress( 'Saving FactoryBatch...' );
    $.post( "/admin/saveFactoryBatch", $( "#factoryBatchForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.factory_batch_id) > 0 )
        {
            if( _factory_batch_id == 0 )
            {
                _factory_batch_id = response.factory_batch_id;
            }

            FactoryBatchForm.notifier.notifyComplete( 'Factory Batch Saved' );
            FactoryBatchForm.viewModel.set( 'id', response.factory_batch_id );

            if( returnToList )
            {
                FactoryBatchForm.showFactoryBatchList();
            }
            else
            {
                FactoryBatchForm.viewModel.load( onComplete );
            }
        }
        else
        {
            FactoryBatchForm.notifier.notifyError( 'Factory Batch could not be saved' );
        }
    });
}

FactoryBatchForm.addKendoElements = function() {

    $("#status").kendoDropDownList();

    $( "#quantity" ).kendoNumericTextBox({
        min: 1000,
        max: 500000,
        format: "0"
    });
}

$( document ).ready( function()
{
    FactoryBatchForm.loadViewModel();
    FactoryBatchForm.addListeners();

    FactoryBatchForm.notifier = Utils.notifier();
    FactoryBatchForm.notifier.status( FactoryBatchForm.status() );
});