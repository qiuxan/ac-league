var IngredientReceiptForm = {
    viewModel : null,
    notifier: null
}

IngredientReceiptForm.getViewModel = function()
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
                    $.get( '/production-partner/getIngredientReceipt', { id : _ingredient_shipment_id }, function( ingredient_shipment )
                    {
                        for( var key in ingredient_shipment )
                        {
                            self.set( key, ingredient_shipment[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        IngredientReceiptForm.addKendoElements();
                    });
                }
                else
                {
                    IngredientReceiptForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? 'Edit Ingredient Receipt' : 'Add Ingredient Receipt';
            }
        });

    return viewModel;
}

IngredientReceiptForm.loadViewModel = function()
{
    IngredientReceiptForm.viewModel = IngredientReceiptForm.getViewModel();
    kendo.bind( $( '#ingredientReceiptFormDiv' ), IngredientReceiptForm.viewModel );
    IngredientReceiptForm.viewModel.load();
}

IngredientReceiptForm.addListeners = function()
{
    $( "#cancelButton" ).click( IngredientReceiptForm.showIngredientShipmentList );

    $( "#saveButton" ).click( function()
    {
        IngredientReceiptForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        IngredientReceiptForm.validateForm( true );
    });
}

IngredientReceiptForm.showIngredientShipmentList = function()
{
    _ingredient_shipment_id = 0;
    $( '#mainContentDiv' ).load( "/production-partner/getIngredientReceiptList" );
}

IngredientReceiptForm.validator = function()
{
    return $( "#ingredientReceiptForm" ).kendoValidator().data( "kendoValidator" );
}

IngredientReceiptForm.status = function()
{
    return $( "span.status" );
}

IngredientReceiptForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

IngredientReceiptForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

IngredientReceiptForm.validateForm = function( returnToList )
{
    if( IngredientReceiptForm.validator().validate() )
    {
        IngredientReceiptForm.save( returnToList );
    }
    else
    {
        IngredientReceiptForm.notifier.notifyError( 'Please complete all required fields.' );
        IngredientReceiptForm.enableSaveButtons();
    }
}

IngredientReceiptForm.save = function( returnToList, onComplete )
{
    IngredientReceiptForm.notifier.notifyProgress( 'Saving Ingredient Receipt...' );
    $.post( "/production-partner/saveIngredientReceipt", $( "#ingredientReceiptForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.ingredient_shipment_id) > 0 )
        {
            if( _ingredient_shipment_id == 0 )
            {
                _ingredient_shipment_id = response.ingredient_shipment_id;
            }

            IngredientReceiptForm.notifier.notifyComplete( 'Ingredient Receipt Saved' );
            IngredientReceiptForm.viewModel.set( 'id', response.ingredient_shipment_id );

            if( returnToList )
            {
                IngredientReceiptForm.showIngredientShipmentList();
            }
            else
            {
                IngredientReceiptForm.viewModel.load( onComplete );
            }
        }
        else
        {
            IngredientReceiptForm.notifier.notifyError( 'Ingredient Receipt could not be saved' );
        }
    });
}

IngredientReceiptForm.addKendoElements = function() {
    if(!$("#received_time").data('kendoDateTimePicker'))
    {
        $( '#received_time' ).kendoDateTimePicker({
            format: "dd/MM/yyyy hh:mm tt",
            parseFormats: ['yyyy-MM-dd', 'HH:mm:ss']
        });
    }

    IngredientReceiptForm.loadIngredientLotGrid();
    IngredientReceiptForm.loadSourceList();
}

IngredientReceiptForm.loadSourceList = function() {
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


/*** start ingredient lot grid ***/

IngredientReceiptForm.loadIngredientLotGrid = function()
{
    if( !$( '#ingredientLotGrid' ).data( 'kendoGrid' ) )
    {
        $( '#ingredientLotGrid' ).kendoGrid(
            {
                dataSource: IngredientReceiptForm.getShipmentIngredientLotDataSource(),
                scrollable: true,
                height: 150,
                selectable: 'single',
                columns: [
                    { template: IngredientReceiptForm.hiddenIngredientLotIdsTemplate(), hidden:true },
                    { field: 'sequence_number', title: '#', width: '60px' },
                    { field: 'lot_code', title: 'Lot' },
                    { field: 'ingredient_name', title: 'Ingredient'}],
                change: function( e )
                {
                    IngredientReceiptForm.setIngredientLotSelected( this.select() );
                }
            });
    }
}

IngredientReceiptForm.hiddenIngredientLotIdsTemplate = function()
{
    return '<input type="hidden" name="ingredient_lot_ids[]" value="#=ingredient_lot_id#" />';
}

IngredientReceiptForm.getShipmentIngredientLotDataSource = function()
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
                    url: "/production-partner/getReceiptIngredientLots",
                    dataType: 'json',
                    data: function() {
                        return {ingredient_shipment_id : _ingredient_shipment_id}
                    }
                }
            },
            schema:
            {
                model: IngredientReceiptForm.getShipmentIngredientLotModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'asc' }
        });
}

IngredientReceiptForm.getShipmentIngredientLotModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

IngredientReceiptForm.setIngredientLotSelected = function( selectedRows )
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

IngredientReceiptForm.ingredientLotsGrid = function()
{
    return $( '#ingredientLotGrid' ).data( 'kendoGrid' );
}

IngredientReceiptForm.filterGrid = function()
{
    IngredientReceiptForm.ingredientLotsGrid().dataSource.filter({});
}

/*** end product grid ***/

$( document ).ready( function()
{
    IngredientReceiptForm.loadViewModel();
    IngredientReceiptForm.addListeners();

    IngredientReceiptForm.notifier = Utils.notifier();
    IngredientReceiptForm.notifier.status( IngredientReceiptForm.status() );
});