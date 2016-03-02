<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

$user = checkConnect();
$message = "";
$categories = getAllFromTable("blogCategory", "", "ordre ASC");
$errors = array();

if(isset($_POST['title'])) {
    if(trim($_POST['title']) == "") $errors["title"] = true;
    if(trim($_POST['message']) == "") $errors["message"] = true;

    if(empty($errors)) {
        if(!empty($user)) {
            $idPost = addBlogPost($_POST['title'], $_POST['category'], $_POST['message'], $user[0]['twitchUsername']);
            $message = addAlert("The post has been added successfully! <a href='posts-" . $idPost . "'>View the post</a>", "alert-success", true);
        }
        else {
            $message = addAlert("You must be logged in with your Twitch account to post a message", "alert-danger", true);
        }
    }
    else {
        $message = addAlert("Please check the required fields, there might be a problem with them.", "alert-danger", true);
    }
}

if($message == "" && empty($user)) {
    $message = addAlert("You have to be logged in with your Twitch account to post a message", "alert-info", true);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add a message | Trahanqc's API</title>

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
                            <h1 class="page-header">Add a message</h1>

                            <ol class="breadcrumb">
                                <li>
                                    <i class="fa fa-dashboard"></i>  <a href="dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <i class="fa fa-ticket"></i>  <a href="forum">Forum</a>
                                </li>
                                <li>
                                    <i class="fa fa-comment"></i>  Add a message
                                </li>
                            </ol>

                            <div class="blank"></div>

                            <div class="row">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <div id="messages"><?= $message; ?></div>

                                    <form action="to_change" method="POST" id="form-add-message">
                                        <fieldset class="form-group <?= (array_key_exists('title', $errors)) ? 'has-danger' : ''; ?>">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= (array_key_exists('title', $errors)) ? 'form-control-danger' : ''; ?>" id="title" name="title" placeholder="What is the discussion about?">
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="category">Category <span class="text-danger">*</span></label>
                                            <select class="c-select full-width" id="category" name="category">
                                                <?php foreach($categories as $c) : ?>
                                                    <option value="<?= $c['id']; ?>"><?= $c['category']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </fieldset>
                                        <fieldset class="form-group <?= (array_key_exists('message', $errors)) ? 'has-danger' : ''; ?>">
                                            <label for="message">Message <span class="text-danger">*</span></label>
                                            <textarea class="form-control <?= (array_key_exists('message', $errors)) ? 'form-control-danger' : ''; ?>" id="message" name="message" placeholder="Content of the discussion" rows="5"></textarea>
                                            <p class="help-block"><span class="text-danger">*</span> required fields</p>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <button class="btn btn-primary full-width" id="btn-add-message">Add message</button>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>

                            <?php for($x = 0; $x < 8; $x++) : ?>
                                <div class="blank"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
        <script src="../js/add-message.js"></script>

    </body>
</html>
