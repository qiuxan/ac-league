var PalletForm = {
    viewModel : null,
    notifier: null
}

PalletForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _pallet_id,
            load: function( onComplete )
            {
                var self = this;

                if( _pallet_id )
                {
                    $.get( '/member/getPallet', { id : _pallet_id }, function( pallet )
                    {
                        for( var key in pallet )
                        {
                            self.set( key, pallet[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        PalletForm.addKendoElements();
                    });
                }
                else
                {
                    PalletForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            }
        });

    return viewModel;
}

PalletForm.loadViewModel = function()
{
    PalletForm.viewModel = PalletForm.getViewModel();
    kendo.bind( $( '#palletFormDiv' ), PalletForm.viewModel );
    PalletForm.viewModel.load();
}

// button listeners
PalletForm.addListeners = function()
{
    $( "#cancelButton" ).click( PalletForm.showPalletList );

    $( "#saveButton" ).click( function()
    {
        PalletForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        PalletForm.validateForm( true );
    });

    $( "#selectBatch" ).click( PalletForm.showBatchList );

    $( "#clearBatch" ).click(function(){
        $("#batch_id").val(0);
        $("#batch_code").val("");
    });

    $( "#selectProduct" ).click( PalletForm.showProductList );
}

PalletForm.showPalletList = function()
{
    _pallet_id = 0;
    $( '#mainContentDiv' ).load( "/member/getPalletList" );
}

PalletForm.validator = function()
{
    return $( "#palletFormDiv" ).kendoValidator().data( "kendoValidator" );
}

PalletForm.status = function()
{
    return $( "span.status" );
}

PalletForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

PalletForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

PalletForm.validateForm = function( returnToList )
{
    if( PalletForm.validator().validate() )
    {
        PalletForm.save( returnToList );
    }
    else
    {
        PalletForm.notifier.notifyError( 'Please complete all required fields.' );
        PalletForm.enableSaveButtons();
    }
}

PalletForm.save = function( returnToList, onComplete )
{
    if (!$('#product_id').val()) {
        PalletForm.notifier.notifyProgress( 'Please select a product for the pallet!' );
        return;
    }

    PalletForm.notifier.notifyProgress( 'Saving Pallet...' );
    $.post( "/member/savePallet", $( "#palletForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.pallet_id) > 0 )
        {
            if( _pallet_id == 0 )
            {
                _pallet_id = response.pallet_id;
            }

            PalletForm.notifier.notifyComplete( 'Pallet Saved' );
            PalletForm.viewModel.set( 'id', response.pallet_id );

            if( returnToList )
            {
                PalletForm.showPalletList();
            }
            else
            {
                // this will cause the kendo element loading many times
                // PalletForm.viewModel.load( onComplete );
                
                // refresh the pallet items list: destroy old grid and create a new one
                $( '#pallet_items_grid' ).data('kendoGrid').destroy();
                $( '#pallet_items_grid' ).kendoGrid(
                    {
                        toolbar: kendo.template( $( '#palletItemToolbarTemplate' ).html() ),
                        dataSource: PalletForm.getPalletItemDataSource(),
                        height: $( window ).height() - 200,
                        sortable: true,
                        selectable: 'multiple',
                        columns: [
                            { field: 'full_code', title: 'Full Code' },
                            { field: 'sscc2_sn', title: 'Carton SSCC2_SN'},
                            { field: 'batch_code', title: 'Batch #'}
                        ],
                        pageable: {
                            refresh: true,
                            pageSizes: true,
                            buttonCount: 5
                        },
                        change: function( e )
                        {
                            PalletForm.setSelectedItem( this.select() );
                        },
                        dataBound: function( e )
                        {
                            PalletForm.setSelectedItem( this.select() );
                        }
                    }
                ); 

                // refresh the pallet carton list: destroy old grid and create a new one
                $( '#pallet_cartons_grid' ).data('kendoGrid').destroy();
                $( '#pallet_cartons_grid' ).kendoGrid(
                    {
                        toolbar: kendo.template( $( '#palletCartonToolbarTemplate' ).html() ),
                        dataSource: PalletForm.getPalletCartonDataSource(),
                        height: $( window ).height() - 200,
                        sortable: true,
                        selectable: 'multiple',
                        columns: [
                            { field: 'sscc2_sn', title: 'SSCC2 SN' },
                            { field: 'batch_code', title: 'Batch #' }                
                        ],
                        pageable: {
                            refresh: true,
                            pageSizes: true,
                            buttonCount: 5
                        },
                        change: function( e )
                        {
                            PalletForm.setSelectedCarton( this.select() );
                        },
                        dataBound: function( e )
                        {
                            PalletForm.setSelectedCarton( this.select() );
                        }
                    }
                );     

            }
        }
        else
        {
            if (response.error) {
                PalletForm.notifier.notifyError( response.error_msg );
            } else {
                PalletForm.notifier.notifyError( 'Pallet could not be saved' );
            }
        }
    });
}

PalletForm.addKendoElements = function() {
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

    // init the pallet items grid
    PalletForm.initPalletItemsGrid();
    // init the pallet cartons grid
    PalletForm.initPalletCartonsGrid();    
}

// *************************************************************************************************
// *************************************************************************************************
// *************************************************************************************************
/*** start items list ***/

PalletForm.initPalletItemsGrid = function()
{
    // init popup window
    $("#availableCodesListContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "460px",
        height: "280px",
        title: "Select Pallet Items",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#pallet_items_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#palletItemToolbarTemplate' ).html() ),
            dataSource: PalletForm.getPalletItemDataSource(),
            height: $( window ).height() - 200,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'full_code', title: 'Full Code' },
                { field: 'sscc2_sn', title: 'Carton SSCC2_SN'},
                { field: 'batch_code', title: 'Batch #'}
            ],
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            change: function( e )
            {
                PalletForm.setSelectedItem( this.select() );
            },
            dataBound: function( e )
            {
                PalletForm.setSelectedItem( this.select() );
            }
        }
    );     
        
    PalletForm.addPalletItemGridListeners();
}

