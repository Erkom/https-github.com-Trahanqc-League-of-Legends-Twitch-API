<?php
include 'apiFunctions/init.php';
$twitchtv = new TwitchTV;

if(isset($_GET['code'])) {
    //far_dump($_GET);
    $ttv_code = $_GET['code'];
    $access_token = $twitchtv->get_access_token($ttv_code);
    $username = $twitchtv->authenticated_user($access_token);

    loginUser($username, $ttv_code, $access_token);

    header('Location: http://gotme.site-meute.com/api/v1/dashboard');
    exit();
}
else if(isset($_GET['error'])) {
    $message = addAlert("Authorization needed to use the application", "alert-danger", true);
    echo $message;
}