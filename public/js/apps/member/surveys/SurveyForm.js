var SurveyForm = {
    viewModel : null,
    notifier: null
}

SurveyForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _survey_id,
            member_id: '',
            title_en: '',
            title_cn: '',
            title_tr: '',
            description_en: '',
            description_cn: '',
            description_tr: '',
            load: function( onComplete )
            {
                var self = this;

                if( _survey_id )
                {
                    $.get( '/member/getSurvey', { id : _survey_id }, function( survey )
                    {
                        for( var key in survey )
                        {
                            self.set( key, survey[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        SurveyForm.addKendoElements();
                    });
                }
                else
                {
                    SurveyForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            },
            breadCrumbName: function()
            {
                return ( this.get( 'id' ) != 0 ) ? this.get( 'name_en' ) : 'New Survey';
            }
        });

    return viewModel;
}

SurveyForm.loadViewModel = function()
{
    SurveyForm.viewModel = SurveyForm.getViewModel();
    kendo.bind( $( '#surveyFormDiv' ), SurveyForm.viewModel );
    SurveyForm.viewModel.load();
}

SurveyForm.addListeners = function()
{
    $( "#cancelButton" ).click( SurveyForm.showSurveyList );

    $( "#saveButton" ).click( function()
    {
        SurveyForm.validateForm( false );
    });

    $( "#doneButton" ).click( function()
    {
        SurveyForm.validateForm( true );
    });
}

SurveyForm.validator = function()
{
    return $( "#surveyForm" ).kendoValidator().data( "kendoValidator" );
}

SurveyForm.status = function()
{
    return $( "span.status" );
}

SurveyForm.disableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', true );
    $( "#doneButton" ).prop( 'disabled', true );
}

SurveyForm.enableSaveButtons = function()
{
    $( "#saveButton" ).prop( 'disabled', false );
    $( "#doneButton" ).prop( 'disabled', false );
}

SurveyForm.validateForm = function( returnToList )
{
    if( SurveyForm.validator().validate() )
    {
        SurveyForm.save( returnToList );
    }
    else
    {
        SurveyForm.notifier.notifyError( 'Please complete all required fields.' );
        SurveyForm.enableSaveButtons();
    }
}

SurveyForm.save = function( returnToList, onComplete )
{
    SurveyForm.notifier.notifyProgress( 'Saving Survey...' );
    $.post( "/member/saveSurvey", $( "#surveyForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.survey_id) > 0 )
        {
            if( _survey_id == 0 )
            {
                _survey_id = response.survey_id;
            }

            SurveyForm.notifier.notifyComplete( 'Survey Saved' );
            SurveyForm.viewModel.set( 'id', response.survey_id );

            if( returnToList )
            {
                SurveyForm.showSurveyList();
            }
            else
            {
                SurveyForm.viewModel.load( onComplete );
            }
        }
        else
        {
            SurveyForm.notifier.notifyError( 'Survey could not be saved' );
        }
    });
}

SurveyForm.addKendoElements = function() {
    $( '#tabs' ).kendoTabStrip();

    if( !SurveyForm.editor1 )
    {
        SurveyForm.editor1 = $( 'textarea#description_en' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !SurveyForm.editor2 )
    {
        SurveyForm.editor2 = $( 'textarea#description_cn' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    if( !SurveyForm.editor3 )
    {
        SurveyForm.editor3 = $( 'textarea#description_tr' ).ckeditor({
            customConfig: '/js/ckeditor/config.js'
        }).editor;
    }

    SurveyForm.initQuestionGrid();
}

/*** start code list ***/

SurveyForm.initQuestionGrid = function()
{
    $("#surveyQuestionFormContainer").kendoWindow({
        actions: ["Close"],
        draggable: false,
        width: "600px",
        height: "350px",
        title: "Question Detail",
        resizable: true,
        modal: true,
        visible: false,
        close: SurveyForm.filterGrid
    });

    $( '#surveyQuestionGrid' ).kendoGrid(
        {
            toolbar: kendo.template( $( '#toolbarTemplate' ).html() ),
            dataSource: SurveyForm.getDataSource(),
            height: $( window ).height() - 200,
            sortable: true,
            selectable: 'multiple',
            columns: [
                { field: 'order_number', title: '#' },
                { field: 'type', title: 'Type', values: [
                    { text: "Open Question", value: 1 },
                    { text: "Multiple Choices Question", value: 2 },
                    { text: "Scaling Question", value: 3 } ] },
                { field: 'question_en', title: 'Question' },
                { field: 'published', title: 'Published', values: [
                    { text: "Yes", value: 1 },
                    { text: "No", value: 0 } ] },
                { field: 'required', title: 'Required', values: [
                    { text: "Yes", value: 1 },
                    { text: "No", value: 0 } ] }],
            change: function( e )
            {
                SurveyForm.setSelected( this.select() );
            },
            dataBound: function( e )
            {
                SurveyForm.setSelected( this.select() );
            }
        });

    $( '#surveyQuestionGrid' ).data( 'kendoGrid' ).table.kendoSortable({
        filter: ">tbody >tr",
        hint: $.noop,
        cursor: "move",
        placeholder: function(element) {
            return element.clone().addClass("k-state-hover").css("opacity", 0.65);
        },
        container: "#surveyQuestionGrid tbody",
        change: function(e) {
            var skip = $( '#surveyQuestionGrid' ).data( 'kendoGrid' ).dataSource.skip(),
                oldIndex = e.oldIndex + skip,
                newIndex = e.newIndex + skip,
                data = $( '#surveyQuestionGrid' ).data( 'kendoGrid' ).dataSource.data(),
                dataItem = $( '#surveyQuestionGrid' ).data( 'kendoGrid' ).dataSource.getByUid(e.item.data("uid"));

            $( '#surveyQuestionGrid' ).data( 'kendoGrid' ).dataSource.remove(dataItem);
            $( '#surveyQuestionGrid' ).data( 'kendoGrid' ).dataSource.insert(newIndex, dataItem);

            // update slides priorities
            dataItems = $( '#surveyQuestionGrid' ).data( 'kendoGrid' ).dataSource.view();
            var question_priorities = [];
            for (i=0; i<dataItems.length; i++) {
                dataItems[i].set('priority', i+1);
                question_priority = {
                    "id": dataItems[i]['id'],
                    "priority": dataItems[i]['priority']
                };
                question_priorities.push(question_priority);
            }
            question_priorities = {
                question_priorities: question_priorities
            };
            SurveyForm.updatePriorities(question_priorities);
        }
    });

    SurveyForm.addGridListeners();
}

SurveyForm.getDataSource = function()
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
                    url: "/member/getSurveyQuestions",
                    dataType: 'json',
                    data:
                    {
                        survey_id: _survey_id
                    }
                }
            },
            schema:
            {
                model: SurveyForm.getModel()
            },
            sort: { field: 'priority', dir: 'asc' }
        });
}

