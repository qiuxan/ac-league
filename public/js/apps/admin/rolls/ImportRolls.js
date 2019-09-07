var ImportRolls;
ImportRolls = {
    _importUrl: null,
    _onClose: null
};

ImportRolls.showWindow = function()
{
    if( !$( '#importWindow' ).data( 'kendowWindow' ) )
    {
        $( document.createElement( 'div' ) )
            .prop( 'id', 'importWindow' )
            .appendTo( 'body' );

        $( '#importWindow' ).kendoWindow({
            title: 'Import Rolls',
            width: '700px',
            height: '500px',
            close: ImportRolls.onClose()
        });
    }

    ImportRolls.setContent();

    $( '#importWindow' ).data( 'kendoWindow' ).center();
    $( '#importWindow' ).data( 'kendoWindow' ).open();
}

ImportRolls.setContent = function()
{
    $( '#importWindow' )
        .html( ImportRolls.upload() )
        .append(
            $( document.createElement( 'div' ) )
                .prop( 'id', 'statusDiv' )
        );
    ImportRolls.setKendoUpload();
}

ImportRolls.upload = function()
{
    return $( document.createElement( 'input' ) )
        .prop( 'type', 'file' )
        .prop( 'id', 'import' )
        .prop( 'name', 'import' );
}

ImportRolls.setKendoUpload = function()
{
    $( '#import' ).kendoUpload({
        async:
        {
            saveUrl: ImportRolls.importUrl(),
            autoUpload: true
        },
        localization:
        {
            select: 'Drag import CSV file here or click to browse...'
        },
        success: ImportRolls.uploadSuccess,
        upload: ImportRolls.onUpload
    });
}

ImportRolls.onUpload = function( e )
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

ImportRolls.uploadSuccess = function( e )
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

ImportRolls.onClose = function( onClose )
{
    if( onClose != undefined )
    {
        ImportRolls._onClose = onClose;
        return ImportRolls;
    }

    return ImportRolls._onClose;
}

ImportRolls.importUrl = function( importUrl )
{
    if( importUrl != undefined )
    {
        ImportRolls._importUrl = importUrl;
        return ImportRolls;
    }

    return ImportRolls._importUrl;
}