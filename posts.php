<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
$message = "";
$postLocked = getDisabled($_GET['id']);
$addReply = foundPost($_GET['id']);

if($postLocked) {
    $message = addAlert("This post is locked and no further replies are allowed", "alert-locked", true);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Post | Trahanqc's API</title>

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
                            <h1 class="page-header">Post</h1>

                            <ol class="breadcrumb">
                                <li>
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <i class="fa fa-ticket"></i>  <a href="forum">Forum</a>
                                </li>
                                <li>
                                    <i class="fa fa-comments"></i>  Post
                                </li>
                            </ol>

                            <div class="blank"></div>

                            <div id="alerts"><?= $message; ?></div>

                            <div id="results"><?php fetchPost($_GET['id']); ?></div>

                            <div id="controls">
                                <?php if($addReply) : ?>
                                    <?php if(isset($_SESSION['username'])) : ?>
                                        <button class="btn btn-primary pull-right" id="add-reply" <?= ($postLocked) ? 'disabled' : ''; ?>><i class="fa fa-reply"></i> Add a reply</button>
                                    <?php else: ?>
                                        <p class="lead">You need to be logged in to add a reply on this post.</p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <?php for($x = 0; $x < 13; $x++) : ?>
                                <div class="blank"></div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="row" id="add-reply-container">
                        <div class="col-lg-12">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p class="lead">Add a reply</p>
                                        <i class="fa fa-close pull-right" id="close-reply"></i>

                                        <div class="form-group">
                                            <textarea class="form-control" rows="8" id="add-reply-input" placeholder="Type you reply here."></textarea>
                                        </div>

                                        <div class="col-lg-2 col-lg-offset-10 no-padding-right">
                                            <button class="btn btn-primary full-width" id="add-reply-btn" data-id="<?= $_GET['id']?>"><i class="fa fa-reply"></i> Reply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="edit-reply-container">
                        <div class="col-lg-12">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p class="lead">Edit your reply</p>
                                        <i class="fa fa-close pull-right" id="close-edit-reply"></i>

                                        <div class="form-group">
                                            <textarea class="form-control" rows="8" id="edit-reply-input" placeholder="Type you reply here."></textarea>
                                        </div>

                                        <div class="col-lg-2 col-lg-offset-10 no-padding-right">
                                            <button class="btn btn-success full-width" id="edit-reply-btn" data-id="<?= $_GET['id']?>"><i class="fa fa-save"></i> Edit reply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="edit-post-container">
                        <div class="col-lg-12">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p class="lead">Edit your post</p>
                                        <i class="fa fa-close pull-right" id="close-edit-post"></i>

                                        <div class="form-group">
                                            <input class="form-control" id="edit-post-title" placeholder="Title of the post">
                                        </div>

                                        <div class="form-group">
                                            <textarea class="form-control" rows="7" id="edit-post-message" placeholder="Type the content of the post here."></textarea>
                                        </div>

                                        <div class="col-lg-2 col-lg-offset-10 no-padding-right">
                                            <button class="btn btn-success full-width" id="edit-post-btn" data-id="<?= $_GET['id']?>"><i class="fa fa-save"></i> Edit post</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
        <script src="../js/jquery.timeago.js"></script>
        <script src="../js/posts.js"></script>

    </body>
</html>
