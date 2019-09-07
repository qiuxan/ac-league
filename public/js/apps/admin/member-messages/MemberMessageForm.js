var MessageForm = {
    viewModel : null,
    notifier: null
}

MessageForm.getViewModel = function()
{
    //Define the viewModel
    var viewModel = kendo.observable(
        {
            id: _message_id,
            status: '',
            title: '',
            detail: '',
            due_date: '',
            assignee_id: 0,
            deleted: 0,
            load: function( onComplete )
            {
                var self = this;

                if( _message_id )
                {
                    $.get( '/admin/getMemberMessage', { id : _message_id }, function( task )
                    {
                        for( var key in task )
                        {
                            self.set( key, task[key] );
                        }

                        if( onComplete != undefined )
                        {
                            onComplete();
                        }
                    });
                }

            }
        });

    return viewModel;
}

MessageForm.loadViewModel = function()
{
    MessageForm.viewModel = MessageForm.getViewModel();
    kendo.bind( $( '#MessageForm' ), MessageForm.viewModel );
    MessageForm.viewModel.load();
}

MessageForm.addListeners = function(){
    $( "#doneButtonMessageForm" ).click( function()
    {
        $( "#MemberMessageDetailsContainer" ).data("kendoWindow").close();        
        MessageList.filterGrid();
    });    
}
$( document ).ready( function()
{
    MessageForm.loadViewModel();
    MessageForm.addListeners();
});