<?php
require_once '../../apiFunctions/init.php';

switch($_POST['action']) {
    case 'updateSummonerName':
        echo updateSummonerName($_POST['summonerName'], $_POST['region'], $_POST['summonerId'], $_POST['season'], $_POST['lang']);
        break;

    case "fetchMessages" :
        fetchMessages($_POST['id']);
        break;
}