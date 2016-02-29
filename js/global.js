$(document).ready(function() {
    $(".navbar-fixed-top .nav a").on('click', function() {
        window.location.href = $(this).attr('href');
    });
});

function addAlert(message, classes, clear, dismiss, div) {
    dismiss = defaultFor(dismiss, true);
    div = defaultFor(div, "#alerts");

    var alert = "",
        alert_div = $(div);

    if(clear) {
        alert_div.html('');
    }

    alert = '<div class="alert alert-dismissible ' + classes + '" role="alert">';
    if(dismiss) {
        alert += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
    }

    switch(classes) {
        case 'alert-danger' : alert += '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ';
            break;

        case 'alert-success' : alert += '<i class="fa fa-check" aria-hidden="true"></i> ';
            break;

        case 'alert-warning' : alert += '<i class="fa fa-exclamation" aria-hidden="true"></i> ';
            break;

        case 'alert-info' : alert += '<i class="fa fa-cog" aria-hidden="true"></i> ';
            break;
    }

    alert += message + '</div>';

    alert_div.append(alert);
}

function defaultFor(arg, val) {
    return typeof arg !== 'undefined' ? arg : val;
}