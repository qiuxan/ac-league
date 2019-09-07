var MenuForm = {
    viewModel     : null,
    menuNotifier: null
}

MenuForm.loadMenuViewModel = function()
{
    MenuForm.menuViewModel = MenuForm.getMenuViewModel();
    kendo.bind( $( '#menuForm' ), MenuForm.menuViewModel );
    MenuForm.menuViewModel.load();
}

MenuForm.validateMenuForm = function( closeForm )
{
    MenuForm.disableMenuSaveButtons();

    var validator = $( "#menuForm" ).kendoValidator().data( "kendoValidator" );

    MenuForm.menuNotifier.notifyProgress( 'Saving menu...' );

    if( validator.validate() )
    {
        $.post( "/staff/saveMenu", $( "#menuForm" ).serialize(), function(response)
        {
            response = JSON.parse(response);
            if( parseInt( response.menu_id ) > 0 )
            {
                MenuForm.menuNotifier.notifyComplete( 'Menu saved' );
                MenuForm.menuViewModel.set( 'id', response.menu_id );
                _menu_id = response.menu_id;
                MenuList.updateTree( response.menu_id );
                if( closeForm )
                {
                    MenuList.newMenu();
                }
                else
                {
                    MenuList.showMenu();
                }
            }
            else
            {
                MenuForm.menuNotifier.notifyError( 'Menu could not be saved' );
            }
            MenuForm.enableMenuSaveButtons();
        } );
    }
    else
    {
        MenuForm.menuNotifier.notifyError( 'Please complete all required fields.' );
        MenuForm.enableMenuSaveButtons();
    }
}

MenuForm.getMenuViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: 0,
            parent_id: 0,
            language: '',
            name: '',
            alias: '',
            external_link: '',
            priority: 0,
            load: function() {
                var self = this;

                if (typeof _menu_id !== 'undefined') {
                    $.get('/staff/getMenu', {id: _menu_id}, function (menu) {
                        for (var key in menu) {
                            self.set(key, menu[key]);
                        }
                    });
                }
            }
        });

    return viewModel;
}

MenuForm.disableMenuSaveButtons = function()
{
    $( "#doneButton" ).prop( 'disabled', true );
}

MenuForm.enableMenuSaveButtons = function()
{
    $( "#doneButton" ).prop( 'disabled', false );
}

MenuForm.addListeners = function()
{
    $( '#name' ).change( MenuForm.nameToAlias );
    $( '#alias' ).change( MenuForm.aliasCheck );

    $( "#doneButton" ).click( function()
    {
        MenuForm.validateMenuForm( true );
    });
    $( "#saveButton" ).click( function()
    {
        MenuForm.validateMenuForm( false );
    });
    $( '#cancelButton' ).click( MenuForm.cancelMenu );

}

MenuForm.nameToAlias = function()
{
    if( $( '#alias' ).val() == '' ) {
        $( '#alias' ).val( $( '#name' ).val().replace( /\W+/g, " ").replace( / /g, "-").toLowerCase() );
    }
}

MenuForm.aliasCheck = function()
{
    if( $( '#alias' ).val() != '' ) {
        $( '#alias' ).val( $( '#alias' ).val().replace( /\W+/g, " ").replace( / /g, "-").toLowerCase() );
    }
}

MenuForm.cancelMenu = function()
{
    MenuList.newMenu();
}

MenuForm.menuStatus = function()
{
    return $( ".menuStatus" );
}

MenuForm.addKendoElements = function() {
    $("#language").kendoDropDownList();
}

$( document ).ready( function()
{
    MenuForm.addListeners();
    MenuForm.loadMenuViewModel();
    MenuForm.addKendoElements();

    MenuForm.menuNotifier = Utils.notifier();
    MenuForm.menuNotifier.status( MenuForm.menuStatus() );
});