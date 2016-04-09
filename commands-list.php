<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Commands list | Trahanqc's API</title>

        <?php include 'includes/header.php'; ?>

    </head>
    <body>
        <nav class="navbar navbar-fixed-top navbar-dark bg-inverse">
            <a class="navbar-brand" href="dashboard">Trahanqc's API</a>

            <div class="globalMessage"><?= $messageGlobal; ?></div>

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
                            <h1 class="page-header">Commands list</h1>

                            <ol class="breadcrumb">
                                <li class="active">
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <i class="fa fa-terminal"></i>  Commands list
                                </li>
                            </ol>

                            <p>Within this API, we have access to a lot of commands.  Some may be more useful for you and you can choose to add any of them.  Sadly, you will need to add them individually.  Feel free to change the command name, these are just examples!</p>

                            <table class="table table-hover table-striped table-responsive">
                                <thead>
                                    <tr>
                                        <th>Command name</th>
                                        <th>Description</th>
                                        <th>Example</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>!championPoints</td>
                                        <td>Display your champion points with a champion and if a chest has been granted.</td>
                                        <td>
                                            <code>Trahanqc: !championPoints tristana</code><br>
                                            <code>Nightbot: Champion level 5 (58190 points - S). Chest granted!</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!doublekills</td>
                                        <td>Display the number of doublekills you have and with which champions.  <strong>Note: be careful, the response may be long!</strong></td>
                                        <td>
                                            <code>Trahanqc: !doublekills</code><br>
                                            <code>Nightbot: Ekko (22), Shaco (13), Malphite (4), Zac (3)</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!lastgame</td>
                                        <td>Display the statistics of your last ranked game.</td>
                                        <td>
                                            <code>Trahanqc: !lastgame</code><br>
                                            <code>Nightbot: Last game won with Gragas 5/4/11 (4 KDA with 51.6% kill participation) 1 double kill</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!masteries</td>
                                        <td>Display your masteries for the current game.  <strong>Note: The summoner must be in a game.</strong></td>
                                        <td>
                                            <code>Trahanqc: !masteries</code><br>
                                            <code>Nightbot: 12/18/0 (Thunderlord's Decree)</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!mostplayed</td>
                                        <td>Display the 5 most played champions in ranked games.</td>
                                        <td>
                                            <code>Trahanqc: !mostplayed</code><br>
                                            <code>Nightbot: 1. Ekko (35), 2. Shaco (16), 3. Zac (9), 4. Malphite (9), 5. Brand (6)</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!pentakills</td>
                                        <td>Display the number of pentakills you have and with which champions.  <strong>Note: be careful, the response may be long!</strong></td>
                                        <td>
                                            <code>Trahanqc: !pentakills</code><br>
                                            <code>Nightbot: Ekko (1)</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!quadrakills</td>
                                        <td>Display the number of quadrakills you have and with which champions.  <strong>Note: be careful, the response may be long!</strong></td>
                                        <td>
                                            <code>Trahanqc: !quadrakills</code><br>
                                            <code>Nightbot: Ekko (2)</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!queue</td>
                                        <td>Display your current queue type.  <strong>Note: The summoner must be in a game.</strong></td>
                                        <td>
                                            <code>Trahanqc: !queue</code><br>
                                            <code>Nightbot: Ranked 5v5 Draft Pick</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!rank</td>
                                        <td>Display your current League of Legends ranking.</td>
                                        <td>
                                            <code>Trahanqc: !rank</code><br>
                                            <code>Nightbot: Gold V (86 LP) Series: ✓ X -</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!rankof <code>Summoner name</code></td>
                                        <td>Display the current League of Legends ranking of <code>Summoner name</code>.</td>
                                        <td>
                                            <code>Trahanqc: !rankof trahanqc</code><br>
                                            <code>Nightbot: Gold V (86 LP) Series: ✓ X -</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!stats <code>Champion name</code></td>
                                        <td>Display your current stats with <code>Champion name</code> in ranked games.</td>
                                        <td>
                                            <code>Trahanqc: !stats ekko</code><br>
                                            <code>Nightbot: 57.1% [20/15]. KDA: 8.1/7.7/8.6 1 penta kill</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!streak</td>
                                        <td>Display your current winning/losing streak in ranked games.</td>
                                        <td>
                                            <code>Trahanqc: !streak</code><br>
                                            <code>Nightbot: Win (1)</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!triplekills</td>
                                        <td>Display the number of triplekills you have and with which champions.  <strong>Note: be careful, the response may be long!</strong></td>
                                        <td>
                                            <code>Trahanqc: !triplekills</code><br>
                                            <code>Nightbot: Ekko (4), Malphite (1), Pantheon (1), Gragas (1)</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>!winrate</td>
                                        <td>Display your overall winning pourcentage and your top 5 champions with 5 games or more in ranked games.</td>
                                        <td>
                                            <code>Trahanqc: !winrate</code><br>
                                            <code>Nightbot: Overall : 48% Top 5 : 1. Blitzcrank (83.3%), 2. Brand (66.7%), 3. Ekko (57.1%), 4. Shaco (56.3%), 5. Morgana (40%)</code>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="blank"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

    </body>
</html>
