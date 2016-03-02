$(document).ready(function() {
    var btnAddMessage = $("#btn-add-message");

    $("time.timeago").timeago();

    btnAddMessage.on('click', function() {
        window.location.href = "add-message";
    });

    $("#select-category").on('change', function() {
        var $this = $(this),
            id = $this.val();

        if(id === "0") id = "all";

        $.ajax({
            type: "POST",
            url: "../ajax/settings.php",
            dateType: "html",
            data: { action: "fetchMessages", id: id },
            success: function(data) {
                $("#results").html('').html(data);

                $("#results").find('time.timeago').timeago();
            }
        });
    });
});