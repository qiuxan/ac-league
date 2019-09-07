var BatchRollForm = {
    viewModel : null,
    notifier: null
}

BatchRollForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _batch_roll_id,
            batch_id: '',
            batch_code: '',
            roll_id: '',
            start_code: '',
            end_code: '',
            code_quantity: 0,
            load: function( onComplete )
            {
                var self = this;

                if( _batch_roll_id )
                {
                    $.get( '/admin/getBatchRoll', { id : _batch_roll_id }, function( batch_roll )
                    {
                        for( var key in batch_roll )
                        {
                            self.set( key, batch_roll[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        BatchRollForm.addKendoElements();
                    });
                }
                else
                {
                    self.set( 'batch_id', _batch_id );
                    BatchRollForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            }
        });

    return viewModel;
}

BatchRollForm.loadViewModel = function()
{
    BatchRollForm.viewModel = BatchRollForm.getViewModel();
    kendo.bind( $( '#batchRollFormDiv' ), BatchRollForm.viewModel );
    BatchRollForm.viewModel.load();
}

BatchRollForm.addListeners = function()
{
    $( "#cancelButtonBatchRoll" ).click( BatchForm.refreshCodeList );

    $( "#saveButtonBatchRoll" ).click( function()
    {
        BatchRollForm.validateForm( false );
    });

    $( "#doneButtonBatchRoll" ).click( function()
    {
        BatchRollForm.validateForm( true );
    });
}

BatchRollForm.status = function()
{
    return $( "span.status" );
}

BatchRollForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

BatchRollForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

BatchRollForm.validator = function()
{
    return $( "#batchRollForm" ).kendoValidator({
        rules:
        {
            quantity: function( input )
            {
                if (input.prop( 'id' ) == 'code_quantity') {
                    return input.val() > 0;
                }
                return true;
            }
        }
    }).data( "kendoValidator" );
}

BatchRollForm.validateForm = function( returnToList )
{
    if( BatchRollForm.validator().validate() )
    {
        BatchRollForm.save( returnToList );
    }
    else
    {
        BatchRollForm.notifier.notifyError( 'Please complete all required fields.' );
        BatchRollForm.enableSaveButtons();
    }
}

BatchRollForm.save = function( returnToList, onComplete )
{
    BatchRollForm.notifier.notifyProgress( 'Saving Batch-Roll...' );
    $.post( "/admin/saveBatchRoll", $( "#batchRollForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.batch_roll_id) > 0 )
        {
            if( _batch_roll_id == 0 )
            {
                _batch_roll_id = response.batch_roll_id;
            }

            BatchRollForm.notifier.notifyComplete( 'Batch-Roll Saved' );
            BatchRollForm.viewModel.set( 'id', response.batch_roll_id );

            if( returnToList )
            {
                BatchForm.refreshCodeList();
            }
            else
            {
                BatchRollForm.viewModel.load( onComplete );
            }
        }
        else
        {
            BatchRollForm.notifier.notifyError( 'Batch-Roll could not be saved' );
        }
    });
}

BatchRollForm.addKendoElements = function() {
    $("#roll_id").kendoDropDownList({
        optionLabel: "Select A Roll",
        template: $("#roll_template").html(),
        dataTextField: "roll_code",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/admin/getMemberAvailableRolls",
                    data: {batch_id: _batch_id}
                }
            }
        },
        change: function() {
            BatchRollForm.getRollInfoForBatch();
        }
    });

    $( "#code_quantity" ).kendoNumericTextBox({
        format: "0"
    }).change(BatchRollForm.updateCode);

    $( "#start_code" ).change( function() {
        BatchRollForm.updateQuantity();
    });

    $( "#end_code" ).change( function() {
        BatchRollForm.updateQuantity();
    });

}

BatchRollForm.updateQuantity = function () {
    $.get( "/admin/getCodeQuantity", {batch_roll_id: BatchRollForm.viewModel.get('id'), roll_id: BatchRollForm.viewModel.get('roll_id'), start_code: BatchRollForm.viewModel.get('start_code'),  end_code: BatchRollForm.viewModel.get('end_code')}, function( total )
    {
        if( parseInt(total) > 0 )
        {
            BatchRollForm.viewModel.set('code_quantity', parseInt(total));
        }
        else
        {
            BatchRollForm.viewModel.set('code_quantity', 0);
            BatchRollForm.notifier.notifyError( 'Invalid info. Please check again!' );
        }
    });
}

BatchRollForm.updateCode = function () {
    $.get( "/admin/getCodeFromQuantity", {batch_roll_id: BatchRollForm.viewModel.get('id'), roll_id: BatchRollForm.viewModel.get('roll_id'), start_code: BatchRollForm.viewModel.get('start_code'), code_quantity: $("#code_quantity").val()}, function( response )
    {
        response = JSON.parse(response);
        if( response.result > 0 )
        {
            BatchRollForm.viewModel.set('end_code', response.code);
        }
        else
        {
            BatchRollForm.viewModel.set('code_quantity', 0);
            BatchRollForm.notifier.notifyError( 'Invalid request. Please check again!' );
        }
    });
}

BatchRollForm.getRollInfoForBatch = function () {
    $.get( "/admin/getRollInfoForBatch", {roll_id: BatchRollForm.viewModel.get('roll_id')}, function( response )
    {
        if( parseInt(response.total) > 0 )
        {
            BatchRollForm.viewModel.set('start_code', response.start_code);
            BatchRollForm.viewModel.set('end_code', response.end_code);
            BatchRollForm.viewModel.set('code_quantity', parseInt(response.total));
        }
        else
        {
            BatchRollForm.notifier.notifyError( 'Invalid Roll. Please check again!' );
        }
    });
}

$( document ).ready( function()
{
    BatchRollForm.loadViewModel();
    BatchRollForm.addListeners();

    BatchRollForm.notifier = Utils.notifier();
    BatchRollForm.notifier.status( BatchRollForm.status() );
});