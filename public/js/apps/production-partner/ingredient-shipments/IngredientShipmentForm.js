var IngredientShipmentForm = {
    viewModel : null,
    notifier: null
}

IngredientShipmentForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _ingredient_shipment_id,
            tracking_number: '',
            notes: '',
            source_name: null,
            load: function( onComplete )
            {
                var self = this;

                if( _ingredient_shipment_id )
                {
                    $.get( '/production-partner/getIngredientShipment', { id : _ingredient_shipment_id }, function( ingredient_shipment )
                    {
                        for( var key in ingredient_shipment )
                        {
                            self.set( key, ingredient_shipment[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        IngredientShipmentForm.addKendoElements();
                    });
                }
                else
                {
                    IngredientShipmentForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? 'Edit Ingredient Shipment' : 'Add Ingredient Shipment';
            }
        });

    return viewModel;
}

IngredientShipmentForm.loadViewModel = function()
{
    IngredientShipmentForm.viewModel = IngredientShipmentForm.getViewModel();
    kendo.bind( $( '#ingredientShipmentFormDiv' ), IngredientShipmentForm.viewModel );
    IngredientShipmentForm.viewModel.load();
}

IngredientShipmentForm.addListeners = function()
{
    $( "#cancelButton" ).click( IngredientShipmentForm.showIngredientShipmentList );

    $( "#saveButton" ).click( function()
    {
        IngredientShipmentForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        IngredientShipmentForm.validateForm( true );
    });
}

IngredientShipmentForm.showIngredientShipmentList = function()
{
    _ingredient_shipment_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientShipmentList" );
}

IngredientShipmentForm.validator = function()
{
    return $( "#ingredientShipmentForm" ).kendoValidator().data( "kendoValidator" );
}

IngredientShipmentForm.status = function()
{
    return $( "span.status" );
}

IngredientShipmentForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

IngredientShipmentForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

IngredientShipmentForm.validateForm = function( returnToList )
{
    if( IngredientShipmentForm.validator().validate() )
    {
        IngredientShipmentForm.save( returnToList );
    }
    else
    {
        IngredientShipmentForm.notifier.notifyError( 'Please complete all required fields.' );
        IngredientShipmentForm.enableSaveButtons();
    }
}

IngredientShipmentForm.save = function( returnToList, onComplete )
{
    IngredientShipmentForm.notifier.notifyProgress( 'Saving Ingredient Shipment...' );
    $.post( "/production-partner/saveIngredientShipment", $( "#ingredientShipmentForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.ingredient_shipment_id) > 0 )
        {
            if( _ingredient_shipment_id == 0 )
            {
                _ingredient_shipment_id = response.ingredient_shipment_id;
            }

            IngredientShipmentForm.notifier.notifyComplete( 'Ingredient Shipment Saved' );
            IngredientShipmentForm.viewModel.set( 'id', response.ingredient_shipment_id );

            if( returnToList )
            {
                IngredientShipmentForm.showIngredientShipmentList();
            }
            else
            {
                IngredientShipmentForm.viewModel.load( onComplete );
            }
        }
        else
        {
            IngredientShipmentForm.notifier.notifyError( 'Ingredient Shipment could not be saved' );
        }
    });
}

IngredientShipmentForm.addKendoElements = function() {
    if(!$("#shipped_time").data('kendoDateTimePicker'))
    {
        $( '#shipped_time' ).kendoDateTimePicker({
            format: "dd/MM/yyyy hh:mm tt",
            parseFormats: ['yyyy-MM-dd', 'HH:mm:ss']
        });
    }

    IngredientShipmentForm.loadIngredientLotGrid();
    IngredientShipmentForm.loadSourceList();
    IngredientShipmentForm.loadDestinationList();
}

IngredientShipmentForm.loadSourceList = function() {
    $("#source_id").kendoDropDownList({
        optionLabel: "Select A Source",
        template: $("#production_partner_template").html(),
        dataTextField: "name_en",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/production-partner/getMemberIngredientSuppliers"
                }
            }
        }
    });
}


