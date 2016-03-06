<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Install guide | Trahanqc's API</title>

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
                            <h1 class="page-header">Install guide</h1>

                            <ol class="breadcrumb">
                                <li>
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li class="active">
                                    <i class="fa fa-wrench"></i> Install guide
                                </li>
                            </ol>

                            <p>
                                Welcome to the install guide for the League of Legends commands.  There are currently 3 bots that are able to use the commands.<br>
                                <small>(However, all bots with custom API are supported but these are the 3 bots I tested)</small>
                            </p>

                            <ul>
                                <li>
                                    <a href="#nightbot">Nightbot</a>
                                </li>
                                <li>
                                    <a href="#deepbot">DeepBot</a>
                                </li>
                                <li>
                                    <a href="#hnlbot">hnlBot</a>
                                </li>
                            </ul>

                            <p>
                                To see a detailed list of all available commands, you can go on the <a href="commands-list">Commands list</a> page!  All of these commands are comming from the <a href="https://developer.riotgames.com/">official Riot API</a>. <br>
                                You can use the install notes below or go into the <a href="commands-generator">Commands generator</a> to create the commands you want.
                            </p>

                            <!--
                            *****************************
                            *****************************
                                    NIGHTBOT
                            *****************************
                            *****************************
                            -->
                            <h3 class="bot-name" id="nightbot">Nightbot</h3>
                            <p>Nightbot is one of the most popular bot for Twitch.  It is free to use and really easy to setup.  More information about Nightbot at : <a href="http://nightbot.tv">www.nightbot.tv</a> (I recommend to upgrade to the Beta v4 version since it's nearly ready to launch)</p>
                            <p>With Nightbot, there is 3 ways to add a command.  You can either add it directly into your Twitch chat :</p>
                            <p>
                                <code>!addcom !rank $(customapi http://gotme.site-meute.com/query.php?action=rank&id=<mark>YOURACCOUNTID</mark>&channel=<mark>YOURCHANNEL</mark>&user=$(user)&bot=Nightbot)</code>
                            </p>
                            <p>If you want to replace one of your command with another one, just use the following code into the chat :</p>
                            <p>
                                <code>!editcom !rank $(customapi http://gotme.site-meute.com/query.php?action=rank&id=<mark>YOURACCOUNTID</mark>&channel=<mark>YOURCHANNEL</mark>&user=$(user)&bot=Nightbot)</code>
                            </p>
                            <p>The second method to add a command with Nightbot is by going into the <a href="https://www.nightbot.tv/index">Nightbot panel</a> and adding a new command with the following response :</p>
                            <p>
                                <code>$(customapi http://gotme.site-meute.com/query.php?action=rank&id=<mark>YOURACCOUNTID</mark>&channel=<mark>YOURCHANNEL</mark>&user=$(user)&bot=Nightbot)</code>
                            </p>
                            <p>You need to change <mark>YOURACCOUNTID</mark> by the <a href="https://developer.riotgames.com/api/methods#!/1061/3663">id of your League of Legends's account</a> and <mark>YOURCHANNEL</mark> by your Twitch channel's name.</p>
                            <p>To have the command in english, add <code>&lang=en</code> after the parameter <code>&bot=Nightbot</code> like so : <code>...&user=$(user)&bot=Nightbot&lang=en)</code></p>

                            <p>
                                The third option would be to add the command directly into your Nightbot panel from within this API.  <strong>Note: This require you to connect to this API with your Twitch account and to allow Nightbot to access to your account on this API.</strong>  To do so, connect to the API, then head to the <a href="settings">Settings</a> and allow Nightbot to access your account.
                                After that, go into the <a href="commands-generator">Commands generator</a> and click the <code>Add into Nightbot</code> button!  It's really easy and the faster way because you won't make any copy pasta mistakes ;)
                            </p>

                            <p>See the screenshot for more options : </p>
                            <img src="../pictures/nightbot.PNG">

                            <!--
                            *****************************
                            *****************************
                                    DEEPBOT
                            *****************************
                            *****************************
                            -->
                            <h3 class="bot-name" id="deepbot">DeepBot</h3>
                            <p>DeepBot is also a popular bot for Twitch.  It has a lot of options, like a points system, easy to install and is super fast.  You can sign up for DeepBot here : <a href="http://deepbot.deep.sg">www.deepbot.deep.sg</a></p>
                            <p>To add a command with DeepBot you will need to go into the DeepBot program and enter the following command into the 'Message' field :</p>
                            <p>
                                <code>@customapi@[http://gotme.site-meute.com/query.php?action=stats&id=<mark>YOURACCOUNTID</mark>&champion=@target@&channel=<mark>YOURCHANNEL</mark>&user=@user@&bot=Deepbot]</code>
                            </p>
                            <p>You need to change <mark>YOURACCOUNTID</mark> by the <a href="https://developer.riotgames.com/api/methods#!/1061/3663">id of your League of Legends's account</a> and <mark>YOURCHANNEL</mark> by your Twitch channel's name.</p>
                            <p>Screenshot is comming soon!</p>

                            <!--
                            *****************************
                            *****************************
                                    HNLBOT
                            *****************************
                            *****************************
                            -->
                            <h3 class="bot-name" id="hnlbot">hnlBot</h3>
                            <p>hnlBot is a free bot for Twitch that has its own server.  You only need to create an account and you are good to go!  For more information <a href="http://hnlbot.com">www.hnlbot.com</a></p>
                            <!--
                            <p>To add a command with hnlBot, you have 2 choices.  You can either add it directly into your Twitch chat : </p>
                            <p>
                                <code>!command new rank !rank %CUSTOMAPI http://gotme.site-meute.com/query.php?action=rank&id=<mark>YOURACCOUNTID</mark>&channel=<mark>YOURCHANNEL</mark>&user=%INDEX1=%SENDERNAME%%&bot=%INDEX2=%hnlbot%%</code>
                            </p>
                            -->
                            <p>To add a command with hnlBot, you need to go into your hnlBot and add the following command into the 'Command Response' field : </p>
                            <p>
                                <code>%CUSTOMAPI http://gotme.site-meute.com/query.php?action=rank&id=<mark>YOURACCOUNTID</mark>&channel=<mark>YOURCHANNEL</mark>&user=%SENDERNAME%&bot=hnlbot%</code>
                            </p>
                            <p>You need to change <mark>YOURACCOUNTID</mark> by the <a href="https://developer.riotgames.com/api/methods#!/1061/3663">id of your League of Legends's account</a> and <mark>YOURCHANNEL</mark> by your Twitch channel's name.</p>
                            <p>See the screenshot for more options : </p>
                            <img src="../pictures/hnlbot.PNG">

                            <div class="blank"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

    </body>
</html>