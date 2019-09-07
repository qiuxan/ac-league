var CartonForm = {
    viewModel : null,
    notifier: null
}

CartonForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _carton_id,
            load: function( onComplete )
            {
                var self = this;

                if( _carton_id )
                {
                    $.get( '/member/getCarton', { id : _carton_id }, function( carton )
                    {
                        for( var key in carton )
                        {
                            self.set( key, carton[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        CartonForm.addKendoElements();
                    });
                }
                else
                {
                    CartonForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            }
        });

    return viewModel;
}

CartonForm.loadViewModel = function()
{
    CartonForm.viewModel = CartonForm.getViewModel();
    kendo.bind( $( '#cartonFormDiv' ), CartonForm.viewModel );
    CartonForm.viewModel.load();
}

// button listeners
CartonForm.addListeners = function()
{
    $( "#cancelButton" ).click( CartonForm.showCartonList );

    $( "#saveButton" ).click( function()
    {
        CartonForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        CartonForm.validateForm( true );
    });

    $( "#selectBatch" ).click( CartonForm.showBatchList );

    $( "#clearBatch" ).click( function(){
        $("#batch_code").val("");
        $("#batch_id").val(0);
    });

    $( "#selectProduct" ).click( CartonForm.showProductList );
}

CartonForm.showCartonList = function()
{
    _carton_id = 0;
    $( '#mainContentDiv' ).load( "/member/getCartonList" );
}

CartonForm.validator = function()
{
    return $( "#cartonFormDiv" ).kendoValidator().data( "kendoValidator" );
}

CartonForm.status = function()
{
    return $( "span.status" );
}

CartonForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

CartonForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

CartonForm.validateForm = function( returnToList )
{
    if( CartonForm.validator().validate() )
    {
        CartonForm.save( returnToList );
    }
    else
    {
        CartonForm.notifier.notifyError( 'Please complete all required fields.' );
        CartonForm.enableSaveButtons();
    }
}

CartonForm.save = function( returnToList, onComplete )
{
    if (!$('#product_id').val()) {
        CartonForm.notifier.notifyProgress( 'Please select a product for the carton!' );
        return;
    }

    CartonForm.notifier.notifyProgress( 'Saving Carton...' );
    $.post( "/member/saveCarton", $( "#cartonForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.carton_id) > 0 )
        {
            if( _carton_id == 0 )
            {
                _carton_id = response.carton_id;
            }

            CartonForm.notifier.notifyComplete( 'Carton Saved' );
            CartonForm.viewModel.set( 'id', response.carton_id );

            if( returnToList )
            {
                CartonForm.showCartonList();
            }
            else
            {
                // this will cause the kendo element loading many times
                // CartonForm.viewModel.load( onComplete );
                
                // refresh the carton items list: destroy old grid and create a new one
                $( '#carton_items_grid' ).data('kendoGrid').destroy();
                $( '#carton_items_grid' ).kendoGrid(
                    {
                        toolbar: kendo.template( $( '#cartonItemToolbarTemplate' ).html() ),
                        dataSource: CartonForm.getCartonItemDataSource(),
                        height: $( window ).height() - 200,
                        sortable: true,
                        selectable: 'multiple',
                        columns: [
                            { field: 'full_code', title: 'Full Code' },
                            { field: 'batch_code', title: 'Batch#' }
                        ],
                        pageable: {
                            refresh: true,
                            pageSizes: true,
                            buttonCount: 5
                        },
                        change: function( e )
                        {
                            CartonForm.setSelected( this.select() );
                        },
                        dataBound: function( e )
                        {
                            CartonForm.setSelected( this.select() );
                        }
                    }
                ); 
            }
        }
        else
        {
            if (response.error) {
                CartonForm.notifier.notifyError( response.error_msg );
            } else {
                CartonForm.notifier.notifyError( 'Carton could not be saved' );
            }
        }
    });
}

CartonForm.addKendoElements = function() {
    $( '#tabs' ).kendoTabStrip();    
    if(!$("#production_partner_id").data('kendoDropDownList')){
        $("#production_partner_id").kendoDropDownList();
    }

    // popup windows
    $("#productListContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "460px",
        height: "280px",
        title: "Select Product",
        resizable: true,
        modal: true,
        visible: false
    });

    $("#batchListContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "460px",
        height: "280px",
        title: "Select Batch",
        resizable: true,
        modal: true,
        visible: false
    });

    // init the carton items grid
    CartonForm.initCartonItemsGrid();
}

