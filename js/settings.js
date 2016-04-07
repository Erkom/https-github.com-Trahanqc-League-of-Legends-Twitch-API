$(document).ready(function() {
    $(".navbar-fixed-top .nav a").on('click', function() {
        window.location.href = $(this).attr('href');
    });

    $("#changeSummonerName").on('click', function() {
        var summonerName = $("#summonerName"),
            region = $("#region"),
            season = $("#season"),
            lang = $("#lang");

        if($.trim(summonerName.val()) !== "") {
            if(summonerName.closest('.form-group').has('has-danger')) {
                summonerName.closest('.form-group').removeClass('has-danger');
            }

            $.ajax({
                type: "POST",
                url: "../ajax/summonerID.php",
                dataType: "html",
                data: { summonerName: summonerName.val(), region: region.val() },
                success: function(data) {
                    if(data.length == 0 || data.length > 10) {
                        addAlert("The summoner name does not exist.", "alert-danger", true, true, "#messages", 3, "#changeSummonerName");
                    }
                    else {
                        $.ajax({
                            type: "POST",
                            url: "../ajax/settings.php",
                            dataType: "html",
                            data: { action: "updateSummonerName", summonerName: summonerName.val(), region: region.val(), summonerId: data, season: season.val(), lang: lang.val() },
                            success: function(data) {
                                if(data === "1") {
                                    addAlert("The commands has been updated successfully! <span class='alert-span'>3</span>", "alert-success", true, true, "#messages", 3, "#changeSummonerName");
                                }
                                else {
                                    addAlert(data, "alert-danger", true, true, "#messages");
                                }
                            }
                        });
                    }
                }
            });


        }
        else {
            summonerName.closest('.form-group').addClass('has-danger');
        }
    });

    $("#unlink-Nightbot").on('click', function(e) {
        $.ajax({
            type: "POST",
            url: "../ajax/settings.php",
            dataType: "html",
            data: { action: "unlinkNightbot" },
            success: function(data) {
                if(data === "1") {
                    location.reload();
                }
                else {
                    addAlert("Error: unable to remove Nightbot from your channel at the moment.  Try again later <span class='alert-span'>5</span>", "alert-danger", true, true, "#messages", 5, "#unlink-Nightbot");
                }
            }
        });
    });

    $(".saveCommand").on('click', function() {
        var $this = $(this),
            command = $this.attr('data-command'),
            response = $("#command-" + command);

        if($.trim(response.val()) !== "") {
            if($this.hasClass('form-control-danger')) {
                $this.removeClass('form-control-danger');
                $this.closest('.form-control').removeClass('has-danger');
            }

            $.ajax({
                type: "POST",
                url: "../ajax/settings.php",
                dataType: "html",
                data: { action: "saveCommand", commandName: command, response: response.val() },
                success: function(data) {
                    $this.attr('disabled', 'disabled');

                    if(data === "1") {
                        $this.html('Saved !');
                    }
                    else {
                        $this.html('Error');
                        $this.addClass('btn-danger');
                        $this.removeClass('btn-success');
                    }

                    setTimeout(function() {
                        $this.removeAttr('disabled');
                        $this.html('<i class="fa fa-save"></i>  Save');

                        if($this.hasClass('btn-danger')) {
                            $this.removeClass('btn-danger');
                            $this.addClass('btn-success');
                        }
                    }, 1000 * 2);
                }
            });
        }
        else {
            $this.addClass('form-control-danger');
            $this.closest('.form-control').addClass('has-danger');
        }
    });

    $(".removeCommand").on('click', function() {
        var $this = $(this),
            command = $this.attr('data-command'),
            defaultResponse = $this.attr('data-default');

        $.ajax({
            type: "POST",
            url: "../ajax/settings.php",
            dataType: "html",
            data: { action: "removeCommand", commandName: command },
            success: function(data) {
                $this.attr('disabled', 'disabled');

                if(data === "1") {
                    $this.html('Done !');
                    $("#command-" + command).val(defaultResponse);
                }
                else {
                    $this.html('Error');
                    $this.addClass('btn-danger');
                    $this.removeClass('btn-warning');
                }

                setTimeout(function() {
                    $this.removeAttr('disabled');
                    $this.html('<i class="fa fa-close"></i>  Default');

                    if($this.hasClass('btn-danger')) {
                        $this.removeClass('btn-danger');
                        $this.addClass('btn-warning');
                    }
                }, 1000 * 2);
            }
        });
    });
});