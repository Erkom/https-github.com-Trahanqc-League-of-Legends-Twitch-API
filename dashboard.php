<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
$message = (!empty($user)) ? addAlert("Hello there <strong>" . $_SESSION['username'] . "</strong>, welcome to the Trahanqc's API!", "alert-success", true) : '';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Dashboard | Trahanqc's API</title>

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
                            <h1 class="page-header">Dashboard</h1>

                            <ol class="breadcrumb">
                                <li class="active">
                                    <i class="fa fa-dashboard"></i>  Dashboard
                                </li>
                            </ol>

                            <div id="messages"><?= $message; ?></div>

                            <?php
                            if(!empty($user)) :
                                $milestones = getMilestones();
                                $stats = getStatsUsername($_SESSION['username']);
                                ?>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <h3>Most used commands</h3>
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Command name</th>
                                                    <th>Used</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $count = 0;
                                                if(isset($stats['mostUsed'])) :
                                                    foreach($stats['mostUsed'] as $command => $nb) :
                                                        if($count < 10) : ?>
                                                            <tr>
                                                                <td><?= "!" . $command; ?></td>
                                                                <td><?= $nb . " (<strong>" . Round(($nb / $stats['allCommands']) * 100, 2) . "%</strong>)"; ?></td>
                                                            </tr>
                                                            <?php
                                                        endif;
                                                        ++$count;
                                                    endforeach;
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-lg-6">
                                        <h3>User with most used commands</h3>
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Used</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $count = 0;
                                                if(isset($stats['mostUsername'])) :
                                                    foreach($stats['mostUsername'] as $username => $nb) :
                                                        if($count < 10) : ?>
                                                            <tr>
                                                                <td><?= $username; ?></td>
                                                                <td><?= $nb . " (<strong>" . Round(($nb / $stats['allCommands']) * 100, 2) . "%</strong>)"; ?></td>
                                                            </tr>
                                                            <?php
                                                        endif;
                                                        ++$count;
                                                    endforeach;
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="blank"></div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <h3>Days with most commands used</h3>
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Day</th>
                                                    <th>Used</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(isset($stats['mostPerDay'])) :
                                                    $count = 0;
                                                    foreach($stats['mostPerDay'] as $day => $nb) :
                                                        if($count < 10) :
                                                            ?>
                                                            <tr>
                                                                <td><?= format_date($day); ?></td>
                                                                <td><?= $nb . " (<strong>" . Round(($nb / $stats['allCommands']) * 100, 2) . "%</strong>)"; ?></td>
                                                            </tr>
                                                            <?php
                                                        endif;
                                                        ++$count;
                                                    endforeach;
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-lg-6">
                                        <h3>Milestones</h3>
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Milestone</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(isset($stats['milestones'])) :
                                                    foreach($stats['milestones'] as $milestone => $username) :
                                                        ?>
                                                        <tr>
                                                            <td><?= $milestones[$milestone]; ?></td>
                                                            <td><?= $milestone; ?> command was done by <?= $username; ?></td>
                                                        </tr>
                                                    <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <?php for($x = 0 ; $x < 10 ; $x++) : ?>
                                <div class="blank"></div>
                            <?php endfor; ?>

                            <?php else: ?>
                                <p>The dashboard is currently empty because you are not logged in with your Twitch account.  To do so, click on the login button in the upper right!</p>

                                <?php for($x = 0 ; $x < 21 ; $x++) : ?>
                                    <div class="blank"></div>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

    </body>
</html>
