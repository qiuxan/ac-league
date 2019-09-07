var SurveyQuestionForm = {
    viewModel : null,
    notifier: null
}

SurveyQuestionForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _question_id,
            survey_id: '',
            type: 0,
            question_en: '',
            question_cn: '',
            question_tr: '',
            required: '',
            published: '',
            priority: 0,
            load: function( onComplete )
            {
                var self = this;

                if( _question_id )
                {
                    $.get( '/member/getSurveyQuestion', { id : _question_id }, function( question )
                    {
                        for( var key in question )
                        {
                            self.set( key, question[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        SurveyQuestionForm.addKendoElements();
                    });
                }
                else
                {
                    self.set( 'survey_id', _survey_id );
                    SurveyQuestionForm.addKendoElements();
                }
            },
            hasOptions: function()
            {
                return (this.get( 'id' ) > 0 && this.get('type') == 2);
            }
        });

    return viewModel;
}

SurveyQuestionForm.loadViewModel = function()
{
    SurveyQuestionForm.viewModel = SurveyQuestionForm.getViewModel();
    kendo.bind( $( '#surveyQuestionFormDiv' ), SurveyQuestionForm.viewModel );
    SurveyQuestionForm.viewModel.load();
}

SurveyQuestionForm.addListeners = function()
{
    $( "#cancelQuestionButton" ).click( SurveyForm.refreshQuestionList );

    $( "#saveQuestionButton" ).click( function()
    {
        SurveyQuestionForm.validateForm( false );
    });

    $( "#doneQuestionButton" ).click( function()
    {
        SurveyQuestionForm.validateForm( true );
    });
}

SurveyQuestionForm.status = function()
{
    return $( "span.status" );
}

SurveyQuestionForm.disableSaveButtons = function()
{
    $( "#saveOptionButton" ).prop( 'disabled', true );
    $( "#doneOptionButton" ).prop( 'disabled', true );
}

SurveyQuestionForm.enableSaveButtons = function()
{
    $( "#saveOptionButton" ).prop( 'disabled', false );
    $( "#doneOptionButton" ).prop( 'disabled', false );
}

SurveyQuestionForm.validator = function()
{
    return $( "#surveyQuestionForm" ).kendoValidator().data( "kendoValidator" );
}

SurveyQuestionForm.validateForm = function( returnToList )
{
    if( SurveyQuestionForm.validator().validate() )
    {
        SurveyQuestionForm.save( returnToList );
    }
    else
    {
        SurveyQuestionForm.notifier.notifyError( 'Please complete all required fields.' );
        SurveyQuestionForm.enableSaveButtons();
    }
}

SurveyQuestionForm.save = function( returnToList, onComplete )
{
    SurveyQuestionForm.notifier.notifyProgress( 'Saving Question...' );
    $.post( "/member/saveQuestion", $( "#surveyQuestionForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.question_id) > 0 )
        {
            if( _question_id == 0 )
            {
                _question_id = response.question_id;
            }

            SurveyQuestionForm.notifier.notifyComplete( 'Question Saved' );
            SurveyQuestionForm.viewModel.set( 'id', response.question_id );

            if( returnToList )
            {
                SurveyForm.refreshQuestionList();
            }
            else
            {
                SurveyQuestionForm.viewModel.load( onComplete );
            }
        }
        else
        {
            SurveyQuestionForm.notifier.notifyError( 'Question could not be saved' );
        }
    });
}

SurveyQuestionForm.addKendoElements = function() {
    $( '#questionTabs' ).kendoTabStrip();
    $("#type").kendoDropDownList();

    SurveyQuestionForm.initQuestionOptionGrid();
}

/*** start option list ***/

SurveyQuestionForm.initQuestionOptionGrid = function()
{
    $("#questionOptionFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "400px",
        height: "200px",
        title: "Option Detail",
        resizable: true,
        modal: true,
        visible: false,
        close: SurveyQuestionForm.filterOptionGrid
    });

    $( '#questionOptionGrid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#optionToolbarTemplate' ).html() ),
            dataSource: SurveyQuestionForm.getOptionDataSource(),
            height: 210,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'order_number', title: '#' },
                { field: 'option_en', title: 'Option' }],
            change: function( e )
            {
                SurveyQuestionForm.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                SurveyQuestionForm.setSelected( this.select() );
            }
        });

    $( '#questionOptionGrid' ).data( 'kendoGrid' ).table.kendoSortable({
        filter: ">tbody >tr",
        hint: $.noop,
        cursor: "move",
        placeholder: function(element) {
            return element.clone().addClass("k-state-hover").css("opacity", 0.65);
        },
        container: "#questionOptionGrid tbody",
        change: function(e) {
            var skip = $( '#questionOptionGrid' ).data( 'kendoGrid' ).dataSource.skip(),
                oldIndex = e.oldIndex + skip,
                newIndex = e.newIndex + skip,
                data = $( '#questionOptionGrid' ).data( 'kendoGrid' ).dataSource.data(),
                dataItem = $( '#questionOptionGrid' ).data( 'kendoGrid' ).dataSource.getByUid(e.item.data("uid"));

            $( '#questionOptionGrid' ).data( 'kendoGrid' ).dataSource.remove(dataItem);
            $( '#questionOptionGrid' ).data( 'kendoGrid' ).dataSource.insert(newIndex, dataItem);

            // update slides priorities
            dataItems = $( '#questionOptionGrid' ).data( 'kendoGrid' ).dataSource.view();
            var option_priorities = [];
            for (i=0; i<dataItems.length; i++) {
                dataItems[i].set('priority', i+1);
                option_priority = {
                    "id": dataItems[i]['id'],
                    "priority": dataItems[i]['priority']
                };
                option_priorities.push(option_priority);
            }
            option_priorities = {
                option_priorities: option_priorities
            };
            SurveyQuestionForm.updatePriorities(option_priorities);
        }
    });

    SurveyQuestionForm.addGridListeners();
}

