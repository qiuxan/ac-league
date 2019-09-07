var SystemVariableForm = {
    viewModel : null,
    notifier: null
}

SystemVariableForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _system_variable_id,
            type: 0,
            variable: '',
            deleted: '',
            load: function( onComplete )
            {
                var self = this;

                if( _system_variable_id )
                {
                    $.get( '/admin/getSystemVariable', { id : _system_variable_id }, function( system_variable )
                    {
                        for( var key in system_variable )
                        {
                            self.set( key, system_variable[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        SystemVariableForm.addKendoElements();
                    });
                }
                else
                {
                    SystemVariableForm.addKendoElements();
                }
            },
            noPassword: function()
            {
                return this.get( 'fakePassword' ).length == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'system_variable_id' ) != 0 ) ? this.get( 'name' ) : 'Add Member';
            }
        });

    return viewModel;
}

SystemVariableForm.loadViewModel = function()
{
    SystemVariableForm.viewModel = SystemVariableForm.getViewModel();
    kendo.bind( $( '#systemVariableFormDiv' ), SystemVariableForm.viewModel );
    SystemVariableForm.viewModel.load();
}

SystemVariableForm.addListeners = function()
{
    $( "#cancelButton" ).click( SystemVariableForm.showVariableList );

    $( "#saveButton" ).click( function()
    {
        SystemVariableForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        SystemVariableForm.validateForm( true );
    });

    $("#clearBackgroundImage").click( function()
    {
        SystemVariableForm.viewModel.set('background_image', '');
    });
}

SystemVariableForm.showVariableList = function()
{
    _system_variable_id = 0;
    SystemVariableList.refreshSystemVariableList();
}

SystemVariableForm.validator = function()
{
    return $( "#systemVariableForm" ).kendoValidator().data( "kendoValidator" );
}

SystemVariableForm.status = function()
{
    return $( "span.status" );
}

SystemVariableForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

SystemVariableForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

SystemVariableForm.validateForm = function( returnToList )
{
    if( SystemVariableForm.validator().validate() )
    {
        SystemVariableForm.save( returnToList );
    }
    else
    {
        SystemVariableForm.notifier.notifyError( 'Please complete all required fields.' );
        SystemVariableForm.enableSaveButtons();
    }
}

SystemVariableForm.save = function( returnToList, onComplete )
{
    SystemVariableForm.notifier.notifyProgress( 'Saving System Variable...' );
    $.post( "/admin/saveSystemVariable", $( "#systemVariableForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.system_variable_id) > 0 )
        {
            if( _system_variable_id == 0 )
            {
                _system_variable_id = response.system_variable_id;
            }

            SystemVariableForm.notifier.notifyComplete( 'System Variable Saved' );
            SystemVariableForm.viewModel.set( 'id', response.system_variable_id );

            if( returnToList )
            {
                SystemVariableForm.showVariableList();
            }
            else
            {
                SystemVariableForm.viewModel.load( onComplete );
            }
        }
        else
        {
            SystemVariableForm.notifier.notifyError( 'System Variable could not be saved' );
        }
    });
}

SystemVariableForm.addKendoElements = function() {
    $("#type").kendoDropDownList();
}

$( document ).ready( function()
{
    SystemVariableForm.loadViewModel();
    SystemVariableForm.addListeners();

    SystemVariableForm.notifier = Utils.notifier();
    SystemVariableForm.notifier.status( SystemVariableForm.status() );
});