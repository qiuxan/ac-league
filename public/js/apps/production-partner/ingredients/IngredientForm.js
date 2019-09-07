var IngredientForm = {
    viewModel : null,
    notifier: null
}

IngredientForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _ingredient_id,
            member_id: 0,
            production_partner_id: 0,
            gtin: '',
            name: '',
            origin: '',
            description: '',
            certificate_url: '',
            load: function( onComplete )
            {
                var self = this;

                if( _ingredient_id )
                {
                    $.get( '/production-partner/getIngredient', { id : _ingredient_id }, function( ingredient )
                    {
                        for( var key in ingredient )
                        {
                            self.set( key, ingredient[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        IngredientForm.addKendoElements();
                    });
                }
                else
                {
                    IngredientForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name' ) : 'Add Ingredient';
            }
        });

    return viewModel;
}

IngredientForm.loadViewModel = function()
{
    IngredientForm.viewModel = IngredientForm.getViewModel();
    kendo.bind( $( '#ingredientFormDiv' ), IngredientForm.viewModel );
    IngredientForm.viewModel.load();
}

IngredientForm.addListeners = function()
{
    $( "#cancelButton" ).click( IngredientForm.showIngredientList );

    $( "#saveButton" ).click( function()
    {
        IngredientForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        IngredientForm.validateForm( true );
    });
}

IngredientForm.showIngredientList = function()
{
    _ingredient_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientList" );
}

IngredientForm.validator = function()
{
    return $( "#ingredientForm" ).kendoValidator().data( "kendoValidator" );
}

IngredientForm.status = function()
{
    return $( "span.status" );
}

IngredientForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

IngredientForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

IngredientForm.validateForm = function( returnToList )
{
    if( IngredientForm.validator().validate() )
    {
        IngredientForm.save( returnToList );
    }
    else
    {
        IngredientForm.notifier.notifyError( 'Please complete all required fields.' );
        IngredientForm.enableSaveButtons();
    }
}

IngredientForm.save = function( returnToList, onComplete )
{
    IngredientForm.notifier.notifyProgress( 'Saving Ingredient...' );
    $.post( "/production-partner/saveIngredient", $( "#ingredientForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.ingredient_id) > 0 )
        {
            if( _ingredient_id == 0 )
            {
                _ingredient_id = response.ingredient_id;
            }

            IngredientForm.notifier.notifyComplete( 'Ingredient Saved' );
            IngredientForm.viewModel.set( 'id', response.ingredient_id );

            if( returnToList )
            {
                IngredientForm.showIngredientList();
            }
            else
            {
                IngredientForm.viewModel.load( onComplete );
            }
        }
        else
        {
            IngredientForm.notifier.notifyError( 'Ingredient could not be saved' );
        }
    });
}

IngredientForm.addKendoElements = function() {
}

$( document ).ready( function()
{
    IngredientForm.loadViewModel();
    IngredientForm.addListeners();

    IngredientForm.notifier = Utils.notifier();
    IngredientForm.notifier.status( IngredientForm.status() );
});