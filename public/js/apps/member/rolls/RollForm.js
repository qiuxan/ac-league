var RollForm = {
    viewModel : null,
    notifier: null
}

RollForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _roll_id,
            member_id: '',
            roll_code: '',
            quantity: '',
            load: function( onComplete )
            {
                var self = this;

                if( _roll_id )
                {
                    $.get( '/member/getRoll', { id : _roll_id }, function( roll )
                    {
                        for( var key in roll )
                        {
                            self.set( key, roll[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        RollForm.addKendoElements();
                    });
                }
                else
                {
                    RollForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Roll';
            }
        });

    return viewModel;
}

RollForm.loadViewModel = function()
{
    RollForm.viewModel = RollForm.getViewModel();
    kendo.bind( $( '#rollFormDiv' ), RollForm.viewModel );
    RollForm.viewModel.load();
}

RollForm.addListeners = function()
{
    $( "#cancelButton" ).click( RollForm.showRollList );

    $( "#saveButton" ).click( function()
    {
        RollForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        RollForm.validateForm( true );
    });
}

RollForm.showRollList = function()
{
    _roll_id = 0;
    $( '#mainContentDiv' ).load( "/member/getRollList" );
}

RollForm.validator = function()
{
    return $( "#rollForm" ).kendoValidator().data( "kendoValidator" );
}

RollForm.status = function()
{
    return $( "span.status" );
}

RollForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

RollForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

RollForm.validateForm = function( returnToList )
{
    if( RollForm.validator().validate() )
    {
        RollForm.save( returnToList );
    }
    else
    {
        RollForm.notifier.notifyError( 'Please complete all required fields.' );
        RollForm.enableSaveButtons();
    }
}

RollForm.save = function( returnToList, onComplete )
{
    RollForm.notifier.notifyProgress( 'Saving Roll...' );
    $.post( "/member/saveRoll", $( "#rollForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.roll_id) > 0 )
        {
            if( _roll_id == 0 )
            {
                _roll_id = response.roll_id;
            }

            RollForm.notifier.notifyComplete( 'Roll Saved' );
            RollForm.viewModel.set( 'id', response.roll_id );

            if( returnToList )
            {
                RollForm.showRollList();
            }
            else
            {
                RollForm.viewModel.load( onComplete );
            }
        }
        else
        {
            RollForm.notifier.notifyError( 'Roll could not be saved' );
        }
    });
}

RollForm.addKendoElements = function() {
    $( "#quantity" ).kendoNumericTextBox({
        min: 100,
        max: 10000,
        format: "0"
    });

    $("#production_partner_id").kendoDropDownList({
        optionLabel: "Select A Production Partner",
        template: $("#production_partner_template").html(),
        dataTextField: "name_en",
        dataValueField: "id",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "/member/getContractManufacturers"
                }
            }
        },
        change: function() {
            BatchForm.viewModel.set('product_id', 0);
            BatchForm.loadProductList();
            BatchForm.viewModel.set('reseller_id', 0);
            BatchForm.loadResellerList();
        }
    });

    RollForm.initGrid();
}

/*** start code list ***/

RollForm.initGrid = function()
{
    $("#codeFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "400px",
        height: "220px",
        title: "Code Detail",
        resizable: true,
        modal: true,
        visible: false
    });

    $( '#grid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: RollForm.getDataSource(),
            height: $( window ).height() - 160,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'order_number', title: '#', width: 100 },
                { field: 'full_code', title: 'Code' },
                { field: 'disposition', title: 'Disposition' },
                { field: 'updated_at', title: 'Updated' }],
            change: function( e )
            {
                RollForm.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                RollForm.setSelected( this.select() );
            },
            pageable: {
                refresh: true,
                pageSizes: [500, 1000, 1500, "all"],
                buttonCount: 5
            },
            excel: {
                fileName: "RollReport.xlsx",
                allPages: true
            }
        });
    RollForm.addGridListeners();
    RollForm.addGridKendoElements();
}

RollForm.getDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: page_size,
            transport:
            {
                read:
                {
                    url: "/member/getRollCodes",
                    dataType: 'json',
                    data:
                    {
                        filters: RollForm.getFilters(),
                        roll_id: _roll_id
                    }
                }
            },
            schema:
            {
                model: RollForm.getModel(),
                data: 'data',
                total: 'total'
            },
            sort: { field: 'id', dir: 'desc' }
        });
}

RollForm.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

RollForm.getFilters = function()
{
    var filters =
    {
        search: function()
        {
            return $( '#searchFilter' ).val();
        },
        disposition_id: function()
        {
            return $( '#filter_disposition_id' ).val();
        }
    }

    return filters;
}

RollForm.filterGrid = function()
{
    RollForm.getGrid().dataSource.filter({});
}

RollForm.editCode = function()
{
    var uid = ( RollForm.getGrid().select().data() ) ? RollForm.getGrid().select().data().uid : null;
    if( uid )
    {
        var selected = RollForm.getGrid().dataSource.getByUid( uid );
        _code_id = selected.id;

        RollForm.showCodeForm();
    }
}

RollForm.addGridListeners = function()
{
    $( 'table' ).dblclick( RollForm.editCode );
    $( '#searchFilter' ).keyup( RollForm.filterGrid );
    $( '#searchFilter' ).click( RollForm.filterGrid );
    $( '#editButton' ).click( RollForm.editCode );
    $( '#exportButton' ).click( RollForm.exportToExcel );
}

RollForm.addGridKendoElements = function()
{
    $("#filter_disposition_id").kendoDropDownList().change(RollForm.filterGrid);
}


RollForm.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editButton' ).addClass( 'k-state-disabled' );
    }
}

RollForm.getGrid = function()
{
    return $( '#grid' ).data( 'kendoGrid' );
}

/*** end code list ***/

RollForm.showCodeForm = function(){
    $("#codeFormContainer").data("kendoWindow").center();
    $("#codeFormContainer").data("kendoWindow").open();
    $("#codeFormContainer").load( "/member/getCodeForm");
}

RollForm.refreshCodeList = function() {
    $( "#codeFormContainer" ).data("kendoWindow").close();
    RollForm.filterGrid();
}

RollForm.exportToExcel = function()
{
    RollForm.getGrid().saveAsExcel();
}


$( document ).ready( function()
{
    RollForm.loadViewModel();
    RollForm.addListeners();

    RollForm.notifier = Utils.notifier();
    RollForm.notifier.status( RollForm.status() );
});