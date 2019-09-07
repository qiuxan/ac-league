var PostForm = {
    viewModel : null,
    notifier: null
}

PostForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _post_id,
            category_id: '',
            language: '',
            icon: '',
            feature_image: '',
            title: '',
            alias: '',
            excerpt: '',
            content: '',
            published: '',
            load: function( onComplete )
            {
                var self = this;

                if( _post_id )
                {
                    $.get( '/staff/getPost', { id : _post_id }, function( post )
                    {
                        for( var key in post )
                        {
                            self.set( key, post[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        PostForm.addKendoElements();
                    });
                }
                else
                {
                    PostForm.addKendoElements();
                }
            }
        });

    return viewModel;
}

PostForm.setPublishedCheckbox = function()
{
    $( "#published" ).prop( 'checked', true );
}

PostForm.loadViewModel = function()
{
    PostForm.viewModel = PostForm.getViewModel();
    kendo.bind( $( '#postFormDiv' ), PostForm.viewModel );
    PostForm.viewModel.load();
}

PostForm.addListeners = function()
{
    $( "#cancelButton" ).click( PostForm.showPostList );

    $( "#saveButton" ).click( function()
    {
        PostForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        PostForm.validateForm( true );
    });

    $( "#title" ).keyup( function(){
        PostForm.generateSlug($("#title").val());
    } );
}

PostForm.generateSlug = function( title ){
    $( "#alias" ).val(
        title
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
    );
}

PostForm.showPostList = function()
{
    _post_id = 0;
    $( '#mainContentDiv' ).load( "/staff/getPostList" );
}

PostForm.validator = function()
{
    return $( "#postForm" ).kendoValidator().data( "kendoValidator" );
}

PostForm.status = function()
{
    return $( "span.status" );
}

PostForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

PostForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

PostForm.validateForm = function( returnToList )
{
    if( PostForm.validator().validate() )
    {
        PostForm.save( returnToList );
    }
    else
    {
        PostForm.notifier.notifyError( 'Please complete all required fields.' );
        PostForm.enableSaveButtons();
    }
}

PostForm.save = function( returnToList, onComplete )
{
    PostForm.notifier.notifyProgress( 'Saving Post...' );
    $.post( "/staff/savePost", $( "#postForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.post_id) > 0 )
        {
            if( _post_id == 0 )
            {
                _post_id = response.post_id;
            }

            PostForm.notifier.notifyComplete( 'Post Saved' );
            PostForm.viewModel.set( 'id', response.post_id );

            if( returnToList )
            {
                PostForm.showPostList();
            }
            else
            {
                PostForm.viewModel.load( onComplete );
            }
        }
        else
        {
            if ( 'alias_duplicated' in response) {
                PostForm.notifier.notifyError( 'Please choose another alias for the post.' );
            } else {
                PostForm.notifier.notifyError( 'Post could not be saved' );
            }
        }
    });
}

PostForm.addKendoElements = function() {
    $("#category_id").kendoDropDownList({
        optionLabel: "Select A Category",
        template: $("#category_template").html(),
        dataTextField: "category",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/staff/getPostCategories"
                }
            }
        }
    });

    $("#language").kendoDropDownList();

    if (!$("#feature_image_file").data('kendoUpload')) {
        $("#feature_image_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: PostForm.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: PostForm.featureImageUploadSuccess,
            upload: PostForm.onUpload
        });
    }

    if( !PostForm.editor )
    {
        PostForm.editor = $( 'textarea#content' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    $( "#date" ).kendoDatePicker();
}

PostForm.onSelect = function( e )
{
    if (e.files.length > 1) {
        alert("Please select only one file");
        e.preventDefault();
    }
}

PostForm.onUpload = function( e )
{
    var files = e.files;
    e.data = { '_token': $('[name="_token"]').val() };
    $.each(files, function ()
    {
        if( this.extension.toLowerCase() != ".jpg" && this.extension.toLowerCase() != ".png" && this.extension.toLowerCase() != ".gif" )
        {
            alert( "Only .jpg, .png or .gif images can be uploaded" );
            e.preventDefault();
        }
    });
}

PostForm.featureImageUploadSuccess = function( e )
{
    $( "#feature_image" ).val(e.response.result.location);
}

$( document ).ready( function()
{
    PostForm.loadViewModel();
    PostForm.addListeners();

    PostForm.notifier = Utils.notifier();
    PostForm.notifier.status( PostForm.status() );
});