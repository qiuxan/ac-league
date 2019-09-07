$( document ).ready( function()
{    
    $("#submit_button").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/contact",
            data: $("#message_form").serialize(),
            success: function(response) {
                response = JSON.parse(response);
                $('.panel.panel-default')
                .attr('class', 'alert alert-success')
                .html(response.message);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.responseText);
            }
        });
    });
});