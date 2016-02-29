$(document).ready(function() {
    var btnAddMessage = $("#btn-add-message"),
        form = $("#form-add-message");

    btnAddMessage.on('click', function(e) {
        e.preventDefault();

        var title = $("#title"),
            category = $("#category"),
            message = $("#message");

        if(checkFields()) {
            form.attr('action', 'add-message');
            form.submit();
        }
    });
});

function checkFields() {
    var correct = true,
        fields = ["#title", "#message"];

    for(var x in fields) {
        var input = $(fields[x]);
        if($.trim(input.val()) === "") {
            input.closest('.form-group').addClass('has-danger');
            input.addClass('form-control-danger');
            correct = false;
        }
        else if(input.closest('.form-group').hasClass('has-danger')) {
            input.closest('.form-group').removeClass('has-danger');
            input.removeClass('form-control-danger');
        }
    }

    return correct;
}