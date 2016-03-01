$(document).ready(function() {
    $(".navbar-fixed-top .nav a").on('click', function() {
        window.location.href = $(this).attr('href');
    });
});

function addAlert(message, classes, clear, dismiss, div, timer, btn) {
    dismiss = defaultFor(dismiss, true);
    div = defaultFor(div, "#alerts");
    timer = defaultFor(timer, 0);
    btn = defaultFor(btn, "");

    var alert = "",
        alert_div = $(div);

    if(clear) {
        alert_div.html('');
    }

    classe = (classes == 'alert-locked' || classes == 'alert-unlocked') ? 'alert-info' : classes;
    alert = '<div class="alert alert-dismissible ' + classe + '" role="alert">';
    if(dismiss) {
        alert += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
    }

    switch(classes) {
        case 'alert-danger' : alert += '<span class="fa fa-exclamation-circle" aria-hidden="true"></span> ';
            break;

        case 'alert-success' : alert += '<span class="fa fa-check" aria-hidden="true"></span> ';
            break;

        case 'alert-warning' : alert += '<span class="fa fa-flag" aria-hidden="true"></span> ';
            break;

        case 'alert-info' : alert += '<span class="fa fa-cog" aria-hidden="true"></span> ';
            break;

        case 'alert-locked' : alert += '<span class="fa fa-lock" aria-hidden="true"></span> ';
            break;

        case 'alert-unlocked' : alert += '<span class="fa fa-unlock" aria-hidden="true"></span> ';
            break;
    }

    alert += message + '</div>';

    if(timer != 0) {
        var button = $(btn),
            lastText = button.html();

        if(btn !== "") {
            button.attr('disabled', 'disabled');
            button.html("").html("<i class='fa fa-refresh fa-spin'></i> " + button.attr('data-message'));
        }

        var timeout = setInterval(function() {
            if(timer == 0) {
                clearInterval(timeout);
                alert_div.slideUp(500, function() {
                    $(this).html('');
                    $(this).show();

                    if(btn !== "") {
                        button.removeAttr('disabled');
                        button.html("").html(lastText);
                    }
                });
            }
            else {
                alert_div.find('.alert-span').html(--timer);
            }
        }, 1000);
    }

    alert_div.append(alert);
}

function defaultFor(arg, val) {
    return typeof arg !== 'undefined' ? arg : val;
}