var RoleForm = {
    viewModel : null,
    notifier: null
}

RoleForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _role_id,
            name: '',
            guard_name: '',
            load: function( onComplete )
            {
                var self = this;

                if( _role_id )
                {
                    $.get( '/admin/getRole', { id : _role_id }, function( role )
                    {
                        for( var key in role )
                        {
                            self.set( key, role[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        RoleForm.addKendoElements();
                    });
                }
                else
                {
                    RoleForm.addKendoElements();
                }
            },
            breadCrumbName: function()
            {
                return ( this.get( 'role_id' ) != 0 ) ? this.get( 'name' ) : 'Add Role';
            }
        });

    return viewModel;
}

RoleForm.loadViewModel = function()
{
    RoleForm.viewModel = RoleForm.getViewModel();
    kendo.bind( $( '#roleFormDiv' ), RoleForm.viewModel );
    RoleForm.viewModel.load();
}

RoleForm.addListeners = function()
{
    $( "#cancelButton" ).click( RoleForm.showRoleList );

    $( "#saveButton" ).click( function()
    {
        RoleForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        RoleForm.validateForm( true );
    });
}

RoleForm.showRoleList = function()
{
    _role_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getRoleList" );
}

RoleForm.validator = function()
{
    return $( "#roleForm" ).kendoValidator().data( "kendoValidator" );
}

RoleForm.status = function()
{
    return $( "span.status" );
}

RoleForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

RoleForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

RoleForm.validateForm = function( returnToList )
{
    if( RoleForm.validator().validate() )
    {
        RoleForm.save( returnToList );
    }
    else
    {
        RoleForm.notifier.notifyError( 'Please complete all required fields.' );
        RoleForm.enableSaveButtons();
    }
}

RoleForm.save = function( returnToList, onComplete )
{
    RoleForm.notifier.notifyProgress( 'Saving Role...' );
    $.post( "/admin/saveRole", $( "#roleForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.role_id) > 0 )
        {
            if( _role_id == 0 )
            {
                _role_id = response.role_id;
            }

            RoleForm.notifier.notifyComplete( 'Role Saved' );
            RoleForm.viewModel.set( 'id', response.role_id );

            if( returnToList )
            {
                RoleForm.showRoleList();
            }
            else
            {
                RoleForm.viewModel.load( onComplete );
            }
        }
        else
        {
            RoleForm.notifier.notifyError( 'Role could not be saved' );
        }
    });
}

RoleForm.addKendoElements = function() {
    $("#guard_name").kendoDropDownList();
}

$( document ).ready( function()
{
    RoleForm.loadViewModel();
    RoleForm.addListeners();

    RoleForm.notifier = Utils.notifier();
    RoleForm.notifier.status( RoleForm.status() );
});