PalletForm.addPalletItemGridListeners = function(){
    $( '#addPalletItemButton' ).click( PalletForm.addPalletItem );
    $( '#deletePalletItemButton' ).click( PalletForm.deletePalletItems );
    $( '#searchPalletItem' ).keyup( PalletForm.filterPalletItemsGrid );
    $( '#searchPalletItem' ).click( PalletForm.filterPalletItemsGrid );
}

PalletForm.addPalletItem = function(){
    if(_pallet_id != 0)
    {
        _pallet_item_id = 0;
        PalletForm.showAvailableCodesList();
    }
    else
    {
        Utils.alert().show("Warning", "Please save pallet first, then add codes segment for pallet!");
    }    
}

PalletForm.deletePalletItems = function(){
    var ids = [];
    var selected = PalletForm.getPalletItemGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        var id = PalletForm.getPalletItemGrid().dataItem( selected[i] )['id'];
        var carton_id = PalletForm.getPalletItemGrid().dataItem( selected[i] )['carton_id'];

        if( carton_id != -1){
            Utils.alert().show("Warning", "You have selected items that belong to carton, please delete those items in the Carton part.");            
            return;
        }

        ids.push( id );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deletePalletItems", {ids: ids, _token: $('[name="_token"]').val(), pallet_id: _pallet_id}, function () {
            PalletForm.refreshCodeList();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected pallet items?");    
}

PalletForm.getPalletItemDataSource = function()
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
                    url: "/member/getPalletItems",
                    dataType: 'json',
                    data:
                    {
                        pallet_id: _pallet_id,
                        filters: PalletForm.getPalletItemsFilters()                        
                    }
                }
            },
            schema:
            {
                model: PalletForm.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        }
    );
}

PalletForm.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

PalletForm.getPalletItemGrid = function()
{
    return $( '#pallet_items_grid' ).data( 'kendoGrid' );
}

PalletForm.setSelectedItem = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editPalletItemButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editPalletItemButton' ).addClass( 'k-state-disabled' );
    }

    if( selectedRows.length > 0 )
    {
        $( '#deletePalletItemButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deletePalletItemButton' ).addClass( 'k-state-disabled' );
    }
}

PalletForm.getPalletItemsFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchPalletItem' ).val();
        }
    }

    return filters;
}

PalletForm.filterPalletItemsGrid = function()
{
    PalletForm.getPalletItemGrid().dataSource.filter({});
}

/*** end items list ***/

// *************************************************************************************************
// *************************************************************************************************
// *************************************************************************************************
/*** start pallet cartons list ***/

