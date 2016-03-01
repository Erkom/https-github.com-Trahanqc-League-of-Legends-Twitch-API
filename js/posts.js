$(document).ready(function() {
    $("time.timeago").timeago();

    $("#add-reply").on('click', function() {
        $("#edit-reply-container").hide();
        $("#edit-post-container").hide();

        $("#add-reply-container").slideDown("fast", function() {
            $(this).show();
            $("#add-reply-input").focus();
        });
    });

    $("#close-reply").on('click', function() {
        $("#add-reply-container").slideUp("fast", function() {
            $(this).hide();
        });
    });

    $("#close-edit-reply").on('click', function() {
        $("#edit-reply-container").slideUp("fast", function() {
            $(this).hide();
        });
    });

    $("#close-edit-post").on('click', function() {
        $("#edit-post-container").slideUp("fast", function() {
            $(this).hide();
        });
    });

    $(".modif-comment").on('click', function() {
        var $this = $(this),
            commentId = $this.attr('data-id'),
            message = $("#message-" + commentId).text();

        $("#add-reply-container").hide();
        $("#edit-post-container").hide();

        $("#edit-reply-input").text(message);
        $("#edit-reply-btn").attr('data-id', commentId);

        $("#edit-reply-container").slideDown("fast", function() {
            $(this).show();
            $("#edit-reply-input").focus();
        });
    });

    $(".delete-comment").on('click', function() {
        var $this = $(this),
            commentId = $this.attr('data-id');

        $.ajax({
            type: "POST",
            url: "../ajax/settings.php",
            dataType: "html",
            data: { action : "deleteComment", id: commentId },
            success: function() {
                $("#comment-" + commentId).fadeOut('fast', function() {
                    $(this).remove();
                });
            }
        });
    });

    $(".edit-post").on('click', function() {
        var $this = $(this),
            postId = $this.attr('data-id'),
            title = $(".post-title h3 span").html(),
            message = $("#post-" + postId).text();

        $("#add-reply-container").hide();
        $("#edit-reply-container").hide();

        $("#edit-post-title").val(title);
        $("#edit-post-message").text(message);

        $("#edit-post-container").slideDown("fast", function() {
            $(this).show();
            $("#edit-post-message").focus();
        });
    });

    $("#edit-reply-btn").on('click', function() {
        var $this = $(this),
            commentId = $this.attr('data-id'),
            message = $("#edit-reply-input");

        console.log(commentId, message.val());

        if(checkEditPost("", message)) {
            $.ajax({
                type: "POST",
                url: "../ajax/settings.php",
                dataType: "html",
                data: { action : "updateComment", id: commentId, message: message.val() },
                success: function(data) {
                    if(data === "1") {
                        location.reload();
                    }
                    else if(data === "2") {
                        addAlert("This post is currently locked.  Therefore, no edits are allowed. <span class='alert-span'>10</span>", "alert-info", true, true, "#alerts", 10);
                    }
                    else {
                        addAlert("You do not meet the requirements to edit this comment. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                    }
                }
            });
        }
    });

    $("#edit-post-btn").on('click', function() {
        var $this = $(this),
            postId = $this.attr('data-id'),
            title = $("#edit-post-title"),
            message = $("#edit-post-message");

        if(checkEditPost(title, message)) {
            $.ajax({
                type: "POST",
                url: "../ajax/settings.php",
                dataType: "html",
                data: { action : "updatePost", id: postId, title: title.val(), message: message.val() },
                success: function(data) {
                    if(data === "1") {
                        location.reload();
                    }
                    else if(data === "2") {
                        addAlert("This post is currently locked.  Therefore, no edits are allowed. <span class='alert-span'>10</span>", "alert-info", true, true, "#alerts", 10);
                    }
                    else {
                        addAlert("You do not meet the requirements to edit this post. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                    }
                }
            });
        }
    });

    $(".delete-post").on('click', function() {
        var $this = $(this),
            postId = $this.attr('data-id');

        $.ajax({
            type: "POST",
            url: "../ajax/settings.php",
            dataType: "html",
            data: { action : "deletePost", id: postId },
            success: function() {
                window.location.href = "http://gotme.site-meute.com/api/v1/support";
            }
        });
    });

    $(".lock-post").on('click', function() {
        var $this = $(this),
            postId = $this.attr('data-id'),
            state = $this.attr('data-locked');

        $.ajax({
            type: "POST",
            url: "../ajax/settings.php",
            dataType: "html",
            data: { action : "lockPost", id: postId, state: state },
            success: function(data) {
                if(data === "1") {
                    addAlert("This post is now locked and no further replies are allowed. <span class='alert-span'>10</span>", "alert-locked", true, true, "#alerts", 10);
                    $this.find('i').removeClass('fa-lock');
                    $this.find('i').addClass('fa-unlock');
                    $this.attr('data-locked', "1");
                }
                else if(data === "2") {
                    addAlert("This post in now unlocked and replies are allowed. <span class='alert-span'>10</span>", "alert-unlocked", true, true, "#alerts", 10);
                    $this.find('i').removeClass('fa-unlock');
                    $this.find('i').addClass('fa-lock');
                    $this.attr('data-locked', "0");
                }
                else {
                    addAlert("You do not meet the requirements to lock a post. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);
                }
            }
        });
    });

    $("#add-reply-btn").on('click', function() {
        var $this = $(this),
            postId = $this.attr('data-id'),
            message = $("#add-reply-input");

        if($.trim(message.val()) !== "") {
            if(message.hasClass(".form-control-danger")) {
                message.closest(".form-group").removeClass("has-danger");
                message.removeClass("form-control-danger");
            }

            $.ajax({
                type: "POST",
                url: "../ajax/settings.php",
                dataType: "html",
                data: { action : "addComment", id: postId, message: message.val() },
                success: function(data) {
                    if(data === "1") {
                        location.reload();
                    }
                    else if(data === "2") {
                        $("#add-reply-container").slideUp("fast", function() {
                            $(this).hide();
                        });
                        addAlert("This post is now locked and no further replies are allowed. <span class='alert-span'>10</span>", "alert-danger", true, true, "#alerts", 10);

                        $("#add-reply").attr('disabled', 'disabled');
                    }
                }
            });
        }
        else {
            message.closest(".form-group").addClass("has-danger");
            message.addClass("form-control-danger");
        }
    });
});

function checkEditPost(title, message) {
    var correct = true;

    if(title !== "") {
        if($.trim(title.val()) === "") {
            title.addClass('form-control-danger');
            title.closest('.form-group').addClass('has-danger');
            correct = false;
        }
        else if(title.hasClass('form-control-danger')) {
            title.removeClass('form-control-danger');
            title.closest('.form-group').removeClass('has-danger');
        }
    }

    if($.trim(message.val()) === "") {
        message.addClass('form-control-danger');
        message.closest('.form-group').addClass('has-danger');
        correct = false;
    }
    else if(message.hasClass('form-control-danger')) {
        message.removeClass('form-control-danger');
        message.closest('.form-group').removeClass('has-danger');
    }

    return correct;
}