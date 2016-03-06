<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
$message = (empty($user)) ? addAlert("You are not currently logged in with your Twitch account.  To do so, click on the Login button on the upper right.", "alert-danger", true) : '';
$commands = getCommands();
$regions = getRegions();

$nightbotSettings = array();

if(isset($_SESSION['username'])) {
    $nightbotSettings = grabNightbotSettings();
}

$nightbot = getAllFromTable('nightbot');
$clientId = $nightbot[0]['client_id'];
$redirectURI = "https://gotme.site-meute.com/api/v1/dashboard";

if(isset($_GET['nightbotConnect'])) {
    $message = addAlert("Nightbot is now connected to your account!", "alert-success", true);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Settings | Trahanqc's API</title>

        <?php include 'includes/header.php'; ?>

    </head>
    <body>
        <nav class="navbar navbar-fixed-top navbar-dark bg-inverse">
            <a class="navbar-brand" href="dashboard">Trahanqc's API</a>

            <ul class="nav nav-pills nav-right" role="tablist" data-toggle="pill">
                <?php if(!empty($user)) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?= $user[0]['twitchUsername']; ?></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="logout">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $twitchtv->authenticate() ?>" id="login_twitch">Login with Twitch</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div id="wrapper">
            <div id="sidebar">
                <?php include 'addon/main_menu.php'; ?>
            </div>

            <div id="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Settings</h1>

                            <ol class="breadcrumb">
                                <li>
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li class="active">
                                    <i class="fa fa-cogs"></i>  Settings
                                </li>
                            </ol>

                            <div id="messages"><?= $message; ?></div>

                            <?php if(!empty($user)) : ?>
                                <p>Here are the settings for all the commands that I provide.  However, you can still choose the commands you want to add into your bot.</p>

                                <div class="row">
                                    <div class="col-lg-3 fix-lineheight">
                                        League of Legends summoner for the commands:
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="summonerName" placeholder="Summoner name" value="<?= $user[0]['summonerName']; ?>">
                                            <small class="text-muted">Summoner name</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-1">
                                        <div class="form-group">
                                            <select id="region" class="c-select full-width">
                                                <?php foreach($regions as $val) : ?>
                                                    <option value="<?= $val['region']; ?>" <?= ($user[0]['region'] == $val['region']) ? 'selected' : '';?>><?= $val['region']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted">Region</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <select id="season" class="c-select full-width">
                                                <option value="SEASON2016" <?= ($user[0]['season'] == 'SEASON2016') ? 'selected' : ''; ?>>Season 2016</option>
                                                <option value="SEASON2015" <?= ($user[0]['season'] == 'SEASON2015') ? 'selected' : ''; ?>>Season 2015</option>
                                                <option value="SEASON2014" <?= ($user[0]['season'] == 'SEASON2014') ? 'selected' : ''; ?>>Season 2014</option>
                                            </select>
                                            <small class="text-muted">Season</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <select id="lang" class="c-select full-width">
                                                <option value="en" <?= ($user[0]['lang'] == 'en') ? 'selected' : ''; ?>>English</option>
                                                <option value="fr" <?= ($user[0]['lang'] == 'fr') ? 'selected' : ''; ?>>Fran&ccedil;ais</option>
                                            </select>
                                            <small class="text-muted">Commands language</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-1">
                                        <button class="btn btn-success full-width" id="changeSummonerName" data-message="Saving..."><i class="fa fa-save"></i>  Save</button>
                                    </div>
                                </div>

                                <div class="row">
                                    <?php if(!empty($nightbotSettings) && $nightbotSettings != NULL && $nightbotSettings['nightbotToken'] != "") : ?>
                                        <div class="col-lg-3 fix-lineheight">Unlink the API to have access to your Nightbot</div>
                                        <div class="col-lg-2">
                                            <a class="btn btn-danger full-width" href="#" id="unlink-Nightbot">Unlink Nightbot</a>
                                        </div>
                                    <?php else : ?>
                                        <div class="col-lg-3 fix-lineheight">Login with Nightbot to add commands automatically</div>
                                        <div class="col-lg-2">
                                            <a class="btn btn-info full-width" href="https://api.nightbot.tv/oauth2/authorize?response_type=code&client_id=<?= $clientId; ?>&redirect_uri=<?= $redirectURI; ?>&scope=commands">Log in with Nightbot</a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="blank"></div>
                                <div class="blank"></div>

                                <p><strong>You will be able to change these settings very soon!</strong></p>
                                <table class="table table-hover table-striped" id="commands-table">
                                    <thead>
                                        <tr>
                                            <th>Command Name</th>
                                            <th>Response</th>
                                            <th>Available settings</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($commands as $val) :?>
                                            <tr>
                                                <td><?= $val['name']; ?></td>
                                                <td>
                                                    <textarea class="form-control" id="command-<?= $val['name']; ?>" disabled><?= $val['response']; ?></textarea>
                                                </td>
                                                <td></td>
                                                <td>
                                                    <button class="btn btn-success saveCommand" data-command="<?= $val['name']; ?>" disabled><i class="fa fa-save"></i>  Save</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <div class="blank"></div>
                            <?php else: ?>
                                <p>There is no data to display since you are not connected to your account.</p>

                                <?php for($x = 0 ; $x < 19 ; $x++) : ?>
                                    <div class="blank"></div>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

        <script>
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
            });
        </script>

    </body>
</html>
