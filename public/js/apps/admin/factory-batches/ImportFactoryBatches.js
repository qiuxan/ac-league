var ImportFactoryBatches;
ImportFactoryBatches = {
    _onClose: null
};

ImportFactoryBatches.showWindow = function()
{
    if( !$( '#importWindow' ).data( 'kendowWindow' ) )
    {
        $( document.createElement( 'div' ) )
            .prop( 'id', 'importWindow' )
            .appendTo( 'body' );

        $( '#importWindow' ).kendoWindow({
            title: 'Import Factory Batches',
            width: '700px',
            height: '500px',
            close: ImportFactoryBatches.onClose()
        });
    }

    ImportFactoryBatches.setContent();

    $( '#importWindow' ).data( 'kendoWindow' ).center();
    $( '#importWindow' ).data( 'kendoWindow' ).open();
}

ImportFactoryBatches.setContent = function()
{
    $( '#importWindow' )
        .html( ImportFactoryBatches.upload() )
        .append(
            $( document.createElement( 'div' ) )
                .prop( 'id', 'statusDiv' )
        );
    ImportFactoryBatches.setKendoUpload();
}

ImportFactoryBatches.upload = function()
{
    return $( document.createElement( 'input' ) )
        .prop( 'type', 'file' )
        .prop( 'id', 'import' )
        .prop( 'name', 'import' );
}

ImportFactoryBatches.setKendoUpload = function()
{
    $( '#import' ).kendoUpload({
        async:
        {
            saveUrl: "/admin/importFactoryCodes",
            autoUpload: true
        },
        localization:
        {
            select: 'Drag import CSV file here or click to browse...'
        },
        success: ImportFactoryBatches.uploadSuccess,
        upload: ImportFactoryBatches.onUpload
    });
}

ImportFactoryBatches.onUpload = function( e )
{
    var files = e.files;
    e.data = { '_token': $('[name="_token"]').val() };
    $.each(files, function ()
    {
        if( this.extension.toLowerCase() != ".csv" )
        {
            alert( "Only .csv files can be uploaded" );
            e.preventDefault();
        }
    });
}

ImportFactoryBatches.uploadSuccess = function( e )
{
    if(!$("#statusTable").length)
    {
        $("#statusDiv")
            .append
            (
                $( document.createElement( 'table' ) )
                    .prop( 'id', 'statusTable' )
                    .prop( 'width', '100%' )
                    .append
                    (
                        $( document.createElement( 'tr' ) )
                            .append
                            (
                                $( document.createElement( 'th' ) )
                                    .html( 'File Name' )
                            )
                            .append
                            (
                                $( document.createElement( 'th' ) )
                                    .html( 'Status' )
                            )
                            .append
                            (
                                $( document.createElement( 'th' ) )
                                    .html( 'Quantity' )
                            )
                    )
            )
    }
    $("#statusTable").append
    (
        $( document.createElement( 'tr' ) )
            .append
            (
                $( document.createElement( 'td' ) )
                    .html(e.response.fileName )
            )
            .append
            (
                $( document.createElement( 'td' ) )
                    .html(  e.response.result )
            )
            .append
            (
                $( document.createElement( 'td' ) )
                    .html(  e.response.count + " codes" )
            )
    );
}

ImportFactoryBatches.onClose = function( onClose )
{
    if( onClose != undefined )
    {
        ImportFactoryBatches._onClose = onClose;
        return ImportFactoryBatches;
    }

    return ImportFactoryBatches._onClose;
}