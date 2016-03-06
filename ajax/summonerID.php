<?php
function getSummonerId($name, $region = 'na', $lang = 'fr', $details = false) {
    $name = strtolower($name);
    $name = str_replace(' ', '', $name);

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/". $region . "/v1.4/summoner/by-name/" . $name . "?api_key=55a52e18-c6ca-4325-9c82-61dde8a08a05");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonIds = json_decode($result, true);

    if(array_key_exists($name, $jsonIds) && $details == true) {
        if($lang == 'fr') {
            echo "L'ID de LoL pour \"" . $name . "\" en \"" . $region . "\" est : <strong>" . $jsonIds[$name]["id"] . "</strong>";
        }
        else {
            echo "The LoL ID for \"" . $name . "\" in \"" . $region . "\" is : <strong>" . $jsonIds[$name]["id"] . "</strong>";
        }
    }
    else if(array_key_exists($name, $jsonIds) && $details == false) {
        echo $jsonIds[$name]["id"];
    }
    else {
        if($lang == 'fr') {
            echo "Le summoner \"" . $name . "\" n'a pas &eacute;t&eacute; trouv&eacute;";
        }
        else {
            echo "The summoner \"" . $name . "\" has not been found";
        }
    }
}

$details = (isset($_POST['details'])) ? $_POST['details'] : false;
getSummonerId($_POST['summonerName'], $_POST['region'], 'en', $_POST['details']);