<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
$message = "";
$categories = getAllFromTable("blogCategory", "", "ordre ASC");

if(isset($_SESSION['username'])) {
    updateDateForumCheck();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Forum | Trahanqc's API</title>

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
                            <h1 class="page-header">Forum</h1>

                            <ol class="breadcrumb">
                                <li>
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li class="active">
                                    <i class="fa fa-ticket"></i>  Forum
                                </li>
                            </ol>

                            <div id="messages"><?= $message; ?></div>

                            <div class="blank"></div>

                            <div class="row">
                                <div class="col-sm-1">
                                    <h5 class="fix-lineheight">Category:</h5>
                                </div>

                                <div class="col-sm-2">
                                    <select class="c-select" id="select-category">
                                        <option value="0" selected>All</option>
                                        <?php foreach($categories as $c) : ?>
                                            <option value="<?= $c['id']; ?>"><?= $c['category']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <?php if(!empty($user)) : ?>
                                    <div class="col-sm-2 col-sm-offset-7">
                                        <button class="btn btn-primary full-width" id="btn-add-message"><i class="fa fa-plus"></i>  Add message</button>
                                    </div>
                                <?php else : ?>
                                    <div class="col-sm-2 col-sm-offset-7">
                                        <p class="lead pull-right">Login to add a post</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="blank"></div>

                            <div id="results"><?php fetchMessages("all"); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
        <script src="../js/jquery.timeago.js"></script>
        <script src="../js/forum.js"></script>

    </body>
</html>
