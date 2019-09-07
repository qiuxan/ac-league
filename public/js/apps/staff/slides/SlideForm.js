var SlideForm = {
    viewModel : null,
    notifier: null
}

SlideForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _slide_id,
            language: '',
            location: '',
            file_id: '',
            title: '',
            published: '',
            load: function( onComplete )
            {
                var self = this;

                if( _slide_id )
                {
                    $.get( '/staff/getSlide', { id : _slide_id }, function( slide )
                    {
                        for( var key in slide )
                        {
                            if (key=='priority') {
                                $(" #priority ").val(slide['priority']);
                            }

                            self.set( key, slide[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        SlideForm.addKendoElements();
                        
                        // make the image upload field not required since the image is loaded
                        $( "#slide_image_file" ).prop("required", false);
                    });

                }
                else
                {
                    SlideForm.addKendoElements();
                }
            }
        });

    return viewModel;
}

SlideForm.setPublishedCheckbox = function()
{
    $( "#published" ).prop( 'checked', true );
}

SlideForm.loadViewModel = function()
{
    SlideForm.viewModel = SlideForm.getViewModel();
    kendo.bind( $( '#slideFormDiv' ), SlideForm.viewModel );
    SlideForm.viewModel.load();
}

SlideForm.addListeners = function()
{
    $( "#cancelButton" ).click( SlideForm.showSlideList );

    $( "#saveButton" ).click( function()
    {
        SlideForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        SlideForm.validateForm( true );
    });
}

SlideForm.showSlideList = function()
{
    _slide_id = 0;
    $( '#mainContentDiv' ).load( "/staff/getSlideList" );
}

SlideForm.validator = function()
{
    return $( "#slideForm" ).kendoValidator().data( "kendoValidator" );
}

SlideForm.status = function()
{
    return $( "span.status" );
}

SlideForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

SlideForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

SlideForm.validateForm = function( returnToList )
{
    if( SlideForm.validator().validate() )
    {
        SlideForm.save( returnToList );
    }
    else
    {
        SlideForm.notifier.notifyError( 'Please complete all required fields.' );
        SlideForm.enableSaveButtons();
    }
}

SlideForm.save = function( returnToList, onComplete )
{
    SlideForm.notifier.notifyProgress( 'Saving Slide...' );

    if ($(" #priority ").val()) {
        SlideForm.postSave(returnToList, onComplete);
    } else {
        // get the existing largest priority in slides 
        $.get( "/staff/getLargestSlidePriority", function ( response ) {
            // set the priority of current slide
            $( "#priority" ).val(response + 1);
            SlideForm.postSave();
        });
    }
}

SlideForm.postSave = function(returnToList, onComplete) {
    $.post( "/staff/saveSlide", $( "#slideForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.slide_id) > 0 )
        {
            if( _slide_id == 0 )
            {
                _slide_id = response.slide_id;
            }

            SlideForm.notifier.notifyComplete( 'Slide Saved' );
            SlideForm.viewModel.set( 'id', response.slide_id );

            if( returnToList )
            {
                SlideForm.showSlideList();
            }
            else
            {
                SlideForm.viewModel.load( onComplete );
            }
        }
        else
        {
            SlideForm.notifier.notifyError( 'Slide could not be saved' );
        }
    });
}

SlideForm.addKendoElements = function() {
    if (!$("#slide_image_file").data('kendoUpload')) {
        $("#slide_image_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: SlideForm.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: SlideForm.slideImageUploadSuccess,
            upload: SlideForm.onUpload
        });
    }

    $("#language").kendoDropDownList();
}

SlideForm.onSelect = function( e )
{
    if (e.files.length > 1) {
        alert("Please select only one file");
        e.preventDefault();
    }
}

SlideForm.onUpload = function( e )
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

SlideForm.slideImageUploadSuccess = function( e )
{
    $( "#slide_image" ).val(e.response.result.id);
    $( "#slide_image_file" ).prop("required", false);
}

$( document ).ready( function()
{
    SlideForm.loadViewModel();
    SlideForm.addListeners();

    SlideForm.notifier = Utils.notifier();
    SlideForm.notifier.status( SlideForm.status() );
});