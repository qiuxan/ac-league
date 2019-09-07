var FileList = {
};

FileList.initGrid = function()
{
    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: FileList.getDataSource(),
            height: $( window ).height() - 160,
            sortable: {
                mode: "single",
                allowUnsort: false
            },
            selectable: 'multiple',
            columns: [   
                { field: 'location', title: 'File Image', template: "<img src='#: location #' alt='Image not found' onerror=\"this.onerror=null;this.src='/images/file.png';\" height='50px' width='auto'/>" },
                { field: 'original_name', title: 'Original Name' },
                { field: 'location', title: 'Link' },
                {
                    command: {
                        text: " Copy the Link",
                        click: FileList.copyToClipBoard,
                        iconClass: "fa fa-file-o"
                    },
                    title: "Copy Link to Clipboard",
                    width: "160px"
                }              
            ],
            change: function( e )
            {
                FileList.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                FileList.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });

}

FileList.copyToClipBoard = function (e) {
    e.preventDefault();
    dataItem = this.dataItem($(e.currentTarget).closest("tr"));
    link = dataItem.location;
    // set the value of tmp input to be text to be copied
    $( '#tmp' ).prop('value', link);
    // copy the value of tmp input to clipboard
    $( '#copy_button' ).click();
}

FileList.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: false,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/member/getFiles",
                    dataType: 'json',
                    data:
                    {
                        filters: FileList.getFilters()
                    }
                }
            },
            schema:
            {
                model: FileList.getModel(),
                data: 'data',
                total: 'total'
            }
        });
}

FileList.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id',
            fields: {
                link: { type: "string" },
                original_name: { type: "string" },
                id: { type: "number" }
            }            
        });
}

FileList.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchFilter' ).val();
        }
    }

    return filters;
}

FileList.filterGrid = function()
{
    FileList.getGrid().dataSource.filter({});
}

FileList.filters = function()
{
    var filters = [];
    filters.push( { app: 'files', grid: 'grid', filterName: 'search', filterValue: FileList.getFilters().search() } );

    return filters;
}

FileList.addListeners = function()
{
    $( '#searchFilter' ).keyup( FileList.filterGrid );
    $( '#searchFilter' ).click( FileList.filterGrid );
    $( '#deleteButton' ).click( FileList.deleteFiles );
}

FileList.setSelected = function( selectedRows )
{
    if( selectedRows.length > 0 )
    {
        $( '#deleteButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deleteButton' ).addClass( 'k-state-disabled' );
    }
}

FileList.deleteFiles = function()
{
    var ids = [];
    var selected = FileList.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( FileList.getGrid().dataItem( selected[i] )['id'] );
    }

    // TODO ask user to confirm delete file permernately
    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteFiles", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            FileList.filterGrid();
            FileList.updateStorage();
        });
    }).show('Confirm Delete', "The selected file(s) will be deleted <strong>permanently</strong> ! Are you sure you want to delete them?");
}

FileList.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

FileList.addKendoElements = function() {
    if (!$("#file_upload").data('kendoUpload')) {
        $("#file_upload").kendoUpload({
            async: {
                saveUrl: "/member/upload_files",
                autoUpload: true
            },
            select: FileList.onSelect,
            localization: {
                select: 'Drag file here or click to browse...'
            },
            success: FileList.fileUploadSuccess,
            upload: FileList.onUpload,
            validation: {
                maxFileSize: 85428800
            }
        });
    }
}

FileList.onSelect = function( e )
{
    if (e.files.length > 1) {
        alert("Please select only one file");
        e.preventDefault();
    }
}

FileList.onUpload = function( e )
{
    var files = e.files;
    e.data = { '_token': $('[name="_token"]').val() };
    $.each(files, function (){});
}

FileList.fileUploadSuccess = function( e )
{
    $( '#mainContentDiv' ).load( "/member/getFileList" );
}

FileList.initClipboard = function() {
    var clipboard = new Clipboard('.btn', {
        text: function(trigger) {
            return $('#tmp').val();
        }
    });
}

FileList.updateStorage = function(){
    $.get("/member/getStorageUsage", function (response) {
        response = JSON.parse(response);
        s_used = response.s_used;
        s_total = response.s_total;
        $("#s_used").html(s_used);
        $("#s_total").html(s_total);
    });
}

$( document ).ready( function()
{   
    FileList.initClipboard();
    FileList.addKendoElements();
    FileList.initGrid();
    FileList.addListeners();
});