// *************************************************************************************************
// *************************************************************************************************
// *************************************************************************************************
/*** start items list ***/

CartonForm.initCartonItemsGrid = function()
{
    // init popup window
    $("#availableCodesListContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "460px",
        height: "280px",
        title: "Select Carton Items",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#carton_items_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#cartonItemToolbarTemplate' ).html() ),
            dataSource: CartonForm.getCartonItemDataSource(),
            height: $( window ).height() - 200,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'full_code', title: 'Full Code' },
                { field: 'batch_code', title: 'Batch#' }
            ],
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            change: function( e )
            {
                CartonForm.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                CartonForm.setSelected( this.select() );
            }
        }
    );     
        
    CartonForm.addCartonItemGridListeners();
}

CartonForm.addCartonItemGridListeners = function(){
    $( '#addCartonItemButton' ).click( CartonForm.addCartonItem );
    $( '#deleteCartonItemButton' ).click( CartonForm.deleteCartonItems );  
    $( '#searchCartonItem' ).keyup( CartonForm.filterCartonItemsGrid );
    $( '#searchCartonItem' ).click( CartonForm.filterCartonItemsGrid );      
}

CartonForm.addCartonItem = function(){
    if(_carton_id != 0)
    {
        _carton_item_id = 0;
        CartonForm.showAvailableCodesList();
    }
    else
    {
        Utils.alert().show("Please save carton first, then add codes segment for carton!");
    }    
}

CartonForm.deleteCartonItems = function(){
    var ids = [];
    var selected = CartonForm.getCartonItemGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( CartonForm.getCartonItemGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteCartonItems", {ids: ids, _token: $('[name="_token"]').val(), carton_id: _carton_id}, function () {
            CartonForm.refreshCodeList();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected carton items?");    
}

CartonForm.getCartonItemDataSource = function()
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
                    url: "/member/getCartonItems",
                    dataType: 'json',
                    data:
                    {
                        carton_id: _carton_id,
                        filters: CartonForm.getCartonItemsFilters()
                    }
                }
            },
            schema:
            {
                model: CartonForm.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        }
    );
}

CartonForm.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

CartonForm.getCartonItemGrid = function()
{
    return $( '#carton_items_grid' ).data( 'kendoGrid' );
}

CartonForm.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editCartonItemButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editCartonItemButton' ).addClass( 'k-state-disabled' );
    }

    if( selectedRows.length > 0 )
    {
        $( '#deleteCartonItemButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deleteCartonItemButton' ).addClass( 'k-state-disabled' );
    }
}

CartonForm.getCartonItemsFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchCartonItem' ).val();
        }
    }

    return filters;
}

CartonForm.filterCartonItemsGrid = function()
{
    CartonForm.getCartonItemGrid().dataSource.filter({});
}

/*** end items list ***/

// popup windows
CartonForm.showAvailableCodesList = function(){
    $("#availableCodesListContainer").data("kendoWindow").center();
    $("#availableCodesListContainer").data("kendoWindow").open();
    $("#availableCodesListContainer").load( "/member/getCartonAvailableCodesList");
}

CartonForm.showProductList = function(){
    $("#productListContainer").data("kendoWindow").center();
    $("#productListContainer").data("kendoWindow").open();
    $("#productListContainer").load( "/member/getCartonProductList");
}

CartonForm.showBatchList = function(){
    // tell user select product first if product is not selected
    if($("#product_id").val()==''){
        Utils.alert().show("Warning", "Please select product first"); 
        return;
    }
    $("#batchListContainer").data("kendoWindow").center();
    $("#batchListContainer").data("kendoWindow").open();
    $("#batchListContainer").load( "/member/getCartonBatchList");
}

CartonForm.init = function() {
}

CartonForm.refreshCodeList = function() {
    CartonForm.getCartonItemGrid().dataSource.filter({});
}

$( document ).ready( function()
{
    CartonForm.init();

    CartonForm.loadViewModel();
    CartonForm.addListeners();

    CartonForm.notifier = Utils.notifier();
    CartonForm.notifier.status( CartonForm.status() );
});