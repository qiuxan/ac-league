var IngredientLotForm = {
    viewModel : null,
    notifier: null
}

IngredientLotForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _ingredient_lot_id,
            production_partner_id: '',
            ingredient_id: '',
            lot_code: '',
            production_date: 0,
            expiration_date: '',
            certificate_url: '',
            created_time: '',
            shipped_time: '',
            received_time: '',
            load: function( onComplete )
            {
                var self = this;

                if( _ingredient_lot_id )
                {
                    $.get( '/production-partner/getIngredientLot', { id : _ingredient_lot_id }, function( ingredient_lot )
                    {
                        for( var key in ingredient_lot )
                        {
                            self.set( key, ingredient_lot[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        IngredientLotForm.addKendoElements();
                    });
                }
                else
                {
                    IngredientLotForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            hasShippedTime: function()
            {
                return this.get( 'id' ) != 0 && this.get( 'shipped_time' ) != ''  && this.get( 'shipped_time' ) != null;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Ingredient Lot';
            }
        });

    return viewModel;
}

IngredientLotForm.loadViewModel = function()
{
    IngredientLotForm.viewModel = IngredientLotForm.getViewModel();
    kendo.bind( $( '#ingredientLotFormDiv' ), IngredientLotForm.viewModel );
    IngredientLotForm.viewModel.load();
}

IngredientLotForm.addListeners = function()
{
    $( "#cancelButton" ).click( IngredientLotForm.showIngredientLotList );

    $( "#saveButton" ).click( function()
    {
        IngredientLotForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        IngredientLotForm.validateForm( true );
    });
}

IngredientLotForm.showIngredientLotList = function()
{
    _ingredient_lot_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientLotList" );
}

IngredientLotForm.validator = function()
{
    return $( "#ingredientLotForm" ).kendoValidator().data( "kendoValidator" );
}

IngredientLotForm.status = function()
{
    return $( "span.status" );
}

IngredientLotForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

IngredientLotForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

IngredientLotForm.validateForm = function( returnToList )
{
    if( IngredientLotForm.validator().validate() )
    {
        IngredientLotForm.save( returnToList );
    }
    else
    {
        IngredientLotForm.notifier.notifyError( 'Please complete all required fields.' );
        IngredientLotForm.enableSaveButtons();
    }
}

IngredientLotForm.save = function( returnToList, onComplete )
{
    IngredientLotForm.notifier.notifyProgress( 'Saving Ingredient Lot...' );
    $.post( "/production-partner/saveIngredientLot", $( "#ingredientLotForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.ingredient_lot_id) > 0 )
        {
            if( _ingredient_lot_id == 0 )
            {
                _ingredient_lot_id = response.ingredient_lot_id;
            }

            IngredientLotForm.notifier.notifyComplete( 'Ingredient Lot Saved' );
            IngredientLotForm.viewModel.set( 'id', response.ingredient_lot_id );

            if( returnToList )
            {
                IngredientLotForm.showIngredientLotList();
            }
            else
            {
                IngredientLotForm.viewModel.load( onComplete );
            }
        }
        else
        {
            IngredientLotForm.notifier.notifyError( 'Ingredient Lot could not be saved' );
        }
    });
}

IngredientLotForm.addKendoElements = function() {
    IngredientLotForm.loadIngredientList();

    if(!$("#production_date").data('kendoDatePicker'))
    {
        $( "#production_date" ).kendoDatePicker({
            format: "dd/MM/yyyy",
            parseFormats: ["yyyy-MM-dd"]
        });
    }

    if(!$("#expiration_date").data('kendoDatePicker'))
    {
        $( "#expiration_date" ).kendoDatePicker({
            format: "dd/MM/yyyy",
            parseFormats: ["yyyy-MM-dd"]
        });
    }

    if(!$("#created_time").data('kendoDatePicker'))
    {
        $( '#created_time' ).kendoDateTimePicker({
            format: "dd/MM/yyyy hh:mm tt",
            parseFormats: ['yyyy-MM-dd', 'HH:mm:ss']
        });
    }

    if(!$("#shipped_time").data('kendoDatePicker'))
    {
        $( '#shipped_time' ).kendoDateTimePicker({
            format: "dd/MM/yyyy hh:mm tt",
            parseFormats: ['yyyy-MM-dd', 'HH:mm:ss']
        });
    }

    if(!$("#received_time").data('kendoDatePicker'))
    {
        $( '#received_time' ).kendoDateTimePicker({
            format: "dd/MM/yyyy hh:mm tt",
            parseFormats: ['yyyy-MM-dd', 'HH:mm:ss']
        });

    }

    if (!$("#certificate_file").data('kendoUpload')) {
        $("#certificate_file").kendoUpload({
            async: {
                saveUrl: "/files",
                autoUpload: true
            },
            select: IngredientLotForm.onSelect,
            localization: {
                select: 'Drag image here or click to browse...'
            },
            success: IngredientLotForm.avatarUploadSuccess,
            upload: IngredientLotForm.onUpload
        });
    }
}

IngredientLotForm.loadIngredientList = function () {
    $("#ingredient_id").kendoDropDownList({
        optionLabel: "Select An Ingredient",
        template: $("#ingredient_template").html(),
        dataTextField: "name",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/production-partner/getIngredientsForDropdown"
                }
            }
        }
    });
}

IngredientLotForm.onSelect = function( e )
{
    if (e.files.length > 1) {
        alert("Please select only one file");
        e.preventDefault();
    }
}

IngredientLotForm.onUpload = function( e )
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

IngredientLotForm.avatarUploadSuccess = function( e )
{
    $( "#avatar" ).val(e.response.result.location);
}

$( document ).ready( function()
{
    IngredientLotForm.loadViewModel();
    IngredientLotForm.addListeners();

    IngredientLotForm.notifier = Utils.notifier();
    IngredientLotForm.notifier.status( IngredientLotForm.status() );
});