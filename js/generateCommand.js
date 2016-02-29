$(document).ready(function() {
    $("#generate_command").on('click', function() {
        var commandName = $("#command_name"),
            command = $("#command"),
            channel = $("#channel_name"),
            lolId = $("#lol_id"),
            botName = $("#bot_name"),
            userAccess = $("#user_access");

        $("#result-warning").html('');

        if(checkFields(commandName, channel, lolId)) {
            var commandAddon = "",
                commandChat = "",
                commandSupp = "",
                commandBackend = "",
                backendTitle = "",
                backendTitleContainer = $("#backend-title-bot"),
                commandChatContainer = $("#chat-command"),
                commandBackendContainer = $("#backend-command");

            $("#results").removeAttr('hidden');

            if(botName.val() == 1) {
                commandSupp = command.find('option:selected').attr('data-addonNb');

                switch(userAccess.val()) {
                    case "2" : commandAddon = " -ul=reg"; break;
                    case "3" : commandAddon = " -ul=mod"; break;
                    case "4" : commandAddon = " -ul=subscriber"; break;
                    case "5" : commandAddon = " -ul=owner"; break;
                    default: commandAddon = "";
                }

                commandChat = "!addcom" + commandAddon + " !" + commandName.val() + " $(customapi http://gotme.site-meute.com/query.php?action=" + command.val() + "&id=" + lolId.val() + "" + commandSupp + "&channel=" + channel.val() + "&user=$(user)&bot=Nightbot&lang=en)";
                commandBackend = "$(customapi http://gotme.site-meute.com/query.php?action=" + command.val() + "&id=" + lolId.val() + "" + commandSupp + "&channel=" + channel.val() + "&user=$(user)&bot=Nightbot&lang=en)";
                backendTitle = "Nightbot's";

                backendTitleContainer.html('').html(backendTitle);
                commandChatContainer.html('').html(commandChat);
                commandBackendContainer.html('').html(commandBackend);
            }
            else if(botName.val() == 2) {
                commandSupp = command.find('option:selected').attr('data-addonDb');

                switch(userAccess.val()) {
                    case "2" :
                        commandAddon = " %1";
                        addAlert("<strong>Watch out!</strong> Deepbot doesn't have any configuration for <strong>Regular</strong> viewers", "alert-warning", true, true, "#result-warning");
                        break;
                    case "3" : commandAddon = " %8"; break;
                    case "4" : commandAddon = " %2"; break;
                    case "5" :
                        commandAddon = " %8";
                        addAlert("<strong>Watch out!</strong> Deepbot doesn't have any configuration for <strong>Owners</strong>", "alert-warning", true, true, "#result-warning");
                        break;
                    default: commandAddon = " %1";
                }

                commandChat = "!addcmd !" + commandName.val() + "" + commandAddon + " @customapi@[http://gotme.site-meute.com/query.php?action=" + command.val() + "&id=" + lolId.val() + "" + commandSupp + "&channel=" + channel.val() + "&user=@user@&bot=Deepbot]";
                commandBackend = "@customapi@[http://gotme.site-meute.com/query.php?action=" + command.val() + "&id=" + lolId.val() + "" + commandSupp + "&channel=" + channel.val() + "&user=@user@&bot=Deepbot]";
                backendTitle = "Deepbot's";

                backendTitleContainer.html('').html(backendTitle);
                commandChatContainer.html('').html(commandChat);
                commandBackendContainer.html('').html(commandBackend);
            }
            else {
                commandSupp = command.find('option:selected').attr('data-addonHb');
                addAlert("<strong>Watch out!</strong> I currently have no support for user access!", "alert-warning", true, true, "#result-warning");

                commandChat = "Please use the backend method to add a command while using the hnlBot.";
                commandBackend = "%CUSTOMAPI http://gotme.site-meute.com/query.php?action=" + command.val() + "&id=" + lolId.val() + "" + commandSupp + "&channel=" + channel.val() + "&user=%SENDERNAME%&bot=hnlbot%";
                backendTitle = "hnlBot's";

                backendTitleContainer.html('').html(backendTitle);
                commandChatContainer.html('').html(commandChat);
                commandBackendContainer.html('').html(commandBackend);
            }
        }
    });

    $("#generateId").on('click', function() {
        var summonerName = $("#summonerNameId"),
            region = $("#region");

        if($.trim(summonerName.val()) !== "") {
            if(summonerName.closest('.form-group').has('has-danger')) {
                summonerName.closest('.form-group').removeClass('has-danger');
            }

            $.ajax({
                type: "POST",
                url: "../ajax/summonerID.php",
                dataType: "html",
                data: { summonerName: summonerName.val(), details: true, region: region.val() },
                success: function(data) {
                    $("#result-summonerId").html('').html(data);
                }
            });
        }
        else {
            summonerName.closest('.form-group').addClass('has-danger');
        }
    });
});

function checkFields(commandName, channel, lolId) {
    var correct = true;

    if($.trim(commandName.val()) === "") {
        commandName.closest('.form-group').addClass('has-danger');
        correct = false;
    }
    else {
        if(commandName.closest('.form-group').has('has-danger')) {
            commandName.closest('.form-group').removeClass('has-danger');
        }
    }

    if($.trim(channel.val()) === "") {
        channel.closest('.form-group').addClass('has-danger');
        correct = false;
    }
    else {
        if(channel.closest('.form-group').has('has-danger')) {
            channel.closest('.form-group').removeClass('has-danger');
        }
    }

    if($.trim(lolId.val()) === "") {
        lolId.closest('.form-group').addClass('has-danger');
        correct = false;
    }
    else {
        if(lolId.closest('.form-group').has('has-danger')) {
            lolId.closest('.form-group').removeClass('has-danger');
        }
    }

    return correct;
}