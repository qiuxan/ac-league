var QuestionOptionForm = {
    viewModel : null,
    notifier: null
}

QuestionOptionForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _option_id,
            question_id: '',
            option_en: '',
            option_cn: '',
            option_tr: '',
            priority: 0,
            load: function( onComplete )
            {
                var self = this;

                if( _option_id )
                {
                    $.get( '/member/getQuestionOption', { id : _option_id }, function( option )
                    {
                        for( var key in option )
                        {
                            self.set( key, option[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                        QuestionOptionForm.addKendoElements();
                    });
                }
                else
                {
                    self.set( 'question_id', _question_id );
                    QuestionOptionForm.addKendoElements();
                }
            },
            isNew: function()
            {
                return this.get( 'id' ) == 0;
            }
        });

    return viewModel;
}

QuestionOptionForm.loadViewModel = function()
{
    QuestionOptionForm.viewModel = QuestionOptionForm.getViewModel();
    kendo.bind( $( '#questionOptionFormDiv' ), QuestionOptionForm.viewModel );
    QuestionOptionForm.viewModel.load();
}

QuestionOptionForm.addListeners = function()
{
    $( "#cancelOptionButton" ).click( SurveyQuestionForm.refreshOptionList );

    $( "#saveOptionButton" ).click( function()
    {
        QuestionOptionForm.validateForm( false );
    });

    $( "#doneOptionButton" ).click( function()
    {
        QuestionOptionForm.validateForm( true );
    });
}

QuestionOptionForm.status = function()
{
    return $( "span.status" );
}

QuestionOptionForm.disableSaveButtons = function()
{
    $( "#saveOptionButton" ).prop( 'disabled', true );
    $( "#doneOptionButton" ).prop( 'disabled', true );
}

QuestionOptionForm.enableSaveButtons = function()
{
    $( "#saveOptionButton" ).prop( 'disabled', false );
    $( "#doneOptionButton" ).prop( 'disabled', false );
}

QuestionOptionForm.validator = function()
{
    return $( "#questionOptionForm" ).kendoValidator().data( "kendoValidator" );
}

QuestionOptionForm.validateForm = function( returnToList )
{
    if( QuestionOptionForm.validator().validate() )
    {
        QuestionOptionForm.save( returnToList );
    }
    else
    {
        QuestionOptionForm.notifier.notifyError( 'Please complete all required fields.' );
        QuestionOptionForm.enableSaveButtons();
    }
}

QuestionOptionForm.save = function( returnToList, onComplete )
{
    QuestionOptionForm.notifier.notifyProgress( 'Saving Question Option...' );
    $.post( "/member/saveQuestionOption", $( "#questionOptionForm" ).serialize(), function( response )
    {
        response = JSON.parse(response);
        if( parseInt(response.option_id) > 0 )
        {
            if( _option_id == 0 )
            {
                _option_id = response.option_id;
            }

            QuestionOptionForm.notifier.notifyComplete( 'Question Option Saved' );
            QuestionOptionForm.viewModel.set( 'id', response.option_id );

            if( returnToList )
            {
                SurveyQuestionForm.refreshOptionList();
            }
            else
            {
                QuestionOptionForm.viewModel.load( onComplete );
            }
        }
        else
        {
            QuestionOptionForm.notifier.notifyError( 'Question option could not be saved' );
        }
    });
}

QuestionOptionForm.addKendoElements = function() {
}

$( document ).ready( function()
{
    QuestionOptionForm.loadViewModel();
    QuestionOptionForm.addListeners();

    QuestionOptionForm.notifier = Utils.notifier();
    QuestionOptionForm.notifier.status( QuestionOptionForm.status() );
});