SurveyQuestionForm.getOptionDataSource = function()
{
    return new kendo.data.DataSource(
        {
            serverPaging: true,
            serverSorting: true,
            pageSize: 50,
            transport:
            {
                read:
                {
                    url: "/member/getQuestionOptions",
                    dataType: 'json',
                    data:
                    {
                        question_id: _question_id
                    }
                }
            },
            schema:
            {
                model: SurveyQuestionForm.getModel()
            },
            sort: { field: 'priority', dir: 'asc' }
        });
}

SurveyQuestionForm.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

SurveyQuestionForm.updatePriorities = function(option_priorities)
{
    option_priorities._token = $('[name="_token"]').val();
    $.post("updateOptionPriorities", option_priorities, function(response){
        SurveyQuestionForm.filterOptionGrid();
    });
}

SurveyQuestionForm.addQuestionOption = function()
{
    if(_question_id != 0)
    {
        _option_id = 0;
        SurveyQuestionForm.showQuestionOptionForm();
    }
    else
    {
        Utils.alert().show("Warning!", "Please save survey first, then add option for question!");
    }
}

SurveyQuestionForm.editQuestionOption = function()
{
    var uid = ( SurveyQuestionForm.getQuestionOptionGrid().select().data() ) ? SurveyQuestionForm.getQuestionOptionGrid().select().data().uid : null;
    if( uid )
    {
        var selected = SurveyQuestionForm.getQuestionOptionGrid().dataSource.getByUid( uid );
        _option_id = selected.id;

        SurveyQuestionForm.showQuestionOptionForm();
    }
}

SurveyQuestionForm.addGridListeners = function()
{
    $( '#questionOptionGrid table' ).dblclick( SurveyQuestionForm.editQuestionOption );
    $( '#addOptionButton' ).click( SurveyQuestionForm.addQuestionOption );
    $( '#editOptionButton' ).click( SurveyQuestionForm.editQuestionOption );
    $( '#deleteOptionButton' ).click( SurveyQuestionForm.deleteQuestionOptions );
}

SurveyQuestionForm.setSelected = function( selectedRows )
{
    if( selectedRows.length == 1 )
    {
        $( '#editOptionButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#editOptionButton' ).addClass( 'k-state-disabled' );
    }

    if( selectedRows.length > 0 )
    {
        $( '#deleteOptionButton' ).removeClass( 'k-state-disabled' );
    }
    else
    {
        $( '#deleteOptionButton' ).addClass( 'k-state-disabled' );
    }
}

SurveyQuestionForm.deleteQuestionOptions = function()
{
    var ids = [];
    var selected = SurveyQuestionForm.getQuestionOptionGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( SurveyQuestionForm.getQuestionOptionGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteQuestionOptions", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            SurveyQuestionForm.filterOptionGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected options?");
}

SurveyQuestionForm.getQuestionOptionGrid = function()
{
    return $( '#questionOptionGrid' ).data( 'kendoGrid' );
}

SurveyQuestionForm.filterOptionGrid = function()
{
    SurveyQuestionForm.getQuestionOptionGrid().dataSource.filter({});
}

/*** end code list ***/

SurveyQuestionForm.showQuestionOptionForm = function(){
    $("#questionOptionFormContainer").data("kendoWindow").center();
    $("#questionOptionFormContainer").data("kendoWindow").open();
    $("#questionOptionFormContainer").load( "/member/getQuestionOptionForm");
}

SurveyQuestionForm.refreshOptionList = function() {
    $( "#questionOptionFormContainer" ).data("kendoWindow").close();
    SurveyQuestionForm.filterOptionGrid();
}

$( document ).ready( function()
{
    SurveyQuestionForm.loadViewModel();
    SurveyQuestionForm.addListeners();

    SurveyQuestionForm.notifier = Utils.notifier();
    SurveyQuestionForm.notifier.status( SurveyQuestionForm.status() );
});