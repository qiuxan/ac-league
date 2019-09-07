var PermissionForm = {
    viewModel : null,
    notifier: null
}

PermissionForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _permission_id,
            name: '',
            guard_name: '',
            load: function( onComplete )
            {
                var self = this;

                if( _permission_id )
                {
                    $.get( '/admin/getPermission', { id : _permission_id }, function( permission )
                    {
                        for( var key in permission )
                        {
                            self.set( key, permission[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        PermissionForm.addKendoElements();
                    });
                }
                else
                {
                    PermissionForm.addKendoElements();
                }
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Permission';
            }
        });

    return viewModel;
}

PermissionForm.loadViewModel = function()
{
    PermissionForm.viewModel = PermissionForm.getViewModel();
    kendo.bind( $( '#permissionForm' ), PermissionForm.viewModel );
    PermissionForm.viewModel.load();
}

PermissionForm.addListeners = function()
{
    $( "#cancelButtonPermissionForm" ).click(
        function() {
            $( "#permissionFormContainer" ).data("kendoWindow").close();
            PermissionList.refreshPermissionList();
        }
    );

    $( "#saveButtonPermissionForm" ).click( function()
    {
        PermissionForm.validateForm( false );
    });

    $( "#doneButtonPermissionForm" ).click( function()
    {
        PermissionForm.validateForm( true );
    });
}

PermissionForm.showPermissionList = function()
{
    _permission_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getPermissionList" );
}

PermissionForm.validator = function()
{
    return $( "#permissionForm" ).kendoValidator().data( "kendoValidator" );
}

PermissionForm.status = function()
{
    return $( "span.status" );
}

PermissionForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

PermissionForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

PermissionForm.validateForm = function( returnToList )
{
    if( PermissionForm.validator().validate() )
    {
        PermissionForm.save( returnToList );
    }
    else
    {
        PermissionForm.notifier.notifyError( 'Please complete all required fields.' );
        PermissionForm.enableSaveButtons();
    }
}

PermissionForm.save = function( returnToList, onComplete )
{
    PermissionForm.notifier.notifyProgress( 'Saving Permission...' );
    $.post( "/admin/savePermission", $( "#permissionForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.permission_id) > 0 )
        {
            if( _permission_id == 0 )
            {
                _permission_id = response.permission_id;
            }

            PermissionForm.notifier.notifyComplete( 'Permission Saved' );
            PermissionForm.viewModel.set( 'id', response.permission_id );

            if( returnToList )
            {
                $( "#permissionFormContainer" ).data("kendoWindow").close();
                PermissionList.refreshPermissionList();
            }
            else
            {
                PermissionForm.viewModel.load( onComplete );
            }
        }
        else
        {
            PermissionForm.notifier.notifyError( 'Permission could not be saved' );
        }
    });
}

PermissionForm.addKendoElements = function() {
    $("#guard_name").kendoDropDownList();
}

$( document ).ready( function()
{
    PermissionForm.loadViewModel();
    PermissionForm.addListeners();

    PermissionForm.notifier = Utils.notifier();
    PermissionForm.notifier.status( PermissionForm.status() );
});