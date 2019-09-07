var BatchForm = {
    viewModel : null,
    notifier: null
}

BatchForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _batch_id,
            member_id: '',
            product_id: '',
            batch_code: '',
            quantity: 0,
            location: '',
            disposition: '',
            load: function( onComplete )
            {
                var self = this;

                if( _batch_id )
                {
                    $.get( '/member/getBatch', { id : _batch_id }, function( batch )
                    {
                        for( var key in batch )
                        {
                            self.set( key, batch[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        BatchForm.addKendoElements();
                    });
                }
                else
                {
                    BatchForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            isAssigned: function()
            {
                return this.get( 'id' ) != 0 && this.get( 'product_id' ) != 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Batch';
            }
        });

    return viewModel;
}

BatchForm.loadViewModel = function()
{
    BatchForm.viewModel = BatchForm.getViewModel();
    kendo.bind( $( '#batchFormDiv' ), BatchForm.viewModel );
    BatchForm.viewModel.load();
}

BatchForm.addListeners = function()
{
    $( "#cancelButton" ).click( BatchForm.showBatchList );

    $( "#saveButton" ).click( function()
    {
        BatchForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        BatchForm.validateForm( true );
    });
}

BatchForm.showBatchList = function()
{
    _batch_id = 0;
    $( '#mainContentDiv' ).load( "/member/getBatchList" );
}

BatchForm.validator = function()
{
    return $( "#batchForm" ).kendoValidator().data( "kendoValidator" );
}

BatchForm.status = function()
{
    return $( "span.status" );
}

BatchForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

BatchForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

BatchForm.validateForm = function( returnToList )
{
    if( BatchForm.validator().validate() )
    {
        BatchForm.save( returnToList );
    }
    else
    {
        BatchForm.notifier.notifyError( 'Please complete all required fields.' );
        BatchForm.enableSaveButtons();
    }
}

BatchForm.save = function( returnToList, onComplete )
{
    BatchForm.notifier.notifyProgress( 'Saving Batch...' );
    $.post( "/member/saveBatch", $( "#batchForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.batch_id) > 0 )
        {
            if( _batch_id == 0 )
            {
                _batch_id = response.batch_id;
            }

            BatchForm.notifier.notifyComplete( 'Batch Saved' );
            BatchForm.viewModel.set( 'id', response.batch_id );

            if( returnToList )
            {
                BatchForm.showBatchList();
            }
            else
            {
                BatchForm.viewModel.load( onComplete );
            }
        }
        else
        {
            BatchForm.notifier.notifyError( 'Batch could not be saved' );
        }
    });
}

BatchForm.addKendoElements = function() {
    $( '#tabs' ).kendoTabStrip();
    BatchForm.loadProductList();

    $("#disposition_id").kendoDropDownList();

    $("#label_size").kendoDropDownList();

    if(!$("#reseller_id").data('kendoDropDownList')){
        $("#reseller_id").kendoDropDownList();
    }
    
    if(!$("#quantity").data('kendoNumericTextBox'))
    {
        $( "#quantity" ).kendoNumericTextBox({
            min: 1,
            format: "0"
        });
    }

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

    $('#batch_code').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9\-]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    BatchForm.initGrid();
}

BatchForm.loadProductList = function () {
    $("#product_id").kendoDropDownList({
        optionLabel: "Select A Product",
        template: $("#product_template").html(),
        dataTextField: "name_en",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/member/getAllProducts"
                }
            }
        }
    });
}

/*** start code list ***/

BatchForm.initGrid = function()
{
    $("#batchRollFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "460px",
        height: "220px",
        title: "Code Detail",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: BatchForm.getDataSource(),
            height: $( window ).height() - 200,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'roll_code', title: 'Roll ID' },
                { field: 'start_code', title: 'Start Code' },
                { field: 'end_code', title: 'End Code' },
                { field: 'code_quantity', title: 'Quantity' }],
            change: function( e )
            {
                BatchForm.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                BatchForm.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            }
        });
    BatchForm.addGridListeners();
}

BatchForm.getDataSource = function()
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
                    url: "/member/getBatchRolls",
                    dataType: 'json',
                    data:
                    {
                        filters: BatchForm.getFilters(),
                        batch_id: _batch_id
                    }
                }
            },
            schema:
            {
                model: BatchForm.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

BatchForm.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

BatchForm.getFilters = function()
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

BatchForm.filterGrid = function()
{
    BatchForm.getGrid().dataSource.filter({});
}

BatchForm.addBatchRoll = function()
{
    if(_batch_id != 0)
    {
        _batch_roll_id = 0;
        BatchForm.showBatchRollForm();
    }
    else
    {
        Utils.alert().show("Warning!", "Please save Batch first, then add roll for batch!");
    }
}

BatchForm.editBatchRoll = function()
{
    var uid = ( BatchForm.getGrid().select().data() ) ? BatchForm.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = BatchForm.getGrid().dataSource.getByUid( uid );
        _batch_roll_id = selected.id;

        BatchForm.showBatchRollForm();
    }
}

BatchForm.addGridListeners = function()
{
    $( 'table' ).dblclick( BatchForm.editBatchRoll );
    $( '#searchFilter' ).keyup( BatchForm.filterGrid );
    $( '#searchFilter' ).click( BatchForm.filterGrid );
    $( '#addButton' ).click( BatchForm.addBatchRoll );
    $( '#editButton' ).click( BatchForm.editBatchRoll );
    $( '#deleteButton' ).click( BatchForm.deleteBatchRolls );
}

BatchForm.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editButton' ).addClass( 'k-state-disabled' );
    }

    if( selectedRows.length > 0 )
    {
        $( '#deleteButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deleteButton' ).addClass( 'k-state-disabled' );
    }
}

BatchForm.deleteBatchRolls = function()
{
    var ids = [];
    var selected = BatchForm.getGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( BatchForm.getGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteBatchRolls", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            BatchForm.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected batch-rolls?");
}

BatchForm.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

/*** end code list ***/

BatchForm.showBatchRollForm = function(){
    $("#batchRollFormContainer").data("kendoWindow").center();
    $("#batchRollFormContainer").data("kendoWindow").open();
    $("#batchRollFormContainer").load( "/member/getBatchRollForm");
}

BatchForm.refreshCodeList = function() {
    $( "#batchRollFormContainer" ).data("kendoWindow").close();
    BatchForm.filterGrid();
}

$( document ).ready( function()
{
    BatchForm.loadViewModel();
    BatchForm.addListeners();

    BatchForm.notifier = Utils.notifier();
    BatchForm.notifier.status( BatchForm.status() );
});