var BatchForm = {
    viewModel : null,
    notifier: null
}

BatchForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _batch_id,
            member_id: '',
            product_id: '',
            batch_code: '',
            quantity: '',
            location: '',
            disposition: '',
            load: function( onComplete )
            {
                var self = this;

                if( _batch_id )
                {
                    $.get( '/admin/getBatch', { id : _batch_id }, function( batch )
                    {
                        for( var key in batch )
                        {
                            self.set( key, batch[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        BatchForm.addKendoElements();
                    });
                }
                else
                {
                    BatchForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            isOld: function()
            {
                return this.get( 'id' ) != 0;
            },
            isAssigned: function()
            {
                return this.get( 'id' ) != 0 && this.get( 'product_id' ) != 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Batch';
            }
        });

    return viewModel;
}

BatchForm.loadViewModel = function()
{
    BatchForm.viewModel = BatchForm.getViewModel();
    kendo.bind( $( '#batchFormDiv' ), BatchForm.viewModel );
    BatchForm.viewModel.load();
}

BatchForm.addListeners = function()
{
    $( "#cancelButton" ).click( BatchForm.showBatchList );

    $( "#saveButton" ).click( function()
    {
        BatchForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        BatchForm.validateForm( true );
    });
}

BatchForm.showBatchList = function()
{
    _batch_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getBatchList" );
}

BatchForm.validator = function()
{
    return $( "#batchForm" ).kendoValidator().data( "kendoValidator" );
}

BatchForm.status = function()
{
    return $( "span.status" );
}

BatchForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

BatchForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

BatchForm.validateForm = function( returnToList )
{
    if( BatchForm.validator().validate() )
    {
        BatchForm.save( returnToList );
    }
    else
    {
        BatchForm.notifier.notifyError( 'Please complete all required fields.' );
        BatchForm.enableSaveButtons();
    }
}

BatchForm.save = function( returnToList, onComplete )
{
    BatchForm.notifier.notifyProgress( 'Saving Batch...' );
    $.post( "/admin/saveBatch", $( "#batchForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.batch_id) > 0 )
        {
            if( _batch_id == 0 )
            {
                _batch_id = response.batch_id;
            }

            BatchForm.notifier.notifyComplete( 'Batch Saved' );
            BatchForm.viewModel.set( 'id', response.batch_id );

            if( returnToList )
            {
                BatchForm.showBatchList();
            }
            else
            {
                BatchForm.viewModel.load( onComplete );
            }
        }
        else
        {
            BatchForm.notifier.notifyError( 'Batch could not be saved' );
        }
    });
}

BatchForm.addKendoElements = function() {
    $("#member_id").kendoDropDownList({
        optionLabel: "Select A Member",
        template: $("#member_template").html(),
        dataTextField: "company_en",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/admin/getMembers"
                }
            }
        },
        change: function() {
            BatchForm.viewModel.set('product_id', 0);
            BatchForm.loadProductList();
        }
    });

    BatchForm.loadProductList();

    $("#disposition").kendoDropDownList();

    $( "#quantity" ).kendoNumericTextBox({
        min: 100,
        max: 10000,
        format: "0"
    });
}

BatchForm.loadProductList = function () {
    $("#product_id").kendoDropDownList({
        optionLabel: "Select A Product",
        template: $("#product_template").html(),
        dataTextField: "name_en",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/admin/getMemberProducts",
                    data: {member_id: BatchForm.viewModel.get('member_id')}
                }
            }
        }
    });
}

$( document ).ready( function()
{
    BatchForm.loadViewModel();
    BatchForm.addListeners();

    BatchForm.notifier = Utils.notifier();
    BatchForm.notifier.status( BatchForm.status() );
});