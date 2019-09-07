var MemberForm = {
    viewModel : null,
    notifier: null
}

MemberForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: 0,
            user_id: 0,
            name: '',
            email: '',
            company_en: '',
            company_cn: '',
            phone: '',
            company_email: '',
            website: '',
            country_en: '',
            country_cn: '',
            status: null,
            logo: '',
            background_image: '',
            avatar: '',
            load: function( onComplete )
            {
                var self = this;
                $.get( '/member/getMember', {}, function( member )
                {
                    for( var key in member )
                    {
                        self.set( key, member[key] );
                    }

                    if( onComplete != undefined )
                    {
                        onComplete();
                    }
                    MemberForm.addKendoElements();
                });
            },
            noPassword: function()
            {
                return this.get( 'fakePassword' ).length == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'member_id' ) != 0 ) ? this.get( 'name' ) : 'Add Member';
            }
        });

    return viewModel;
}

MemberForm.loadViewModel = function()
{
    MemberForm.viewModel = MemberForm.getViewModel();
    kendo.bind( $( '#memberFormDiv' ), MemberForm.viewModel );
    MemberForm.viewModel.load();
}

MemberForm.addListeners = function()
{
    $( "#cancelButton" ).click( function(){
        javascript:history.go(-1);
    } );

    $( "#saveButton" ).click( function()
    {
        MemberForm.validateForm( false );
    });

    $("#clearBackgroundImage").click( function()
    {
        MemberForm.viewModel.set('background_image', '');
    });
}

MemberForm.validator = function()
{
    return $( "#memberForm" ).kendoValidator().data( "kendoValidator" );
}

MemberForm.status = function()
{
    return $( "span.status" );
}

MemberForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

MemberForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

MemberForm.validateForm = function( returnToList )
{
    if( MemberForm.validator().validate() )
    {
        MemberForm.save( returnToList );
    }
    else
    {
        MemberForm.notifier.notifyError( 'Please complete all required fields.' );
        MemberForm.enableSaveButtons();
    }
}

MemberForm.save = function( returnToList, onComplete )
{
    MemberForm.notifier.notifyProgress( 'Saving Member...' );
    $.post( "/member/saveMember", $( "#memberForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.member_id) > 0 )
        {
            MemberForm.notifier.notifyComplete( 'Member Saved' );
            MemberForm.viewModel.set( 'id', response.member_id );

            if( returnToList )
            {
                MemberForm.showMemberList();
            }
            else
            {
                MemberForm.viewModel.load( onComplete );
            }
        }
        else
        {
            MemberForm.notifier.notifyError( 'Member could not be saved' );
        }
    });
}

MemberForm.addKendoElements = function() {
    if (!$("#logo_file").data('kendoUpload')) {
        $("#logo_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: MemberForm.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: MemberForm.logoUploadSuccess,
            upload: MemberForm.onUpload
        });
    }

    if (!$("#background_image_file").data('kendoUpload')) {
        $("#background_image_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: MemberForm.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: MemberForm.backgroundImageUploadSuccess,
            upload: MemberForm.onUpload
        });
    }

    if (!$("#avatar_file").data('kendoUpload')) {
        $("#avatar_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: MemberForm.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: MemberForm.avatarUploadSuccess,
            upload: MemberForm.onUpload
        });
    }
}

MemberForm.onSelect = function( e )
{
    if (e.files.length > 1) {
        alert("Please select only one file");
        e.preventDefault();
    }
}

MemberForm.onUpload = function( e )
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

MemberForm.logoUploadSuccess = function( e )
{
    $( "#logo" ).val(e.response.result.location);
}

MemberForm.backgroundImageUploadSuccess = function( e )
{
    $( "#background_image" ).val(e.response.result.location);
}

MemberForm.avatarUploadSuccess = function( e )
{
    $( "#avatar" ).val(e.response.result.location);
}

$( document ).ready( function()
{
    MemberForm.loadViewModel();
    MemberForm.addListeners();

    MemberForm.notifier = Utils.notifier();
    MemberForm.notifier.status( MemberForm.status() );
});