SurveyForm.getModel = function()
{
    return kendo.data.Model.define(
        {
            id: 'id'
        });
}

SurveyForm.updatePriorities = function(question_priorities)
{
    question_priorities._token = $('[name="_token"]').val();
    $.post("updateQuestionPriorities", question_priorities, function(response){
        SurveyForm.filterGrid();
    });
}

SurveyForm.addSurveyQuestion = function()
{
    if(_survey_id != 0)
    {
        _question_id = 0;
        SurveyForm.showSurveyQuestionForm();
    }
    else
    {
        Utils.alert().show("Warning!", "Please save survey first, then add question for survey!");
    }
}

SurveyForm.editSurveyQuestion = function()
{
    var uid = ( SurveyForm.getSurveyQuestionGrid().select().data() ) ? SurveyForm.getSurveyQuestionGrid().select().data().uid : null;
    if( uid )
    {
        var selected = SurveyForm.getSurveyQuestionGrid().dataSource.getByUid( uid );
        _question_id = selected.id;

        SurveyForm.showSurveyQuestionForm();
    }
}

SurveyForm.addGridListeners = function()
{
    $( '#surveyQuestionGrid table' ).dblclick( SurveyForm.editSurveyQuestion );
    $( '#addButton' ).click( SurveyForm.addSurveyQuestion );
    $( '#editButton' ).click( SurveyForm.editSurveyQuestion );
    $( '#deleteButton' ).click( SurveyForm.deleteSurveyQuestions );
}

SurveyForm.setSelected = function( selectedRows )
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

SurveyForm.deleteSurveyQuestions = function()
{
    var ids = [];
    var selected = SurveyForm.getSurveyQuestionGrid().select();

    for( var i = 0; i < selected.length; i++ )
    {
        ids.push( SurveyForm.getSurveyQuestionGrid().dataItem( selected[i] )['id'] );
    }

    Utils.confirm().yesCallBack(function () {
        $.post("/member/deleteQuestions", {ids: ids, _token: $('[name="_token"]').val()}, function () {
            SurveyForm.filterGrid();
        });
    }).show('Confirm Delete', "Are you sure you want to delete the selected survey questions?");
}

SurveyForm.getSurveyQuestionGrid = function()
{
    return $( '#surveyQuestionGrid' ).data( 'kendoGrid' );
}

SurveyForm.filterGrid = function()
{
    SurveyForm.getSurveyQuestionGrid().dataSource.filter({});
}

/*** end code list ***/

SurveyForm.showSurveyQuestionForm = function(){
    $("#surveyQuestionFormContainer").data("kendoWindow").center();
    $("#surveyQuestionFormContainer").data("kendoWindow").open();
    $("#surveyQuestionFormContainer").load( "/member/getSurveyQuestionForm");
}

SurveyForm.refreshQuestionList = function() {
    $( "#surveyQuestionFormContainer" ).data("kendoWindow").close();
    SurveyForm.filterGrid();
}

$( document ).ready( function()
{
    SurveyForm.loadViewModel();
    SurveyForm.addListeners();

    SurveyForm.notifier = Utils.notifier();
    SurveyForm.notifier.status( SurveyForm.status() );
});