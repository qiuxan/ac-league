var FileForm = {
    viewModel : null,
    notifier: null    
}

FileForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _file_id,
            original_name: '',
            location: '',
            load: function( onComplete )
            {
                var self = this;

                if( _file_id )
                {
                    $.get( '/staff/getFile', { id : _file_id }, function( file )
                    {
                        for( var key in file )
                        {
                            if (key=='original_name') {
                                file_extension = file['original_name'].split('.').pop();
                                if (file_extension!='jpg' && file_extension!='png' && file_extension!='gif'){
                                    self.set( 'location', '/images/file.png');
                                    delete file['location'];
                                }
                                self.set('location','/images/file.png');
                            }
                            self.set( key, file[key] );
                        }
                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                    });

                }
            }
        });

    return viewModel;
}

FileForm.loadViewModel = function()
{
    FileForm.viewModel = FileForm.getViewModel();
    kendo.bind( $( '#fileFormDiv' ), FileForm.viewModel );
    FileForm.viewModel.load();
    console.log(FileForm.viewModel);
}

FileForm.addListeners = function()
{
    $( "#cancelButton" ).click( FileForm.showFileList );

    $( "#saveButton" ).click( function()
    {
        FileForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        FileForm.validateForm( true );
    });
}

FileForm.showFileList = function()
{
    _file_id = 0;
    $( '#mainContentDiv' ).load( "/staff/getFileList" );
}

FileForm.validator = function()
{
    return $( "#fileForm" ).kendoValidator().data( "kendoValidator" );
}

FileForm.status = function()
{
    return $( "span.status" );
}

FileForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

FileForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

FileForm.validateForm = function( returnToList )
{
    if( FileForm.validator().validate() )
    {
        FileForm.save( returnToList );
    }
    else
    {
        FileForm.notifier.notifyError( 'Please complete all required fields.' );
        FileForm.enableSaveButtons();
    }
}

FileForm.save = function( returnToList, onComplete )
{
    FileForm.notifier.notifyProgress( 'Saving File...' );
    $.post( "/staff/saveFile", $( "#fileForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.file_id) > 0 )
        {
            if( _file_id == 0 )
            {
                _file_id = response.file_id;
            }

            FileForm.notifier.notifyComplete( 'File Saved' );
            FileForm.viewModel.set( 'id', response.file_id );

            if( returnToList )
            {
                FileForm.showFileList();
            }
            else
            {
                FileForm.viewModel.load( onComplete );
            }
        }
        else
        {
            FileForm.notifier.notifyError( 'File could not be saved' );
        }
    }); 
}

$( document ).ready( function()
{
    FileForm.loadViewModel();
    FileForm.addListeners();

    FileForm.notifier = Utils.notifier();
    FileForm.notifier.status( FileForm.status() );
});