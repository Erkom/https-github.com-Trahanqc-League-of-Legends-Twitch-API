<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
$patchNotes = getAllFromTable('patchNotes', "", "dateCreated DESC");
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

                            <div class="blank"></div>

                            <div id="alerts"></div>

                            <?php if(isset($_SESSION['username']) && $_SESSION['username'] == "trahanqc") : ?>
                                <div class="col-lg-2 pull-right no-padding-right">
                                    <button class="btn btn-primary full-width" id="add-patch-notes"><i class="fa fa-plus"></i> Add Patch notes</button>
                                </div>
                                <div class="blank"></div>
                                <div class="blank"></div>
                            <?php endif;

                            if(count($patchNotes) > 0) :
                                foreach($patchNotes as $val) :
                                ?>
                                    <div class="card" id="patch-<?= $val['id']; ?>">
                                        <div class="card-header">
                                            <strong>Version: <span class="patch-version"><?= $val['version']; ?></span> &#8212; <?= format_date($val['dateCreated']); ?></strong>

                                            <?php if(isset($_SESSION['username']) && $_SESSION['username'] == "trahanqc") : ?>
                                                <div class="toolbar-controls">
                                                    <div class="editPatch" data-id="<?= $val['id']; ?>"><i class="fa fa-pencil"></i></div>
                                                    <div class="deletePatch" data-id="<?= $val['id']; ?>"><i class="fa fa-trash"></i></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-block">
                                            <h3 class="card-title"><?= $val['title']; ?></h3>
                                            <p class="card-text"><?= nl2br($val['patchNotes']); ?></p>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            else :
                            ?>
                                <p>Since the launching of the application, there has been no changes to the API.</p>
                            <?php endif; ?>

                            <div class="row reply-operation" id="add-patch-container">
                                <div class="col-lg-12">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <p class="lead">Add a patch note</p>
                                                <i class="fa fa-close pull-right" id="close-add-patch"></i>

                                                <div class="form-group">
                                                    <input type="text" id="add-patch-title" class="form-control" placeholder="Title of the Patch note">
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" id="add-patch-version" class="form-control" placeholder="Version of the Patch note">
                                                </div>

                                                <div class="form-group">
                                                    <textarea class="form-control" rows="4" id="add-patch-input" placeholder="Type the Patch notes here."></textarea>
                                                </div>

                                                <div class="col-lg-2 col-lg-offset-10 no-padding-right">
                                                    <button class="btn btn-primary full-width" id="add-patch-btn"><i class="fa fa-plus"></i> Add patch</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row reply-operation" id="edit-patch-container">
                                <div class="col-lg-12">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <p class="lead">Edit your reply</p>
                                                <i class="fa fa-close pull-right" id="close-edit-patch"></i>

                                                <div class="form-group">
                                                    <input type="text" id="edit-patch-title" class="form-control" placeholder="Title of the Patch note">
                                                </div>

                                                <div class="form-group">
                                                    <input type="text" id="edit-patch-version" class="form-control" placeholder="Version of the Patch note">
                                                </div>

                                                <div class="form-group">
                                                    <textarea class="form-control" rows="4" id="edit-patch-input" placeholder="Type the Patch notes here."></textarea>
                                                </div>

                                                <div class="col-lg-2 col-lg-offset-10 no-padding-right">
                                                    <button class="btn btn-success full-width" id="edit-reply-btn" data-id="<?= $_GET['id']?>"><i class="fa fa-save"></i> Edit patch note</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php for($x = 0 ; $x < 21 ; $x++) : ?>
                                <div class="blank"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
        <script src="../js/patchNotes.js"></script>

    </body>
</html>
