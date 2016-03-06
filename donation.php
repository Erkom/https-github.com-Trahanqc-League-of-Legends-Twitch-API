<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Donation | Trahanqc's API</title>

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
                            <h1 class="page-header">Donation</h1>

                            <ol class="breadcrumb">
                                <li class="active">
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <i class="fa fa-money"></i>  Donation
                                </li>
                            </ol>

                            <p class="lead">If you like the API and would like to contribute to it, you can do so by doing a <a href="https://www.twitchalerts.com/donate/trahanqc">donation</a>!</p>

                            <!--
                            <p>
                                The next feature that I want to add to this API is to automatically add the commands into your Nightbot panel for you.  Sadly, I need to have a secure website to do so (the https version) and this certificate cost a little bit more than <strong>$65 USD</strong>.
                                Your donation will help be buy this certificate :)
                            </p>

                            <h4>Current progression of that goal</h4>

                            <?php
                            $progression = Round((5 / 60) * 100);
                            if($progression < 10) $class = "progress-danger";
                            else if($progression >= 10 && $progression < 30) $class = "progress-warning";
                            else if($progression >= 30 && $progression < 80) $class = "progress-info";
                            else $class = "progress-success";
                            ?>

                            <progress class="progress <?= $class; ?>" value="5" max="65"><?= $progression; ?>%</progress>
                            <strong><?= $progression; ?>%</strong>

                            -->

                            <div class="blank"></div>

                            <h4>Special thanks</h4>
                            <ul class="list-unstyled">
                                <li><a href="http://twitch.tv/mitchell486">Mitchell486</a></li>
                            </ul>

                            <?php for($x = 0 ; $x < 18 ; $x++) : ?>
                                <div class="blank"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

    </body>
</html>
