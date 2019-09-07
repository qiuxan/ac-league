var CodeForm = {
    viewModel : null,
    notifier: null
}

CodeForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _code_id,
            company_en: '',
            product_name: '',
            full_code: '',
            location: '',
            disposition_id: '',
            reseller_id: '',
            load: function( onComplete )
            {
                var self = this;

                if( _code_id )
                {
                    $.get( '/member/getCode', { id : _code_id }, function( code )
                    {
                        for( var key in code )
                        {
                            self.set( key, code[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        CodeForm.addKendoElements();
                    });
                }
                else
                {
                    CodeForm.addKendoElements();
                }
            },
            isAssigned: function()
            {
                return this.get( 'id' ) != 0 && this.get( 'product_name' ) != '';
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'Add Code';
            }
        });

    return viewModel;
}

CodeForm.loadViewModel = function()
{
    CodeForm.viewModel = CodeForm.getViewModel();
    kendo.bind( $( '#codeForm' ), CodeForm.viewModel );
    CodeForm.viewModel.load();
}

CodeForm.addListeners = function()
{
    $( "#cancelButtonCodeForm" ).click(
        function() {
            if(typeof RollForm != "undefined")
            {
                RollForm.refreshCodeList();
            }
            else if(typeof CodeList != "undefined")
            {
                CodeList.refreshCodeList();
            }
            else
            {
                $( "#codeFormContainer" ).data("kendoWindow").close();
            }
        }
    );

    $( "#saveButtonCodeForm" ).click( function()
    {
        CodeForm.validateForm( false );
    });

    $( "#doneButtonCodeForm" ).click( function()
    {
        CodeForm.validateForm( true );
    });
}

CodeForm.showCodeList = function()
{
    _code_id = 0;
    $( '#mainContentDiv' ).load( "/member/getCodeList" );
}

CodeForm.validator = function()
{
    return $( "#codeForm" ).kendoValidator().data( "kendoValidator" );
}

CodeForm.status = function()
{
    return $( "span.status" );
}

CodeForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

CodeForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

CodeForm.validateForm = function( returnToList )
{
    if( CodeForm.validator().validate() )
    {
        CodeForm.save( returnToList );
    }
    else
    {
        CodeForm.notifier.notifyError( 'Please complete all required fields.' );
        CodeForm.enableSaveButtons();
    }
}

CodeForm.save = function( returnToList, onComplete )
{
    CodeForm.notifier.notifyProgress( 'Saving Code...' );
    $.post( "/member/saveCode", $( "#codeForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.code_id) > 0 )
        {
            if( _code_id == 0 )
            {
                _code_id = response.code_id;
            }

            CodeForm.notifier.notifyComplete( 'Code Saved' );
            CodeForm.viewModel.set( 'id', response.code_id );

            if( returnToList )
            {
                if(typeof RollForm != "undefined")
                {
                    RollForm.refreshCodeList();
                }
                else if(typeof CodeList != "undefined")
                {
                    CodeList.refreshCodeList();
                }
                else
                {
                    $( "#codeFormContainer" ).data("kendoWindow").close();
                }
            }
            else
            {
                CodeForm.viewModel.load( onComplete );
            }
        }
        else
        {
            CodeForm.notifier.notifyError( 'Code could not be saved' );
        }
    });
}

CodeForm.addKendoElements = function() {
    $("#disposition_id").kendoDropDownList();
    if(!$("#reseller_id").data('kendoDropDownList')){
        $("#reseller_id").kendoDropDownList(
            {optionLabel: "Select A Reseller"}
        );  
    }    
}

$( document ).ready( function()
{
    CodeForm.loadViewModel();
    CodeForm.addListeners();

    CodeForm.notifier = Utils.notifier();
    CodeForm.notifier.status( CodeForm.status() );
});