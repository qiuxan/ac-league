var MemberConfigurationForm = {
    viewModel : null,
    notifier: null
}

MemberConfigurationForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _member_configuration_id,
            type: 0,
            system_variable_id: '',
            deleted: '',
            load: function( onComplete )
            {
                var self = this;

                if( _member_configuration_id )
                {
                    $.get( '/admin/getMemberConfiguration', { id : _member_configuration_id }, function( member_configuration )
                    {
                        for( var key in member_configuration )
                        {
                            self.set( key, member_configuration[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        MemberConfigurationForm.addKendoElements();
                    });
                }
                else
                {
                    MemberConfigurationForm.addKendoElements();
                }
            },
            noPassword: function()
            {
                return this.get( 'fakePassword' ).length == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'member_configuration_id' ) != 0 ) ? this.get( 'name' ) : 'Add Member';
            },
            isVerAuthDisplay: function()
            {
                return this.get( 'type' ) == 1;
            },
            isPromoDisplay: function()
            {
                return this.get( 'type' ) == 2;
            },
            isVerRule: function()
            {
                return this.get( 'type' ) == 3;
            }
        });

    return viewModel;
}

MemberConfigurationForm.loadViewModel = function()
{
    MemberConfigurationForm.viewModel = MemberConfigurationForm.getViewModel();
    kendo.bind( $( '#systemVariableFormDiv' ), MemberConfigurationForm.viewModel );
    MemberConfigurationForm.viewModel.load();
}

MemberConfigurationForm.addListeners = function()
{
    $( "#cancelButton" ).click( MemberConfigurationForm.showMemberList );

    $( "#saveButton" ).click( function()
    {
        MemberConfigurationForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        MemberConfigurationForm.validateForm( true );
    });

    $("#clearBackgroundImage").click( function()
    {
        MemberConfigurationForm.viewModel.set('background_image', '');
    });
}

MemberConfigurationForm.showMemberList = function()
{
    _member_configuration_id = 0;
    MemberConfigurationList.refreshMemberConfigurationList();
}

MemberConfigurationForm.validator = function()
{
    return $( "#systemVariableForm" ).kendoValidator().data( "kendoValidator" );
}

MemberConfigurationForm.status = function()
{
    return $( "span.status" );
}

MemberConfigurationForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

MemberConfigurationForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

MemberConfigurationForm.validateForm = function( returnToList )
{
    if( MemberConfigurationForm.validator().validate() )
    {
        MemberConfigurationForm.save( returnToList );
    }
    else
    {
        MemberConfigurationForm.notifier.notifyError( 'Please complete all required fields.' );
        MemberConfigurationForm.enableSaveButtons();
    }
}

MemberConfigurationForm.save = function( returnToList, onComplete )
{
    MemberConfigurationForm.notifier.notifyProgress( 'Saving Member...' );
    $.post( "/admin/saveMemberConfiguration", $( "#systemVariableForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.member_configuration_id) > 0 )
        {
            if( _member_configuration_id == 0 )
            {
                _member_configuration_id = response.member_configuration_id;
            }

            MemberConfigurationForm.notifier.notifyComplete( 'Member Saved' );
            MemberConfigurationForm.viewModel.set( 'id', response.member_configuration_id );

            if( returnToList )
            {
                MemberConfigurationForm.showMemberList();
            }
            else
            {
                MemberConfigurationForm.viewModel.load( onComplete );
            }
        }
        else
        {
            MemberConfigurationForm.notifier.notifyError( 'Member could not be saved' );
        }
    });
}

MemberConfigurationForm.addKendoElements = function() {
    $("#member_id").kendoDropDownList();
    $("#type").kendoDropDownList({
        change: function() {
            MemberConfigurationForm.viewModel.set('system_variable_id', 0);
            MemberConfigurationForm.loadVariableList();
        }
    });

    $("#value_ver_auth_display").kendoDropDownList();

    $("#value_promotion_display").kendoDropDownList();

    MemberConfigurationForm.loadVariableList();
}

MemberConfigurationForm.loadVariableList = function () {
    $("#system_variable_id").kendoDropDownList({
        optionLabel: "Select A Variable",
        template: $("#system_variable_template").html(),
        dataTextField: "variable",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/admin/getTypeSystemVariables",
                    data: {type: MemberConfigurationForm.viewModel.get('type')}
                }
            }
        }
    });
}

$( document ).ready( function()
{
    MemberConfigurationForm.loadViewModel();
    MemberConfigurationForm.addListeners();

    MemberConfigurationForm.notifier = Utils.notifier();
    MemberConfigurationForm.notifier.status( MemberConfigurationForm.status() );
});