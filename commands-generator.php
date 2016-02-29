<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();

$bots = array(
    1 => "Nigthbot",
    2 => "DeepBot",
    3 => "hnlBot"
);

$userAccess = array(
    1 => "Everybody",
    2 => "Regular",
    3 => "Subscriber",
    4 => "Mod",
    5 => "Owner"
);

$commands = getCommands("name");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Commands generator | Trahanqc's API</title>

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
                            <h1 class="page-header">Commands generator</h1>

                            <ol class="breadcrumb">
                                <li class="active">
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <i class="fa fa-code"></i>  Commands generator
                                </li>
                            </ol>

                            <p>The command generator allow you to generate any command from the <a href="commands-list">command list</a> easily.  You will need to add the command in the proper bot application.</p>
                            <p>You will need to generate your <a href="https://developer.riotgames.com/api/methods#!/1061/3663">League of Legends ID</a> in order to use any of those commands (won't be necessary in a near futur).</p>

                            <div class="row">
                                <div class="col-lg-2 fix-lineheight">
                                    Find your League of Legends ID :
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control col-lg-4" id="summonerNameId" placeholder="Summoner name">
                                    </div>
                                </div>

                                <div class="col-lg-1">
                                    <select class="form-control col-lg-3" id="region" disabled>
                                        <option value="na">NA</option>
                                    </select>
                                </div>

                                <div class="col-lg-2">
                                    <button class="btn btn-default col-lg-2 full-width" id="generateId">Generate ID</button>
                                </div>

                                <div class="col-lg-5 fix-lineheight">
                                    <div id="result-summonerId"></div>
                                </div>
                            </div>

                            <div class="blank"></div>

                            <table class="table" id="commands-generator">
                                <thead>
                                    <tr>
                                        <th>Command name</th>
                                        <th>Command</th>
                                        <th>Channel</th>
                                        <th>LoL ID</th>
                                        <th>Bot</th>
                                        <th>User Access</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">!</div>
                                                    <input type="text" class="form-control" id="command_name">
                                                </div>
                                            </div>

                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <select id="command" class="form-control">
                                                    <?php foreach($commands as $id => $c) : ?>
                                                        <option value="<?= $c['called']; ?>" data-addonNb="<?= $c['addonNb']; ?>" data-addonDb="<?= $c['addonDb']; ?>" data-addonHb="<?= $c['addonHb']; ?>"><?= $c['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="channel_name">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="lol_id">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <select id="bot_name" class="form-control">
                                                    <?php foreach($bots as $id => $name) : ?>
                                                        <option value="<?= $id; ?>"><?= $name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <select id="user_access" class="form-control">
                                                    <?php foreach($userAccess as $id => $name) : ?>
                                                        <option value="<?= $id; ?>"><?= $name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <button id="generate_command" class="btn btn-default">Generate</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div id="results" hidden>
                                <div id="result-warning"></div>
                                <div id="chat-title" class="command-title">
                                    <h4>Copy paste the following command in your chat:</h4>
                                </div>
                                <div id="chat-command" class="command-code"></div>
                                <div id="backend-title" class="command-title">
                                    <h4>Or put the following command in <span id="backend-title-bot"></span> backend: </h4>
                                </div>
                                <div id="backend-command" class="command-code"></div>
                            </div>

                            <?php for($x = 0 ; $x < 14 ; $x++) : ?>
                                <div class="blank"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
        <script src="js/generateCommand.js"></script>

    </body>
</html>
