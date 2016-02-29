<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Patch notes | Trahanqc's API</title>

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
                            <h1 class="page-header">Patch notes</h1>

                            <ol class="breadcrumb">
                                <li class="active">
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <i class="fa fa-cog"></i>  Patch notes
                                </li>
                            </ol>

                            <p>Since the launching of the application, there has been no changes to the API.</p>

                            <?php for($x = 0 ; $x < 21 ; $x++) : ?>
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
