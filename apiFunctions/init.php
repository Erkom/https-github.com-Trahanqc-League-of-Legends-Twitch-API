<?php
session_start();

function connect_db() {
    try {
        $db = new PDO('mysql:host=localhost;dbname=sitemeut_louis', 'sitemeut_admin', '4C51d21f9C');
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    return $db;
}

function far_dump($string) {
    echo '<pre>';
    var_dump($string);
    echo '</pre>';
}

function addAlert($message, $classes, $dismiss = true) {
    $alert = "<div class='alert alert-dismissible " . $classes . "' role='alert'>";
    if($dismiss) {
        $alert .= "<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>";
    }

    switch($classes) {
        case 'alert-danger' : $alert .= '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ';
            break;

        case 'alert-success' : $alert .= '<i class="fa fa-check" aria-hidden="true"></i> ';
            break;

        case 'alert-warning' : $alert .= '<i class="fa fa-exclamation" aria-hidden="true"></i> ';
            break;

        case 'alert-info' : $alert .= '<i class="fa fa-cog" aria-hidden="true"></i> ';
            break;
    }

    $alert .= $message . "</div>";
    return $alert;
}

function getAllFromTable($table, $where = "", $orderby = "", $limit = "", $key = "", $newLine = false) {
    $db = connect_db();
    $query = "SELECT * FROM " . $table;
    if($where != "") {
        $query .= " WHERE " . $where;
    }
    if($orderby != "") {
        $query .= " ORDER BY " . $orderby;
    }
    if($limit != "") {
        $query .= " LIMIT " . $limit;
    }
    $rep = $db->query($query);
    $rep->execute();

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        if($key != "") {
            if($newLine) {
                $data[$donnees[$key]][] = $donnees;
            }
            else {
                $data[$donnees[$key]] = $donnees;
            }
        }
        else {
            $data[] = $donnees;
        }
    }

    return $data;
}

include 'twitchtv.php';
include 'functions.php';