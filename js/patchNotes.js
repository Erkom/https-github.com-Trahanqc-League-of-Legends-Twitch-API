$(document).ready(function() {
    $(".deletePatch").on('click', function() {
        var $this = $(this),
            id = $this.attr('data-id');

        $.ajax({
            type: "POST",
            url: "../ajax/settings.php",
            dataType: "html",
            data: { action: "deletePatch", id: id },
            success: function(data) {
                if(data === "1") {
                    $("#patch-" + id).fadeOut('500', function() {
                        $(this).remove();
                    });

                    addAlert("The Patch note has been removed successfully! <span class='alert-span'>3</span>", "alert-success", true, true, "#alerts", 3);
                }
                else if(data === "2") {
                    addAlert("You do not have the current access to execute this operation. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                }
                else {
                    addAlert("There was a problem while trying to delete the Patch notes.  Try again later. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                }
            }
        });
    });

    $("#add-patch-notes").on('click', function() {
        $("#edit-patch-container").hide();

        $("#add-patch-container").slideDown('fast', function() {
            $(this).show();
            $("#add-patch-title").focus();
        });
    });

    $("#close-add-patch").on('click', function() {
        $("#add-patch-container").slideUp('fast', function() {
            $(this).hide();
        });
    });

    $("#add-patch-btn").on('click', function() {
        var title = $("#add-patch-title"),
            version = $("#add-patch-version"),
            patchNote = $("#add-patch-input");

        if(checkFields(title, version, patchNote)) {
            $.ajax({
                type: "POST",
                url: "../ajax/settings.php",
                dataType: "html",
                data: { action: "addPatch", title: title.val(), version: version.val(), patchNotes: patchNote.val() },
                success: function(data) {
                    if(data === "1") {
                        location.reload();
                    }
                    else if(data === "2") {
                        addAlert("You do not have the current access to execute this operation. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                    }
                    else {
                        addAlert("There was a problem while trying to add the Patch notes.  Try again later. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                    }
                }
            });
        }
    });

    $(".editPatch").on('click', function() {
        var $this = $(this),
            id = $this.attr('data-id'),
            card = $("#patch-" + id),
            title = card.find('.card-title').text(),
            version = card.find('.patch-version').text(),
            patchNotes = card.find('.card-text').text();

        $("#edit-patch-title").val(title);
        $("#edit-patch-version").val(version);
        $("#edit-patch-input").text(patchNotes);

        $("#edit-reply-btn").attr('data-id', id);

        $("#add-patch-container").hide();

        $("#edit-patch-container").slideDown('fast', function() {
            $(this).show();
            $("#edit-patch-input").focus();
        });
    });

    $("#close-edit-patch").on('click', function() {
        $("#edit-patch-container").slideUp('fast', function() {
            $(this).hide();
        });
    });

    $("#edit-reply-btn").on('click', function() {
        var $this = $(this),
            id = $this.attr('data-id'),
            title = $("#edit-patch-title"),
            version = $("#edit-patch-version"),
            patchNote = $("#edit-patch-input");

        if(checkFields(title, version, patchNote)) {
            $.ajax({
                type: "POST",
                url: "../ajax/settings.php",
                dataType: "html",
                data: { action: "editPatch", id:id, title: title.val(), version: version.val(), patchNotes: patchNote.val() },
                success: function(data) {
                    if(data === "1") {
                        location.reload();
                    }
                    else if(data === "2") {
                        addAlert("You do not have the current access to execute this operation. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                    }
                    else {
                        addAlert("There was a problem while trying to edit the Patch notes.  Try again later. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                    }
                }
            });
        }
    });
});

function checkFields(title, version, patchNote) {
    var correct = true;

    if($.trim(title.val()) === "") {
        title.addClass('form-control-danger');
        title.closest('.form-group').addClass('has-danger');
        correct = false;
    }
    else if(title.hasClass('form-control-danger')) {
        title.removeClass('form-control-danger');
        title.closest('.form-group').removeClass('has-danger');
    }

    if($.trim(version.val()) === "") {
        version.addClass('form-control-danger');
        version.closest('.form-group').addClass('has-danger');
        correct = false;
    }
    else if(version.hasClass('form-control-danger')) {
        version.removeClass('form-control-danger');
        version.closest('.form-group').removeClass('has-danger');
    }

    if($.trim(patchNote.val()) === "") {
        patchNote.addClass('form-control-danger');
        patchNote.closest('.form-group').addClass('has-danger');
        correct = false;
    }
    else if(patchNote.hasClass('form-control-danger')) {
        patchNote.removeClass('form-control-danger');
        patchNote.closest('.form-group').removeClass('has-danger');
    }

    return correct;
}