IngredientShipmentForm.loadDestinationList = function() {
    $("#destination_id").kendoDropDownList({
        optionLabel: "Select A Destination",
        template: $("#production_partner_template").html(),
        dataTextField: "name_en",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/production-partner/getMemberContractManufacturers"
                }
            }
        }
    });
}

/*** start ingredient lot grid ***/

IngredientShipmentForm.loadIngredientLotGrid = function()
{
    $("#ingredientLotListContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "500px",
        height: "400px",
        title: "Select Ingredient Lot",
        resizable: true,
        modal: true,
        visible: false
    });

    if( !$( '#ingredientLotGrid' ).data( 'kendoGrid' ) )
    {
        $( '#ingredientLotGrid' ).kendoGrid(
            {
                toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
                dataSource: IngredientShipmentForm.getShipmentIngredientLotDataSource(),
                scrollable: true,
                height: 150,
                selectable: 'single',
                columns: [
                    { template: IngredientShipmentForm.hiddenIngredientLotIdsTemplate(), hidden:true },
                    { field: 'sequence_number', title: '#', width: '60px' },
                    { field: 'lot_code', title: 'Lot' },
                    { field: 'ingredient_name', title: 'Ingredient'}],
                change: function( e )
                {
                    IngredientShipmentForm.setIngredientLotSelected( this.select() );
                }
            });
        IngredientShipmentForm.addIngredientLotsGridListeners();
    }
}

IngredientShipmentForm.hiddenIngredientLotIdsTemplate = function()
{
    return '<input type="hidden" name="ingredient_lot_ids[]" value="#=ingredient_lot_id#" />';
}

IngredientShipmentForm.getShipmentIngredientLotDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 20,
            transport:
            {
                read:
                {
                    url: "/production-partner/getShipmentIngredientLots",
                    dataType: 'json',
                    data: function() {
                        return {ingredient_shipment_id : _ingredient_shipment_id}
                    }
                }
            },
            schema:
            {
                model: IngredientShipmentForm.getShipmentIngredientLotModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'asc' }
        });
}

IngredientShipmentForm.getShipmentIngredientLotModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

IngredientShipmentForm.setIngredientLotSelected = function( selectedRows )
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

IngredientShipmentForm.addIngredientLotsGridListeners = function ()
{
    $( '#deleteButton' ).click( function()
    {
        IngredientShipmentForm.deleteProducts();
    });

    $( '#addButton' ).click( function ()
    {
        IngredientShipmentForm.showIngredientLotList();
    });
}

IngredientShipmentForm.deleteIngredientLots = function()
{
    var selected = IngredientShipmentForm.ingredientLotsGrid().select();
    for( var i = 0; i < selected.length; i++ )
    {
        IngredientShipmentForm.ingredientLotsGrid().dataSource.remove(IngredientShipmentForm.ingredientLotsGrid().dataItem( selected[i]));
    }
    IngredientShipmentForm.updateQuantity(0);
}

IngredientShipmentForm.ingredientLotsGrid = function()
{
    return $( '#ingredientLotGrid' ).data( 'kendoGrid' );
}

IngredientShipmentForm.filterGrid = function()
{
    IngredientShipmentForm.ingredientLotsGrid().dataSource.filter({});
}

IngredientShipmentForm.showIngredientLotList = function ()
{
    $( "#ingredientLotListContainer" ).data("kendoWindow").center();
    $( "#ingredientLotListContainer" ).data("kendoWindow").open();
    $( "#ingredientLotListContainer" ).load( "/production-partner/getIngredientLotListForm");
}

/*** end product grid ***/

$( document ).ready( function()
{
    IngredientShipmentForm.loadViewModel();
    IngredientShipmentForm.addListeners();

    IngredientShipmentForm.notifier = Utils.notifier();
    IngredientShipmentForm.notifier.status( IngredientShipmentForm.status() );
});