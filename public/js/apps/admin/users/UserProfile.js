var UserProfile = {
    viewModel : null,
    notifier: null
}

UserProfile.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: 0,
            name: '',
            email: '',
            avatar: '',
            load: function( onComplete )
            {
                var self = this;
                $.get( '/admin/getUser', {}, function( User )
                {
                    for( var key in User )
                    {
                        self.set( key, User[key] );
                    }

                    if( onComplete != undefined )
                    {
                        onComplete();
                    }
                    UserProfile.addKendoElements();
                });
            },
            noPassword: function()
            {
                return this.get( 'fakePassword' ).length == 0;
            },
            breadCrumbName: function()
            {
                return 'Admin Profile';
            }
        });

    return viewModel;
}

UserProfile.loadViewModel = function()
{
    UserProfile.viewModel = UserProfile.getViewModel();
    kendo.bind( $( '#userFormDiv' ), UserProfile.viewModel );
    UserProfile.viewModel.load();
}

UserProfile.addListeners = function()
{
    $( "#cancelButton" ).click( function(){
        javascript:history.go(-1);
    } );

    $( "#saveButton" ).click( function()
    {
        UserProfile.validateForm( false );
    });
}

UserProfile.validator = function()
{
    return $( "#userForm" ).kendoValidator().data( "kendoValidator" );
}

UserProfile.status = function()
{
    return $( "span.status" );
}

UserProfile.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

UserProfile.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

UserProfile.validateForm = function( returnToList )
{
    if( UserProfile.validator().validate() )
    {
        UserProfile.save( returnToList );
    }
    else
    {
        UserProfile.notifier.notifyError( 'Please complete all required fields.' );
        UserProfile.enableSaveButtons();
    }
}

UserProfile.save = function( returnToList, onComplete )
{
    UserProfile.notifier.notifyProgress( 'Saving User...' );
    $.post( "/admin/saveUser", $( "#userForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.id) > 0 )
        {
            UserProfile.notifier.notifyComplete( 'User Saved' );
            UserProfile.viewModel.set( 'id', response.id );

            UserProfile.viewModel.load( onComplete );
        }
        else
        {
            UserProfile.notifier.notifyError( 'User could not be saved' );
        }
    });
}

UserProfile.addKendoElements = function() {

    if (!$("#avatar_file").data('kendoUpload')) {
        $("#avatar_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: UserProfile.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: UserProfile.avatarUploadSuccess,
            upload: UserProfile.onUpload
        });
    }
}

UserProfile.onSelect = function( e )
{
    if (e.files.length > 1) {
        alert("Please select only one file");
        e.preventDefault();
    }
}

UserProfile.onUpload = function( e )
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

UserProfile.avatarUploadSuccess = function( e )
{
    $( "#avatar" ).val(e.response.result.location);
}

$( document ).ready( function()
{
    UserProfile.loadViewModel();
    UserProfile.addListeners();

    UserProfile.notifier = Utils.notifier();
    UserProfile.notifier.status( UserProfile.status() );
});