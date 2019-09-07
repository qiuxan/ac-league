var ProductAttributeForm = {
    viewModel : null,
    notifier: null,
    TYPE_TEXTBOX: 1,
    TYPE_TEXTAREA: 2,
    TEXT_IMAGE: 3
}

ProductAttributeForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _product_attribute_id,
            product_id: '',
            language: '',
            type: 1,
            displayed_at: 1,
            name: '',
            value: '',
            value_textBox: '',
            value_textArea: '',
            value_image: '',
            value_document: '',
            priority: 0,
            load: function( onComplete )
            {
                var self = this;

                if( _product_attribute_id )
                {
                    $.get( '/member/getProductAttribute', { id : _product_attribute_id }, function( product_attribute )
                    {
                        for( var key in product_attribute )
                        {
                            self.set( key, product_attribute[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        ProductAttributeForm.addKendoElements();
                    });
                }
                else
                {
                    self.set( 'product_id', _product_id );
                    ProductAttributeForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            isTextBox: function()
            {
                return this.get( 'type' ) == ProductAttributeForm.TYPE_TEXTBOX;
            },
            isTextArea: function()
            {
                return this.get( 'type' ) == ProductAttributeForm.TYPE_TEXTAREA;
            },
            isImage: function()
            {
                return this.get( 'type' ) == ProductAttributeForm.TEXT_IMAGE;
            }
        });

    return viewModel;
}

ProductAttributeForm.loadViewModel = function()
{
    ProductAttributeForm.viewModel = ProductAttributeForm.getViewModel();
    kendo.bind( $( '#productAttributeFormDiv' ), ProductAttributeForm.viewModel );
    ProductAttributeForm.viewModel.load();
}

ProductAttributeForm.addListeners = function()
{
    $( "#cancelButtonProductAttribute" ).click( ProductAttributeList.refreshProductAttributeList );

    $( "#saveButtonProductAttribute" ).click( function()
    {
        ProductAttributeForm.validateForm( false );
    });

    $( "#doneButtonProductAttribute" ).click( function()
    {
        ProductAttributeForm.validateForm( true );
    });
}

ProductAttributeForm.status = function()
{
    return $( "span.status" );
}

ProductAttributeForm.disableSaveButtons = function()
{
    $( "#saveButtonProductAttribute" ).prop( 'disabled', true );
    $( "#doneButtonProductAttribute" ).prop( 'disabled', true );
}

ProductAttributeForm.enableSaveButtons = function()
{
    $( "#saveButtonProductAttribute" ).prop( 'disabled', false );
    $( "#doneButtonProductAttribute" ).prop( 'disabled', false );
}

ProductAttributeForm.validator = function()
{
    return $( "#productAttributeForm" ).kendoValidator().data( "kendoValidator" );
}

ProductAttributeForm.validateForm = function( returnToList )
{
    if( ProductAttributeForm.validator().validate() )
    {
        ProductAttributeForm.save( returnToList );
    }
    else
    {
        ProductAttributeForm.notifier.notifyError( 'Please complete all required fields.' );
        ProductAttributeForm.enableSaveButtons();
    }
}

ProductAttributeForm.save = function( returnToList, onComplete )
{
    ProductAttributeForm.notifier.notifyProgress( 'Saving Product Attribute...' );
    $.post( "/member/saveProductAttribute", $( "#productAttributeForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.product_attribute_id) > 0 )
        {
            if( _product_attribute_id == 0 )
            {
                _product_attribute_id = response.product_attribute_id;
            }

            ProductAttributeForm.notifier.notifyComplete( 'Product Attribute Saved' );
            ProductAttributeForm.viewModel.set( 'id', response.product_attribute_id );

            if( returnToList )
            {
                ProductAttributeList.refreshProductAttributeList();
            }
            else
            {
                ProductAttributeForm.viewModel.load( onComplete );
            }
        }
        else if(parseInt(response.product_attribute_id) == -1)
        {
            ProductAttributeForm.notifier.notifyError( 'Please input value of attribute' );
        }
        else
        {
            ProductAttributeForm.notifier.notifyError( 'Product Attribute could not be saved' );
        }
    });
}

ProductAttributeForm.addKendoElements = function() {
    $("#language").kendoDropDownList();

    $("#type").kendoDropDownList();

    $("#displayed_at").kendoDropDownList();

    if( !ProductAttributeForm.editor )
    {
        ProductAttributeForm.editor = $( 'textarea#value_textArea' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if (!$("#image_file").data('kendoUpload')) {
        $("#image_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: ProductAttributeForm.imageUploadSuccess,
            upload: ProductAttributeForm.onImageUpload
        });
    }
}

ProductAttributeForm.onImageUpload = function( e )
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

ProductAttributeForm.imageUploadSuccess = function( e )
{
    ProductAttributeForm.viewModel.set( "value_image", e.response.result.location);
}

$( document ).ready( function()
{
    ProductAttributeForm.loadViewModel();
    ProductAttributeForm.addListeners();

    ProductAttributeForm.notifier = Utils.notifier();
    ProductAttributeForm.notifier.status( ProductAttributeForm.status() );
});