var ProductForm = {
    viewModel : null,
    notifier: null
}

ProductForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _product_id,
            gtin: '',
            member_id: '',
            name_en: '',
            name_cn: '',
            origin_en: '',
            origin_cn: '',
            volume_en: '',
            volume_cn: '',
            description_en: '',
            description_cn: '',
            ingredients_en: '',
            ingredients_cn: '',
            benefits_en: '',
            benefits_cn: '',
            safety_certificate: '',
            company_en: '',
            company_cn: '',
            name_tr: '',
            origin_tr: '',
            volume_tr: '',
            description_tr: '',
            ingredients_tr: '',
            benefits_tr: '',
            company_tr: '',                        
            company_website: '',
            company_logo: '',
            load: function( onComplete )
            {
                var self = this;

                if( _product_id )
                {
                    $.get( '/admin/getProduct', { id : _product_id }, function( product )
                    {
                        for( var key in product )
                        {
                            self.set( key, product[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        ProductForm.addKendoElements();
                    });
                }
                else
                {
                    ProductForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Product';
            }
        });

    return viewModel;
}

ProductForm.loadViewModel = function()
{
    ProductForm.viewModel = ProductForm.getViewModel();
    kendo.bind( $( '#productFormDiv' ), ProductForm.viewModel );
    ProductForm.viewModel.load();
}

ProductForm.addListeners = function()
{
    $( "#cancelButton" ).click( ProductForm.showProductList );

    $( "#saveButton" ).click( function()
    {
        ProductForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        ProductForm.validateForm( true );
    });
}

ProductForm.showProductList = function()
{
    _product_id = 0;
    $( '#mainContentDiv' ).load( "/admin/getProductList" );
}

ProductForm.validator = function()
{
    return $( "#productForm" ).kendoValidator().data( "kendoValidator" );
}

ProductForm.status = function()
{
    return $( "span.status" );
}

ProductForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

ProductForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

ProductForm.validateForm = function( returnToList )
{
    if( ProductForm.validator().validate() )
    {
        ProductForm.save( returnToList );
    }
    else
    {
        ProductForm.notifier.notifyError( 'Please complete all required fields.' );
        ProductForm.enableSaveButtons();
    }
}

ProductForm.save = function( returnToList, onComplete )
{
    ProductForm.notifier.notifyProgress( 'Saving Product...' );
    $.post( "/admin/saveProduct", $( "#productForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.product_id) > 0 )
        {
            if( _product_id == 0 )
            {
                _product_id = response.product_id;
            }

            ProductForm.notifier.notifyComplete( 'Product Saved' );
            ProductForm.viewModel.set( 'id', response.product_id );

            if( returnToList )
            {
                ProductForm.showProductList();
            }
            else
            {
                ProductForm.viewModel.load( onComplete );
            }
        }
        else
        {
            ProductForm.notifier.notifyError( 'Product could not be saved' );
        }
    });
}

ProductForm.addKendoElements = function() {
    $("#member_id").kendoDropDownList();

    if (!$("#image").data('kendoUpload')) {
        $("#image").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: ProductForm.imageUploadSuccess,
            upload: ProductForm.onUpload
        });
    }

    if (!$("#safety_certificate_file").data('kendoUpload')) {
        $("#safety_certificate_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: ProductForm.safetyCertUploadSuccess,
            upload: ProductForm.onUpload
        });
    }

    if (!$("#company_logo_file").data('kendoUpload')) {
        $("#company_logo_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: ProductForm.companyLogoUploadSuccess,
            upload: ProductForm.onUpload
        });
    }

    $( '#tabs' ).kendoTabStrip();

    if( !ProductForm.editor1 )
    {
        ProductForm.editor1 = $( 'textarea#description_en' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !ProductForm.editor2 )
    {
        ProductForm.editor2 = $( 'textarea#description_cn' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !ProductForm.editor3 )
    {
        ProductForm.editor3 = $( 'textarea#ingredients_en' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !ProductForm.editor4 )
    {
        ProductForm.editor4 = $( 'textarea#ingredients_cn' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !ProductForm.editor5 )
    {
        ProductForm.editor5 = $( 'textarea#benefits_en' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !ProductForm.editor6 )
    {
        ProductForm.editor6 = $( 'textarea#benefits_cn' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !ProductForm.editor7 )
        {
            ProductForm.editor7 = $( 'textarea#ingredients_tr' ).ckeditor({
                customConfig: '/js/ckeditor/config.js'
            }).editor;
        }
    
    if( !ProductForm.editor8 )
    {
        ProductForm.editor8 = $( 'textarea#benefits_tr' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !ProductForm.editor9 )
    {
        ProductForm.editor9 = $( 'textarea#description_tr' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }
        
    ProductForm.setImages();
}

/*** start product images ***/

ProductForm.setImages = function()
{
    if( ProductForm.imageList() )
    {
        ProductForm.imageList().destroy();
        $( "#productImages" ).html();
    }
    ProductForm.imageDataSource = ProductForm.imagesDataSource();
    $( "#productImages" ).kendoListView({
        dataSource: ProductForm.imageDataSource,
        template: kendo.template( $("#imageTemplate").html() ),
        pageable: true
    });
}

$("#productImages").kendoSortable({
    filter: ".productImage",
    ignore: "textarea",
    cursor: "move",
    placeholder: function(element) {
        return element.clone().css("opacity", 0.1);
    },
    hint: function(element) {
        return element.clone().removeClass("k-state-selected");
    },
    change: function(e) {
        var skip = ProductForm.imageDataSource.skip(),
            oldIndex = e.oldIndex + skip,
            newIndex = e.newIndex + skip,
            data = ProductForm.imageDataSource.data(),
            dataItem = ProductForm.imageDataSource.getByUid(e.item.data("uid"));

        ProductForm.imageDataSource.remove(dataItem);
        ProductForm.imageDataSource.insert(newIndex, dataItem);
        var file_id_list = "";
        for (var i = 0; i < ProductForm.imageDataSource.data().length; i++) {
            var item = ProductForm.imageDataSource.data()[i];
            if(file_id_list == "")
            {
                file_id_list = file_id_list + item.file_id;
            }
            else
            {
                file_id_list = file_id_list + "-" + item.file_id;
            }

        }
        ProductForm.setProductImagePriority(file_id_list);
    }
})

ProductForm.imagesDataSource = function()
{
    var Image = kendo.data.Model.define(
        {
            id: 'file_id'
        });

    var dataSource = new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 50,
            transport:
            {
                read:
                {
                    url: "/admin/getProductImages",
                    dataType: 'json',
                    data: function() {
                        return { product_id : _product_id }
                    }
                }
            },
            schema:
            {
                model: Image,
                data: 'data',
                total: 'total'
            },
            sort: { field: 'priority', dir: 'asc' }
        });

    return dataSource;
}

ProductForm.onUpload = function( e )
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

ProductForm.imageUploadSuccess = function( e )
{
    $.post( '/admin/addProductImage', { product_id: _product_id, file_id: e.response.result.id, _token: $('[name="_token"]').val() }, function()
    {
        ProductForm.setImages();
    });
}

ProductForm.safetyCertUploadSuccess = function( e )
{
    $( "#safety_certificate" ).val(e.response.result.location);
}

ProductForm.companyLogoUploadSuccess = function( e )
{
    $( "#company_logo" ).val(e.response.result.location);
}

ProductForm.reloadImages = function()
{
    ProductForm.imageList().dataSource.filter({});
}

ProductForm.imageList = function()
{
    $( "#productImages" ).data( 'kendoListView' );
}

ProductForm.deleteProductImage = function( file_id )
{
    Utils.confirm().yesCallBack( function(){
        $.post( '/admin/deleteProductImage', { product_id: _product_id, file_id: file_id, _token: $('[name="_token"]').val() }, function()
        {
            ProductForm.setImages();
        })
    }).show( 'Confirm Delete', 'Are you sure you want to delete this image?' );
}

ProductForm.setProductImageDescription = function( file_id, lang )
{
    // English
    if (lang == 1)
    {
        var description = $("#imageDescription_en_" + file_id).val();
    }
    // Chinese
    else if (lang == 0) 
    {
        var description = $("#imageDescription_cn_" + file_id).val();
    } else 
    // Traditional Chinese
    {
        var description = $("#imageDescription_tr_" + file_id).val();        
    }

    $.post( '/admin/setProductImageDescription', { product_id: _product_id, file_id: file_id, description: description, lang: lang,  _token: $('[name="_token"]').val() });
}

ProductForm.setProductImagePriority = function( file_id_list )
{
    $.post( '/admin/setProductImagePriority', { product_id: _product_id, file_id_list: file_id_list, _token: $('[name="_token"]').val() });
}

ProductForm.setProductThumbnail = function( file_id )
{
    $.post( '/admin/setProductThumbnail', { product_id: _product_id, file_id: file_id, _token: $('[name="_token"]').val() }, function () {
        ProductForm.setImages();
    });
}

/*** end product images ***/

$( document ).ready( function()
{
    ProductForm.loadViewModel();
    ProductForm.addListeners();

    ProductForm.notifier = Utils.notifier();
    ProductForm.notifier.status( ProductForm.status() );
});