PalletForm.initPalletCartonsGrid = function()
{
    // init popup window
    $("#availableCartonsListContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "460px",
        height: "280px",
        title: "Select Pallet Cartons",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#pallet_cartons_grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#palletCartonToolbarTemplate' ).html() ),
            dataSource: PalletForm.getPalletCartonDataSource(),
            height: $( window ).height() - 200,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'sscc2_sn', title: 'SSCC2 SN' },
                { field: 'batch_code', title: 'Batch #' }                
            ],
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            change: function( e )
            {
                PalletForm.setSelectedCarton( this.select() );
            },
            dataBound: function( e )
            {
                PalletForm.setSelectedCarton( this.select() );
            }
        }
    );     
        
    PalletForm.addPalletCartonGridListeners();
}

PalletForm.addPalletCartonGridListeners = function(){
    $( '#addPalletCartonButton' ).click( PalletForm.addPalletCarton );
    $( '#deletePalletCartonButton' ).click( PalletForm.deletePalletCartons );    
    $( '#searchPalletCarton' ).keyup( PalletForm.filterPalletCartonsGrid );
    $( '#searchPalletCarton' ).click( PalletForm.filterPalletCartonsGrid );    
}

PalletForm.addPalletCarton = function(){
    if(_pallet_id != 0)
    {
        _pallet_carton_id = 0;
        PalletForm.showAvailableCartonsList();
    }
    else
    {
        Utils.alert().show("Please save pallet first, then add codes segment for pallet!");
    }    
}

PalletForm.deletePalletCartons = function(){
    var ids = [];
    var selected = PalletForm.getPalletCartonGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( PalletForm.getPalletCartonGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deletePalletCartons", {ids: ids, _token: $('[name="_token"]').val(), pallet_id: _pallet_id}, function () {
            PalletForm.getPalletCartonGrid().dataSource.filter({});
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected pallet items?");    
}

PalletForm.getPalletCartonDataSource = function()
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
                    url: "/member/getPalletCartons",
                    dataType: 'json',
                    data:
                    {
                        pallet_id: _pallet_id,
                        filters: PalletForm.getPalletCartonsFilters()
                    }
                }
            },
            schema:
            {
                model: PalletForm.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        }
    );
}

PalletForm.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

PalletForm.getPalletCartonGrid = function()
{
    return $( '#pallet_cartons_grid' ).data( 'kendoGrid' );
}

PalletForm.setSelectedCarton = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editPalletCartonButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editPalletCartonButton' ).addClass( 'k-state-disabled' );
    }

    if( selectedRows.length > 0 )
    {
        $( '#deletePalletCartonButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deletePalletCartonButton' ).addClass( 'k-state-disabled' );
    }
}

PalletForm.getPalletCartonsFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchPalletCarton' ).val();
        }
    }

    return filters;
}

PalletForm.filterPalletCartonsGrid = function()
{
    PalletForm.getPalletCartonGrid().dataSource.filter({});
}

/*** end pallet cartons list ***/

// popup windows
PalletForm.showAvailableCodesList = function(){
    $("#availableCodesListContainer").data("kendoWindow").center();
    $("#availableCodesListContainer").data("kendoWindow").open();
    $("#availableCodesListContainer").load( "/member/getPalletAvailableCodesList");
}

PalletForm.showAvailableCartonsList = function(){
    $("#availableCartonsListContainer").data("kendoWindow").center();
    $("#availableCartonsListContainer").data("kendoWindow").open();
    $("#availableCartonsListContainer").load( "/member/getPalletAvailableCartonsList");
}

PalletForm.showProductList = function(){
    $("#productListContainer").data("kendoWindow").center();
    $("#productListContainer").data("kendoWindow").open();
    $("#productListContainer").load( "/member/getPalletProductList");
}

PalletForm.showBatchList = function(){
    // tell user select product first if product is not selected
    if($("#product_id").val()==''){
        Utils.alert().show("Warning", "Please select product first"); 
        return;
    }    
    $("#batchListContainer").data("kendoWindow").center();
    $("#batchListContainer").data("kendoWindow").open();
    $("#batchListContainer").load( "/member/getPalletBatchList");
}

PalletForm.init = function() {
}

PalletForm.refreshCodeList = function() {
    PalletForm.getPalletItemGrid().dataSource.filter({});
}

$( document ).ready( function()
{
    PalletForm.init();

    PalletForm.loadViewModel();
    PalletForm.addListeners();

    PalletForm.notifier = Utils.notifier();
    PalletForm.notifier.status( PalletForm.status() );
});