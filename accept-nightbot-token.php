<?php
include 'apiFunctions/init.php';

if(isset($_POST['access_token'])) {

}
else if(isset($_GET['access_token'])) {

}
else {
    echo "Error: Nothing to work with.";
    exit();
}