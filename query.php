<?php
include 'credentials.php';

/**
 * @param $summonerId
 * @param string $lang
 * @param $channel
 * @param string $region
 *
 * Taking the summoner id from within the URL (id) and calling the getAllSummonerLeagues() function
 * to grab the current ranking of this summoner
 */
function getRank($summonerId, $lang = 'en', $channel, $region = 'na') {
    $summonerId = str_replace(')', '', $summonerId);
    echo getAllSummonerLeagues($summonerId, $lang, $channel, 'rank', $region);
}

/**
 * @param $name
 * @param string $lang
 * @param $channel
 * @param string $region
 *
 * Taking the summoner name from within the URL (name), grabbing the summoner id of the summoner and calling the
 * getAllSummonerLeagues() function to grab the current ranking of the summoner requested
 */
function getRankName($name, $lang = 'en', $channel, $region = 'na') {
    $summonerId = getSummonerId($name, $region, $channel);

    if($summonerId != NULL) {
        echo getAllSummonerLeagues($summonerId, $lang, $channel, 'rankname', $region);
    }
    else {
        if($lang == 'fr') {
            echo "Le joueur n'existe pas";
        }
        else {
            echo "The summoner name doesn't exist";
        }
    }
}

/**
 * @param $name
 * @param string $region
 * @param string $channel
 * @return string
 *
 * Grabs the summoner id from a summoner name
 */
function getSummonerId($name, $region = "na", $channel = "YOURCHANNEL") {
    global $api_keys;

    $name = strtolower($name);
    $name = str_replace(' ', '', $name);

    $settings = getApiSettings($channel);
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $region = ($region == "") ? 'na' : $region;

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.4/summoner/by-name/" . $name . "?api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonIds = json_decode($result, true);

    if(!empty($jsonIds)) {
        return $jsonIds[$name]["id"];
    }

    return "";
}

/**
 * @param $id
 * @param string $region
 * @param string $channel
 * @return string
 *
 * Grabs the summoner name from a summoner id
 */
function getSummonerName($id, $region = "na", $channel = "YOURCHANNEL") {
    global $api_keys;

    $settings = getApiSettings($channel);
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.4/summoner/" . $id . "?api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonIds = json_decode($result, true);

    if(!empty($jsonIds)) {
        return $jsonIds[$id]['name'];
    }
    else {
        return "NULL";
    }
}

/**
 * @param $summonerId
 * @param string $lang
 * @param string $channel
 * @param string $command
 * @param $region
 * @return string
 *
 * Display the current league of the summoner requested by his Id
 * Shows his ranking, LP and current series
 */
function getAllSummonerLeagues($summonerId, $lang = 'en', $channel = '', $command = 'rank', $region) {
    global $api_keys;

    $league = array();

    $settings = getApiSettings($channel);
    $urlSummonerId = $summonerId;
    $summonerId = ($settings != NULL && $command == 'rank') ? $settings[0]['summonerId'] : $summonerId;
    $summonerId = ($summonerId == "YOURSUMMONERID") ? $urlSummonerId : $summonerId;
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $region = ($region == "") ? "na" : $region;
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $commandSettings = grabCommandOutput($channel, $command);

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v2.5/league/by-summoner/" . $summonerId . "/entry?api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonLeague = json_decode($result, true);

    $league["summoner_name"] = getSummonerName($summonerId, $region, $channel);

    if($jsonLeague[$summonerId] != NULL) {
        foreach($jsonLeague[$summonerId] as $k => $v) {
            $progress = "";
            if($v["queue"] == "RANKED_SOLO_5x5") {
                $string = "";

                if(gettype($v) == "array") {
                    foreach($v as $key => $value) {
                        if(gettype($value) == "array") {
                            $temp = $value[0];
                            $string = $temp["division"] . ' (' . $temp["leaguePoints"] . ' LP)';

                            $league["rank"] = ucfirst(strtolower($v["tier"])) . " " . $temp["division"];
                            $league["league_points"] = $temp["leaguePoints"];

                            if(array_key_exists("miniSeries", $temp)) {
                                $progress .= $temp["miniSeries"]["progress"];
                            }
                        }
                    }
                }

                if($progress != "") {
                    $progress = str_split($progress);
                    $tempString = "Series:";
                    foreach($progress as $progressKey => $progressValue) {
                        if($progressValue == "L") {
                            $tempString .= " X";
                        }
                        else if($progressValue == "W") {
                            $tempString .= " ✓";
                        }
                        else if($progressValue == "N") {
                            $tempString .= " -";
                        }
                    }
                    $string .= ' ' . $tempString;
                    $league["series"] = $tempString;
                }

                $league[$summonerId] = ucfirst(strtolower($v["tier"])) . ' ' . $string;
            }

            if(!array_key_exists($summonerId, $league)) {
                $league[$summonerId] = "Unranked";
            }
        }
    }
    else {
        if($lang == 'fr') {
            $league[$summonerId] = htmlspecialchars("Les 10 parties de placements ne sont pas encore terminé");
        }
        else {
            $league[$summonerId] = "You need to finish the 10 placement games to get the ranking";
        }
    }

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists($command, $data)) ? $data->$command : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    foreach($variables as $val) {
        preg_match("/\(([a-z_]+)\)/", $val, $variableName);
        $outputString = str_replace($val, $league[$variableName[1]], $outputString);
    }

    return (array_key_exists("rank", $league)) ? htmlspecialchars($outputString) : "You need to finish the placements games.";
}

/**
 * @param $summonerId
 * @param string $lang
 * @param string $channel
 * @param string $command
 * @param string $region
 *
 * Display the current streak of the summoner requested by his Id
 * Only takes ranked games into account
 */
function getStreak($summonerId, $lang = 'en', $channel = '', $command = 'streak', $region = 'na') {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $summonerId;
    $summonerId = ($settings != NULL && $command == 'streak') ? $settings[0]['summonerId'] : $summonerId;
    $summonerId = ($summonerId == "YOURSUMMONERID") ? $urlSummonerId : $summonerId;
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $region = ($region == "") ? "na" : $region;
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $commandSettings = grabCommandOutput($channel, $command);
    $outputValues = array();
    $outputValues["summoner_name"] = getSummonerName($summonerId, $region, $channel);

    $ch = curl_init("https://" . $region .  ".api.pvp.net/api/lol/" . $region . "/v1.3/game/by-summoner/" . $summonerId . "/recent?api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonData = json_decode($result, true);

    $newData = array();

    $countStreak = 0;
    $firstGameStreak = true;
    $stopCounting = false;
    $countGame = 0;
    if(gettype($jsonData["games"]) == "array" || gettype($jsonData["games"]) == "object") {
        foreach($jsonData["games"] as $gameId => $gameGeneralData) {
            if($gameGeneralData["subType"] == "RANKED_SOLO_5x5") {
                if($countStreak == 0) {
                    $firstGameStreak = $gameGeneralData["stats"]["win"];
                    $outputValues["win"] = ($firstGameStreak) ? "Win" : "Lose";
                }

                if($gameGeneralData["stats"]["win"] == $firstGameStreak && !$stopCounting) {
                    $countStreak++;
                }
                else {
                    $stopCounting = true;
                }
                $countGame++;
            }
        }

        $outputValues["streak"] = $countStreak;

        if($lang == "fr") {
            $newData["winLoseStreak"] = ($firstGameStreak) ? "Victoire (" . $countStreak . ")": htmlspecialchars("Défaite (" . $countStreak . ")");
        }
        else {
            $newData["winLoseStreak"] = ($firstGameStreak) ? "Win (" . $countStreak . ")": "Lose (" . $countStreak . ")";
        }
    }

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists($command, $data)) ? $data->$command : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    foreach($variables as $val) {
        preg_match("/\(([a-z_]+)\)/", $val, $variableName);
        $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
    }

    echo htmlspecialchars($outputString);
}

/**
 * @param $id
 * @param string $lang
 * @param string $channel
 * @param $region
 *
 * Display the 5 most played champions of the summoner requested by his Id
 */
function getMostPlayed($id, $lang = 'en', $channel = '', $region) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $id;
    $id = ($settings != NULL) ? $settings[0]['summonerId'] : $id;
    $id = ($id == "YOURSUMMONERID") ? $urlSummonerId : $id;
    $season = ($settings != NULL) ? $settings[0]['season'] : 'SEASON2016';
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $region = ($region == "") ? 'na' : $region;

    $commandSettings = grabCommandOutput($channel, 'mostplayed');
    $outputValues = array();
    $outputValues["summoner_name"] = getSummonerName($id, $region, $channel);

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists('mostplayed', $data)) ? $data->mostplayed : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    $champions = getChampions();

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.3/stats/by-summoner/" . $id . "/ranked?season=" . $season . "&api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonData = json_decode($result, true);

    if(count($jsonData["champions"]) > 0) {
        $data = array();

        foreach($jsonData["champions"] as $val) {
            if($val["id"] != 0) {
                $data[$val["id"]]['name'] = $champions[$val["id"]]['name'];
                $data[$val["id"]]['nbGames'] = $val["stats"]["totalSessionsPlayed"];
            }
        }

        $data = array_reverse(quicksort($data, "nbGames"));

        $string = array();

        for($x = 0 ; $x < 5 ; $x++) {
            if(count($data) > $x) {
                $string[] = ($x + 1) . '. ' . $data[$x]['name'] . ' (' . $data[$x]['nbGames'] . ')';
            }
        }

        $seasonAddon = "";
        switch($season) {
            case 'SEASON2014' : $seasonAddon = "(Season 4) "; break;
            case 'SEASON2015' : $seasonAddon = "(Season 5) "; break;
        }

        $outputValues["season"] = $seasonAddon;
        $outputValues["most_played"] = implode($string, ', ');

        foreach($variables as $val) {
            preg_match("/\(([a-z_]+)\)/", $val, $variableName);
            $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
        }

        echo htmlspecialchars($outputString);
    }
    else {
        echo 'No ranked in the current season';
    }
}

/**
 * @param $id
 * @param string $lang
 * @param string $channel
 * @param $region
 *
 * Display the most successful champions of the summoner requested by Id.
 * Only works with ranked games.
 * 5 games or more are required to be played with a champion before being on that list.
 */
function getBestPerformance($id, $lang = 'en', $channel = '', $region) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $id;
    $id = ($settings != NULL) ? $settings[0]['summonerId'] : $id;
    $id = ($id == "YOURSUMMONERID") ? $urlSummonerId : $id;
    $season = ($settings != NULL) ? $settings[0]['season'] : 'SEASON2016';
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $region = ($region == "") ? "na" : $region;

    $commandSettings = grabCommandOutput($channel, 'bestperformance');
    $outputValues = array();
    $outputValues["summoner_name"] = getSummonerName($id, $region, $channel);

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists('bestperformance', $data)) ? $data->bestperformance : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    $champions = getChampions();

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.3/stats/by-summoner/" . $id . "/ranked?season=" . $season . "&api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonData = json_decode($result, true);

    if(count($jsonData["champions"]) > 0) {
        $data = array();
        $dataOverall = array();

        if(!isset($jsonData["status"])) {
            foreach($jsonData["champions"] as $val) {
                if($val["id"] != 0 && $val["stats"]["totalSessionsPlayed"] >= 5) {
                    $data[$val["id"]]['name'] = $champions[$val["id"]]['name'];
                    $data[$val["id"]]['winRate'] = ($val["stats"]["totalSessionsLost"] == 0) ? "100" : round(($val["stats"]["totalSessionsWon"] / $val["stats"]["totalSessionsPlayed"]) * 100, 1);
                }

                if($val["id"] != 0) {
                    if(!array_key_exists('win', $dataOverall)) {
                        $dataOverall['win'] = 0;
                        $dataOverall['played'] = 0;
                    }

                    $dataOverall['win'] += $val["stats"]["totalSessionsWon"];
                    $dataOverall['played'] += $val["stats"]["totalSessionsPlayed"];
                }
            }

            $data = array_reverse(quicksort($data, "winRate"));

            $string = array();

            $outputValues["top_number"] = 0;

            for($x = 0 ; $x < 5 ; $x++) {
                if(count($data) > $x) {
                    $string[] = ($x + 1) . '. ' . $data[$x]['name'] . ' (' . $data[$x]['winRate'] . '%)';
                    $outputValues["top_number"] = $x + 1;
                }
            }

            $seasonAddon = "";

            switch($season) {
                case 'SEASON2014' : $seasonAddon = "(Season 4) "; break;
                case 'SEASON2015' : $seasonAddon = "(Season 5) "; break;
                default: $seasonAddon = "";
            }

            $outputValues["season"] = $seasonAddon;
            $outputValues["best_performance"] = implode($string, ', ');
            $outputValues["overall"] = round(($dataOverall['win'] / $dataOverall['played']) * 100, 1) . "%";

            foreach($variables as $val) {
                preg_match("/\(([a-z_]+)\)/", $val, $variableName);
                $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
            }

            echo htmlspecialchars($outputString);
        }
        else {
            echo 'Problems with Riot API, sorry!';
        }
    }
    else {
        echo 'No ranked in the current season';
    }
}

/**
 * @param $id
 * @param $viewer
 * @param $champion
 * @param string $lang
 * @param string $channel
 * @param $region
 *
 * Display the stats of the champion for a summoner requested by Id.
 * Only works for ranked games.
 */
function getStats($id, $viewer, $champion, $lang = 'en', $channel = '', $region) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $id;
    $id = ($settings != NULL) ? $settings[0]['summonerId'] : $id;
    $id = ($id == "YOURSUMMONERID") ? $urlSummonerId : $id;
    $season = ($settings != NULL) ? $settings[0]['season'] : 'SEASON2016';
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $region = ($region == "") ? "na" : $region;
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $champion = multipleChampionNames($champion);

    $summonerName = getSummonerName($id, $region, $channel);

    $commandSettings = grabCommandOutput($channel, 'stats');
    $outputValues = array();
    $outputValues["summoner_name"] = $summonerName;

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists('stats', $data)) ? $data->stats : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    $champions = getChampions();

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.3/stats/by-summoner/" . $id . "/ranked?season=" . $season . "&api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonData = json_decode($result, true);

    if(count($jsonData['champions']) > 0) {
        $data = array();
        $dataOverall = array();

        if(!isset($jsonData['status'])) {
            foreach($jsonData['champions'] as $val) {
                if($val['id'] != 0 && strtolower($champion) == strtolower($champions[$val['id']]['displayName'])) {
                    $data['name'] = $champions[$val['id']]['displayName'];
                    $data['winRate'] = ($val['stats']['totalSessionsLost'] == 0) ? '100' : round(($val['stats']['totalSessionsWon'] / $val['stats']['totalSessionsPlayed']) * 100, 1);
                    $data['win'] = $val['stats']['totalSessionsWon'];
                    $data['lose'] = $val['stats']['totalSessionsLost'];
                    $data['kills'] = ($val['stats']['totalChampionKills'] == 0) ? $val['stats']['totalChampionKills'] : round(($val['stats']['totalChampionKills'] / $val['stats']['totalSessionsPlayed']), 1);
                    $data['deaths'] = ($val['stats']['totalChampionKills'] == 0) ? $val['stats']['totalDeathsPerSession'] : round(($val['stats']['totalDeathsPerSession'] / $val['stats']['totalSessionsPlayed']), 1);
                    $data['assists'] = ($val['stats']['totalChampionKills'] == 0) ? $val['stats']['totalAssists'] : round(($val['stats']['totalAssists'] / $val['stats']['totalSessionsPlayed']), 1);
                    $data['games'] = $val['stats']['totalSessionsPlayed'];
                    $data['penta'] = $val['stats']['totalPentaKills'];
                }

                if($val['id'] != 0) {
                    if(!array_key_exists('win', $dataOverall)) {
                        $dataOverall['win'] = 0;
                        $dataOverall['played'] = 0;
                    }

                    $dataOverall['win'] += $val['stats']['totalSessionsWon'];
                    $dataOverall['played'] += $val['stats']['totalSessionsPlayed'];
                }
            }

            switch($season) {
                case "SEASON2015": $seasonAddon = "(Season 5) "; break;
                case "SEASON2014": $seasonAddon = "(Season 4) "; break;
                case "SEASON3": $seasonAddon = "(Season 3) "; break;
                default: $seasonAddon = ""; break;
            }

            if(!empty($data)) {
                $outputValues["season"] = $seasonAddon;
                $outputValues["champion"] = $champion;
                $outputValues["win_rate"] = $data["winRate"] . "%";
                $outputValues["games"] = '[' . $data["win"] . '/' . $data["lose"] . ']';
                $outputValues["kda"] = $data["kills"] . '/' . $data["deaths"] . '/' . $data["assists"];
                $outputValues["penta"] = ($data['penta'] != 0) ? ' ' . $data['penta'] . " pentakill!" : '';

                foreach($variables as $val) {
                    preg_match("/\(([a-z_]+)\)/", $val, $variableName);
                    $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
                }

                echo htmlspecialchars($outputString);
            }
            else {
                echo 'Sorry ' . $viewer . ', no ranked stats for ' . $champion . ' yet';
            }
        }
        else {
            echo 'Problems with Riot API, sorry!';
        }
    }
    else {
        echo 'No ranked in the current season';
    }
}

/**
 * @param $summonerId
 * @param string $lang
 * @param string $channel
 * @param $region
 *
 * Display stats from the last game played for the summoner requested by Id.
 * Only works for ranked games.
 */
function lastGame($summonerId, $lang = 'en', $channel = '', $region) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $summonerId;
    $summonerId = ($settings != NULL) ? $settings[0]['summonerId'] : $summonerId;
    $summonerId = ($summonerId == "YOURSUMMONERID") ? $urlSummonerId : $summonerId;
    $season = ($settings != NULL) ? $settings[0]['season'] : 'SEASON2016';
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $region = ($region == "") ? "na" : $region;
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $champion = 0;
    $matchId = 0;

    $commandSettings = grabCommandOutput($channel, 'lastGame');
    $outputValues = array();
    $outputValues["summoner_name"] = getSummonerName($summonerId, $region, $channel);

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists('lastGame', $data)) ? $data->lastGame : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    $champions = getChampions();

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v2.2/matchlist/by-summoner/" . $summonerId . "?rankedQueues=TEAM_BUILDER_DRAFT_RANKED_5x5&seasons=" . $season . "&api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonMatches = json_decode($result, true);

    if(!empty($jsonMatches['matches'])) {
        $champion = $jsonMatches['matches'][0]['champion'];
        $matchId = $jsonMatches['matches'][0]['matchId'];

        if($matchId != 0 && $champion != 0) {
            $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v2.2/match/" . $matchId . "?api_key=" . $api_keys[0]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            $jsonMatch = json_decode($result, true);

            $KDA = 0;
            $kills = 0;
            $deaths = 0;
            $assists = 0;
            $winLose = "";
            $double = 0;
            $triple = 0;
            $quadra = 0;
            $penta = 0;
            $teamId = 0;
            $teamKills = array(100 => 0, 200 => 0);

            if(count($champions) > 0) {
                if(!empty($jsonMatch['participants'])) {
                    foreach($jsonMatch['participants'] as $part) {
                        if($part['championId'] == $champion) {
                            $teamId = $part['teamId'];
                            if($lang == 'fr') {
                                $winLose = ($part['stats']['winner']) ? 'gagné' : 'perdue';
                            }
                            else {
                                $winLose = ($part['stats']['winner']) ? 'won' : 'lost';
                            }
                            $kills = $part['stats']['kills'];
                            $deaths = $part['stats']['deaths'];
                            $assists = $part['stats']['assists'];
                            $KDA = ($deaths != 0) ? round(($kills + $assists) / $deaths, 2) : $kills + $assists;
                            $double = $part['stats']['doubleKills'];
                            $triple = $part['stats']['tripleKills'];
                            $quadra = $part['stats']['quadraKills'];
                            $penta = $part['stats']['pentaKills'];
                        }

                        $teamKills[$part['teamId']] += $part['stats']['kills'];
                    }

                    $killParticipation = ($teamKills[$teamId] != 0) ? round((($kills + $assists) / $teamKills[$teamId]) * 100, 1) : 0;

                    $suffix = "";

                    if($penta > 0) {
                        $suffix = " " . $penta . " penta kill";
                    }
                    else if($quadra > 0) {
                        $suffix = " " . $quadra . " quadra kill";
                    }
                    else if($triple > 0) {
                        $suffix = " " . $triple . " triple kill";
                    }
                    else if($double > 0) {
                        $suffix = " " . $double . " double kill";
                    }

                    $outputValues["win"] = $winLose;
                    $outputValues["champion"] = $champions[$champion]['displayName'];
                    $outputValues["kills"] = $kills . '/' . $deaths . '/' . $assists;
                    $outputValues["kda"] = $KDA;
                    $outputValues["kill_participation"] = $killParticipation . "%";
                    $outputValues["highest_kill"] = $suffix;

                    foreach($variables as $val) {
                        preg_match("/\(([a-z_]+)\)/", $val, $variableName);
                        $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
                    }

                    echo htmlspecialchars($outputString);
                }
                else {
                    echo 'Problems with API';
                }
            }
            else {
                echo 'Problems with API';
            }
        }
        else {
            echo 'Problems with API';
        }
    }
    else {
        echo 'No ranked in the current season';
    }
}

/**
 * @param $summonerId
 * @param $field
 * @param int $min
 * @param string $lang
 * @param string $channel
 * @param $region
 *
 * Display how many Xkills the summoner requested by Id has.
 * Only works for ranked games.
 */
function getPentakills($summonerId, $field, $min = 1, $lang = 'en', $channel = "", $region) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $summonerId;
    $summonerId = ($settings != NULL) ? $settings[0]['summonerId'] : $summonerId;
    $summonerId = ($summonerId == "YOURSUMMONERID") ? $urlSummonerId : $summonerId;
    $season = ($settings != NULL) ? $settings[0]['season'] : 'SEASON2016';
    $region = ($settings != NULL) ? strtolower($settings[0]['region']) : strtolower($region);
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $summonerName = getSummonerName($summonerId, $region, $channel);

    $ch = curl_init("https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.3/stats/by-summoner/" . $summonerId . "/ranked?season=" . $season . "&api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonMatches = json_decode($result, true);

    switch($field) {
        case 'totalPentaKills' : $string = "pentakill"; break;
        case 'totalQuadraKills' : $string = "quadrakill"; break;
        case 'totalTripleKills' : $string = "triplekill"; break;
        default : $string = "doublekill"; break;
    }

    $commandName = $string . "s";
    $commandSettings = grabCommandOutput($channel, $commandName);
    $outputValues = array();
    $outputValues["summoner_name"] = $summonerName;

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists($commandName, $data)) ? $data->$commandName : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    if(!empty($jsonMatches)) {
        if(!empty($jsonMatches['champions'])) {
            $pentakills = array();
            $finalString = array();

            foreach($jsonMatches['champions'] as $val) {
                if($val['stats'][$field] != 0 && $val['stats'][$field] >= $min) {
                    $pentakills[$val['id']]['id'] = $val['id'];
                    $pentakills[$val['id']][$field] = $val['stats'][$field];
                }
            }

            $champions = getChampions();

            if(count($champions) > 0) {
                $pentakills = array_reverse(quicksort($pentakills, $field));

                foreach($pentakills as $penta) {
                    if($penta['id'] != 0) {
                        $finalString[] = $champions[$penta['id']]['name'] . " (" . $penta[$field] . ")";
                    }
                }

                $outputValues["stats"] = (!empty($finalString)) ? implode(', ', $finalString) : "No " . $string . "s for the current season";

                foreach($variables as $val) {
                    preg_match("/\(([a-z_]+)\)/", $val, $variableName);
                    $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
                }

                echo htmlspecialchars($outputString);
            }
            else {
                echo 'Problems with API';
            }
        }
        else {
            echo 'No ranked in the current season';
        }
    }
    else {
        echo 'No ranked in the current season';
    }
}

/**
 * @param $summonerId
 * @param string $lang
 * @param string $channel
 * @param $region
 *
 * Display the current queue type the summoner requested by Id is currently in.
 * Required to be in-game.
 */
function getCurrentQueue($summonerId, $lang = 'en', $channel = '', $region) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $summonerId;
    $summonerId = ($settings != NULL) ? $settings[0]['summonerId'] : $summonerId;
    $summonerId = ($summonerId == "YOURSUMMONERID") ? $urlSummonerId : $summonerId;
    $region = ($settings != NULL) ? $settings[0]['region'] : strtoupper($region);
    $region = ($region == "") ? "NA1" : $region;
    $regions = getRegions();
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $summonerName = getSummonerName($summonerId, $region, $channel);

    $commandSettings = grabCommandOutput($channel, 'currentqueue');
    $outputValues = array();
    $outputValues["summoner_name"] = $summonerName;

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists('currentqueue', $data)) ? $data->currentqueue : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    $ch = curl_init("https://" . strtolower($region) . ".api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/" . $regions[$region] . "/" . $summonerId . "?api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonData = json_decode($result, true);

    if(count($jsonData) != 1) {
        $db = connect_db();
        $query = "SELECT Name FROM queueType WHERE gameQueueConfigId = ?";
        $rep = $db->prepare($query);
        $rep->execute(array($jsonData['gameQueueConfigId']));

        $queue = "";

        while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
            $queue = $donnees['Name'];
        }

        $outputValues["queue"] = $queue;

        foreach($variables as $val) {
            preg_match("/\(([a-z_]+)\)/", $val, $variableName);
            $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
        }

        echo htmlspecialchars($outputString);
    }
    else {
        if($lang == 'fr') {
            echo $summonerName . ' n\'est pas dans une partie actuellement';
        }
        else {
            echo $summonerName . ' is not currently in a game';
        }
    }
}

/**
 * @param $summonerId
 * @param string $lang
 * @param string $channel
 * @param $region
 *
 * Display the current masteries the summoner requested by Id currently has.
 * Required to be in-game.
 */
function getMasteriesDetails($summonerId, $lang = 'en', $channel = '', $region) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $summonerId;
    $summonerId = ($settings != NULL) ? $settings[0]['summonerId'] : $summonerId;
    $summonerId = ($summonerId == "YOURSUMMONERID") ? $urlSummonerId : $summonerId;
    $region = ($settings != NULL) ? $settings[0]['region'] : strtoupper($region);
    $region = ($region == "") ? "NA" : $region;
    $regions = getRegions();
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $summonerName = getSummonerName($summonerId, $region, $channel);

    $commandSettings = grabCommandOutput($channel, 'masteries');
    $outputValues = array();
    $outputValues["summoner_name"] = $summonerName;

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists('masteries', $data)) ? $data->masteries : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    $ch = curl_init("https://" . strtolower($region) . ".api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/" . $regions[$region] . "/" . $summonerId . "?api_key=" . $api_keys[0]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $jsonData = json_decode($result, true);

    if(count($jsonData) != 1) {
        $db = connect_db();
        $query = "SELECT id, name, mastery_tree FROM masteries";
        $rep = $db->prepare($query);
        $rep->execute();

        $masteries = array();
        $username = "";

        while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
            $masteries[$donnees['id']]['tree'] = $donnees['mastery_tree'];
            $masteries[$donnees['id']]['name'] = $donnees['name'];
        }

        $masterieTree = array(
            'Ferocity' => 0,
            'Cunning' => 0,
            'Resolve' => 0,
            'Final' => "");

        if(!empty($jsonData['participants'])) {
            foreach($jsonData['participants'] as $val) {
                if($summonerId == $val['summonerId']) {
                    $username = $val['summonerName'];

                    foreach($val['masteries'] as $m) {
                        foreach($masteries as $mId => $mas) {
                            if($m['masteryId'] == $mId) {
                                $masterieTree[$mas['tree']] += $m['rank'];

                                if($mId == 6161 || $mId == 6162 || $mId == 6164 || $mId == 6361 || $mId == 6362 || $mId == 6363 || $mId == 6261 || $mId == 6262 || $mId == 6263) {
                                    $masterieTree['Final'] = $mas['name'];
                                }
                            }
                        }
                    }
                }
            }

            $outputValues["masteries"] = $masterieTree['Ferocity'] . '/' . $masterieTree['Cunning'] . '/' . $masterieTree['Resolve'];
            $outputValues["details"] = ' (' . $masterieTree['Final'] . ')';

            foreach($variables as $val) {
                preg_match("/\(([a-z_]+)\)/", $val, $variableName);
                $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
            }

            echo htmlspecialchars($outputString);
        }
        else {
            echo 'Problems with API';
        }
    }
    else if(strtolower($summonerName) == "brouitup"){
        echo 'Masteries de Brouitup : http://www.lolking.net/summoner/na/21931905#masteries';
    }
    else {
        if($lang == 'fr') {
            echo $summonerName . " n'est pas dans une partie";
        }
        else {
            echo $summonerName . " is not currently in a game";
        }
    }
}

/**
 * @param $summonerId
 * @param $lang
 * @param $channel
 * @param $region
 * @param $champion
 *
 * Display the champion points the summoner requested by Id currently has.
 * Shows the mastery points, mastery level, highest grade and if a chest has been granted.
 */
function getChampionPoints($summonerId, $lang, $channel, $region, $champion) {
    global $api_keys;

    $settings = getApiSettings($channel);
    $urlSummonerId = $summonerId;
    $summonerId = ($settings != NULL) ? $settings[0]['summonerId'] : $summonerId;
    $summonerId = ($summonerId == "YOURSUMMONERID") ? $urlSummonerId : $summonerId;
    $season = ($settings != NULL) ? $settings[0]['season'] : 'SEASON2016';
    $region = ($settings != NULL) ? $settings[0]['region'] : strtoupper($region);
    $region = ($region == "") ? "NA" : $region;
    $regions = getRegions();
    $lang = ($settings != NULL) ? strtolower($settings[0]['lang']) : $lang;

    $championId = 0;

    $champion = multipleChampionNames(strtolower($champion));

    $commandSettings = grabCommandOutput($channel, 'championPoints');
    $outputValues = array();
    $outputValues["summoner_name"] = getSummonerName($summonerId, $region, $channel);

    if(array_key_exists("data", $commandSettings)) {
        $data = json_decode($commandSettings["data"]);
        $outputString = (array_key_exists('championPoints', $data)) ? $data->championPoints : $commandSettings["default"]["response"];
    }
    else {
        $outputString = $commandSettings["default"]["response"];
    }

    $variables = explode(',', $commandSettings["default"]["variables"]);
    $variableName = array();

    $champions = getChampions();

    if(count($champions) > 0) {
        foreach($champions as $v) {
            if(strtolower($v['displayName']) == strtolower($champion)) {
                $championId = $v['id'];
            }
        }

        if($championId != 0) {
            $ch = curl_init("https://" . strtolower($region) . ".api.pvp.net/championmastery/location/" . $regions[$region] . "/player/" . $summonerId . "/champions?api_key=" . $api_keys[0]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            $jsonPoints = json_decode($result, true);

            if($jsonPoints != NULL) {
                foreach($jsonPoints as $val) {
                    if($val['championId'] == $championId) {
                        $outputValues["champion_name"] = $champion;
                        $outputValues["champion_points"] = $val["championPoints"];
                        $outputValues["champion_level"] = $val["championLevel"];
                        $outputValues["chest_granted"] = ($val["chestGranted"]) ? "Chest granted!" : "";
                        $outputValues["highest_grade"] = (isset($val['highestGrade'])) ? $val['highestGrade'] : '';
                    }
                }

                if(isset($outputValues['champion_name'])) {
                    foreach($variables as $val) {
                        preg_match("/\(([a-z_]+)\)/", $val, $variableName);
                        $outputString = str_replace($val, $outputValues[$variableName[1]], $outputString);
                    }

                    echo htmlspecialchars($outputString);
                }
                else {
                    echo "No champion points found for " . $champion;
                }
            }
            else {
                echo "Unable to retrieve champions points";
            }
        }
        else {
            echo "The champion " . $champion . " does not exists";
        }
    }
}

/**
 * @param string $quote
 * @param string $user
 * @param string $userLevel
 * @param string $channel
 *
 * Allow a moderator to add a quote
 */
function addQuote($quote = "", $user = "none", $userLevel = "everyone", $channel = "YOURCHANNEL") {
    if($quote != "" && ($userLevel == "moderator" || $userLevel == "owner")) {
        $db = connect_db();
        $query = "INSERT INTO citations VALUES(?, ?, ?, ?, ?)";
        $rep = $db->prepare($query);
        $rep->execute(array('', $quote, $channel, $user, date('Y-m-d H:i:s', strtotime("now -6hours"))));

        echo htmlspecialchars("Quote added");
    }
}

/**
 * @param string $channel
 * @param string $userLevel
 * @param int $quoteId
 *
 * Allow a moderator to remove a quote
 */
function deleteQuote($channel = "YOURCHANNEL", $userLevel = "everyone", $quoteId = 0) {
    if($userLevel == "moderator" || $userLevel == "owner") {
        $db = connect_db();
        $query = "SELECT COUNT(id) FROM citations WHERE channel = ? AND id = ?";
        $rep = $db->prepare($query);
        $rep->execute(array($channel, $quoteId));
        $quoteFound = $rep->fetch(PDO::FETCH_NUM)[0];

        if($quoteFound) {
            $query = "DELETE FROM citations WHERE channel = ? AND id = ?";
            $rep = $db->prepare($query);
            $rep->execute(array($channel, $quoteId));

            echo htmlspecialchars("Quote #" . $quoteId . " removed");
        }
        else {
            echo htmlspecialchars("Quote #" . $quoteId . " was not found");
        }
    }
}

/**
 * @param string $channel
 * @param string $search
 *
 * Grabs a random quote.
 * Search by quote Id or words.
 */
function fetchQuote($channel = "YOURCHANNEL", $search = "") {
    $db = connect_db();
    $query = "SELECT COUNT(id) FROM citations WHERE channel = ?";
    $rep = $db->prepare($query);
    $rep->execute(array($channel));
    $nbQuotes = $rep->fetch(PDO::FETCH_NUM)[0];

    if($nbQuotes > 0) {
        if($search == "") {
            $query = "SELECT citation FROM citations WHERE channel = ? ORDER BY RAND() LIMIT 1";
            $rep = $db->prepare($query);
            $rep->execute(array($channel));
            $quote = $rep->fetch(PDO::FETCH_ASSOC)['citation'];
            echo (!empty($quote)) ? $quote : "No quotes found";
        }
        else if(is_numeric($search)) {
            $query = "SELECT citation FROM citations WHERE channel = ? AND id = ?";
            $rep = $db->prepare($query);
            $rep->execute(array($channel, $search));
            $quote = $rep->fetch(PDO::FETCH_ASSOC)['citation'];
            echo (!empty($quote)) ? $quote : "Quote #" . $search . " was not found";
        }
        else if($search == "list") {
            echo htmlspecialchars("http://gotme.site-meute.com/quotes?channel=" . $channel);
        }
        else {
            $query = "SELECT citation FROM citations WHERE channel = ? AND LOWER(citation) LIKE LOWER(?) ORDER BY RAND() LIMIT 1";
            $rep = $db->prepare($query);
            $rep->execute(array($channel, '%' . $search . '%'));
            $quote = $rep->fetch(PDO::FETCH_ASSOC)['citation'];
            echo (!empty($quote)) ? $quote : "No quotes found with the search term " . $search;
        }
    }
    else {
        echo "No quotes has been found for " . $channel . "'s channel";
    }
}

/**
 * @param string $channel
 * @return array
 *
 * Return all the quotes for a channel
 */
function getQuotes($channel = "YOURCHANNEL") {
    $db = connect_db();
    $query = "SELECT * FROM citations WHERE LOWER(channel) = LOWER(?) ORDER BY dateCitation DESC";
    $rep = $db->prepare($query);
    $rep->execute(array($channel));

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $donnees;
    }

    return $data;
}

/**
 * @param $champion
 * @return mixed
 *
 * Returns the real name of the champion when using a shorter version of his name
 */
function multipleChampionNames($champion) {
    $championNames = array(
        "aatrox" => "Aatrox",
        "ahri" => "Ahri",
        "akali" => "Akali",
        "alistar" => "Alistar",
        "ali" => "Alistar",
        "amumu" => "Amumu",
        "mumu" => "Amumu",
        "anivia" => "Anivia",
        "nivia" => "Anivia",
        "annie" => "Annie",
        "ashe" => "Ashe",
        "aurelion Sol" => "Aurelion Sol",
        "aurelionsol" => "Aurelion Sol",
        "aurelion" => "Aurelion Sol",
        "aurel" => "Aurelion Sol",
        "azir" => "Azir",
        "shurima" => "Azir",
        "bard" => "Bard",
        "blitzcrank" => "Blitzcrank",
        "blitz" => "Blitzcrank",
        "crank" => "Blitzcrank",
        "brand" => "Brand",
        "braum" => "Braum",
        "caitlyn" => "Caitlyn",
        "cait" => "Caitlyn",
        "cassiopeia" => "Cassiopeia",
        "cassio" => "Cassiopeia",
        "cass" => "Cassiopeia",
        "cho'gath" => "Cho'Gath",
        "cho" => "Cho'Gath",
        "chogath" => "Cho'Gath",
        "gath" => "Cho'Gath",
        "corki" => "Corki",
        "darius" => "Darius",
        "dar" => "Darius",
        "diana" => "Diana",
        "dia" => "Diana",
        "draven" => "Draven",
        "drav" => "Draven",
        "draaaaven" => "Draven",
        "dr. mundo" => "Dr. Mundo",
        "dr mundo" => "Dr. Mundo",
        "drmundo" => "Dr. Mundo",
        "mundo" => "Dr. Mundo",
        "ekko" => "Ekko",
        "elise" => "Elise",
        "spider" => "Elise",
        "ezreal" => "Ezreal",
        "ez" => "Ezreal",
        "fiddlesticks" => "Fiddlesticks",
        "fiddle" => "Fiddlesticks",
        "fidd" => "Fiddlesticks",
        "fid" => "Fiddlesticks",
        "crittlesticks" => "Fiddlesticks",
        "fiora" => "Fiora",
        "fio" => "Fiora",
        "fizz" => "Fizz",
        "fish" => "Fizz",
        "galio" => "Galio",
        "gal" => "Galio",
        "gangplank" => "Gangplank",
        "pirate" => "Gangplank",
        "gp" => "Gangplank",
        "garen" => "Garen",
        "spin to win" => "Garen",
        "spintowin" => "Garen",
        "gnar" => "Gnar",
        "gragas" => "Gragas",
        "grag" => "Gragas",
        "graves" => "Graves",
        "hecarim" => "Hecarim",
        "heca" => "Hecarim",
        "hec" => "Hecarim",
        "heimerdinger" => "Heimerdinger",
        "heimerdonger" => "Heimerdinger",
        "donger" => "Heimerdinger",
        "illaoi" => "Illaoi",
        "illa" => "Illaoi",
        "ill" => "Illaoi",
        "irelia" => "Irelia",
        "janna" => "Janna",
        "jarvan iv" => "Jarvan IV",
        "jarvaniv" => "Jarvan IV",
        "jarvan 4" => "Jarvan IV",
        "jarvan4" => "Jarvan IV",
        "jiv" => "Jarvan IV",
        "j iv" => "Jarvan IV",
        "j4" => "Jarvan IV",
        "jax" => "Jax",
        "jayce" => "Jayce",
        "jhin" => "Jhin",
        "kalista" => "Kalista",
        "kalis" => "Kalista",
        "kali" => "Kalista",
        "kal" => "Kalista",
        "Karma" => "Karma",
        "karthus" => "Karthus",
        "karth" => "Karthus",
        "kart" => "Karthus",
        "kassadin" => "Kassadin",
        "kassa" => "Kassadin",
        "kass" => "Kassadin",
        "casque de bain" => "Kassadin",
        "casquedebain" => "Kassadin",
        "katarina" => "Katarina",
        "kata" => "Katarina",
        "kat" => "Katarina",
        "Kayle" => "Kayle",
        "kennen" => "Kennen",
        "ken" => "Kennen",
        "kha'zix" => "Kha'Zix",
        "kha zix" => "Kha'Zix",
        "khazix" => "Kha'Zix",
        "kha" => "Kha'Zix",
        "bug" => "Kha'Zix",
        "kindred" => "Kindred",
        "kind" => "Kindred",
        "kin" => "Kindred",
        "kog'maw" => "Kog'Maw",
        "kog maw" => "Kog'Maw",
        "kogmaw" => "Kog'Maw",
        "kog" => "Kog'Maw",
        "leblanc" => "LeBlanc",
        "lb" => "LeBlanc",
        "lee sin" => "Lee Sin",
        "leesin" => "Lee Sin",
        "lee" => "Lee Sin",
        "blind man" => "Lee Sin",
        "blind" => "Lee Sin",
        "leona" => "Leona",
        "leo" => "Leona",
        "lissandra" => "Lissandra",
        "liss" => "Lissandra",
        "lucian" => "Lucian",
        "luci" => "Lucian",
        "luc" => "Lucian",
        "black man" => "Lucian",
        "black" => "Lucian",
        "lulu" => "Lulu",
        "lux" => "Lux",
        "malphite" => "Malphite",
        "malph" => "Malphite",
        "malzahar" => "Malzahar",
        "malza" => "Malzahar",
        "malz" => "Malzahar",
        "maokai" => "Maokai",
        "mao" => "Maokai",
        "master yi" => "Master Yi",
        "masteryi" => "Master Yi",
        "master" => "Master Yi",
        "yi" => "Master Yi",
        "miss fortune" => "Miss Fortune",
        "missfortune" => "Miss Fortune",
        "mf" => "Miss Fortune",
        "mordekaiser" => "Mordekaiser",
        "morde" => "Mordekaiser",
        "mord" => "Mordekaiser",
        "hue" => "Mordekaiser",
        "morgana" => "Morgana",
        "morg" => "Morgana",
        "nami" => "Nami",
        "mermaid" => "Nami",
        "nasus" => "Nasus",
        "susan" => "Nasus",
        "doge" => "Nasus",
        "dog" => "Nasus",
        "nautilus" => "Nautilus",
        "naut" => "Nautilus",
        "nidalee" => "Nidalee",
        "cougar" => "Nidalee",
        "nid" => "Nidalee",
        "nocturne" => "Nocturne",
        "noct" => "Nocturne",
        "nunu" => "Nunu",
        "ahahah" => "Nunu",
        "olaf" => "Olaf",
        "orianna" => "Orianna",
        "oriana" => "Orianna",
        "ori" => "Orianna",
        "pantheon" => "Pantheon",
        "panth" => "Pantheon",
        "300" => "Pantheon",
        "poppy" => "Poppy",
        "quinn" => "Quinn",
        "rammus" => "Rammus",
        "rek'sai" => "Rek'Sai",
        "rek sai" => "Rek'Sai",
        "reksai" => "Rek'Sai",
        "rek" => "Rek'Sai",
        "renekton" => "Renekton",
        "renek" => "Renekton",
        "crocodile" => "Renekton",
        "rengar" => "Rengar",
        "rengo" => "Rengar",
        "ren" => "Rengar",
        "riven" => "Riven",
        "rumble" => "Rumble",
        "ryze" => "Ryze",
        "sejuani" => "Sejuani",
        "sej" => "Sejuani",
        "shaco" => "Shaco",
        "shen" => "Shen",
        "shyvana" => "Shyvana",
        "shyv" => "Shyvana",
        "singed" => "Singed",
        "poison master" => "Singed",
        "poisonmaster" => "Singed",
        "sion" => "Sion",
        "sivir" => "Sivir",
        "skarner" => "Skarner",
        "skar" => "Skarner",
        "sona" => "Sona",
        "soraka" => "Soraka",
        "soroko" => "Soraka",
        "roko" => "Soraka",
        "raka" => "Soraka",
        "ambulance" => "Soraka",
        "swain" => "Swain",
        "syndra" => "Syndra",
        "tahm kench" => "Tahm Kench",
        "tahmkench" => "Tahm Kench",
        "tahm" => "Tahm Kench",
        "kench" => "Tahm Kench",
        "talon" => "Talon",
        "taric" => "Taric",
        "gems" => "Taric",
        "teemo" => "Teemo",
        "satan" => "Teemo",
        "captain teemo" => "Teemo",
        "Thresh" => "Thresh",
        "Thr" => "Thresh",
        "Th" => "Thresh",
        "tristana" => "Tristana",
        "trist" => "Tristana",
        "trundle" => "Trundle",
        "tryndamere" => "Tryndamere",
        "trynda" => "Tryndamere",
        "trynd" => "Tryndamere",
        "tryn" => "Tryndamere",
        "twisted fate" => "Twisted Fate",
        "twistedfate" => "Twisted Fate",
        "tf" => "Twisted Fate",
        "twitch" => "Twitch",
        "tw" => "Twitch",
        "udyr" => "Udyr",
        "godyr" => "Udyr",
        "urgot" => "Urgot",
        "urgod" => "Urgot",
        "urgut" => "Urgot",
        "furgot" => "Urgot",
        "trashgot" => "Urgot",
        "giant enemy trashgot" => "Urgot",
        "varus" => "Varus",
        "veigar" => "Veigar",
        "veig" => "Veigar",
        "vel'koz" => "Vel'Koz",
        "vel koz" => "Vel'Koz",
        "velkoz" => "Vel'Koz",
        "vel" => "Vel'Koz",
        "koz" => "Vel'Koz",
        "vi" => "Vi",
        "viktor" => "Viktor",
        "vladimir" => "Vladimir",
        "vlad" => "Vladimir",
        "blood" => "Vladimir",
        "volibear" => "Volibear",
        "voli" => "Volibear",
        "bear" => "Volibear",
        "warwick" => "Warwick",
        "ww" => "Warwick",
        "wukong" => "Wukong",
        "monkey king" => "Wukong",
        "monkey" => "Wukong",
        "wu" => "Wukong",
        "xerath" => "Xerath",
        "xe" => "Xerath",
        "xin zhao" => "Xin Zhao",
        "xinzhao" => "Xin Zhao",
        "xin" => "Xin Zhao",
        "yasuo" => "Yasuo",
        "yas" => "Yasuo",
        "yorick" => "Yorick",
        "zac" => "Zac",
        "bloob" => "Zac",
        "zed" => "Zed",
        "ziggs" => "Ziggs",
        "bomb master" => "Ziggs",
        "bombmaster" => "Ziggs",
        "bomb" => "Ziggs",
        "zilean" => "Zilean",
        "zil" => "Zilean",
        "zyra" => "Zyra"
    );

    return (array_key_exists(strtolower($champion), $championNames)) ? $championNames[$champion] : $champion;
}

/**
 * @param string $channel
 * @param string $username
 * @param string $command
 * @param string $bot
 * @param string $id
 * @param string $region
 * @param string $addon
 *
 * Save each commands done on this API for stats purpoises.
 */
function saveStats($channel = "YOURCHANNEL", $username = "none", $command = "none", $bot = "none", $id = "YOURSUMMONERID", $region = "NA", $addon = "none") {
    $db = connect_db();
    $query = "INSERT INTO lolStats VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $rep = $db->prepare($query);
    $rep->execute(
        array('',
            ($channel == "") ? "YOURCHANNEL" : $channel,
            ($username == "") ? "none" : $username,
            ($command == "") ? "none" : $command,
            ($id == "") ? "YOURSUMMONERID" : $id,
            ($region == "") ? "NA" : $region,
            ($addon == "") ? "none" : $addon,
            ($bot == "") ? "Nightbot" : $bot,
            date('Y-m-d H:i:s', strtotime("now -6hours"))
        ));
}

/**
 * @param $channel
 * @param $command
 * @return array|mixed
 *
 * Return the output of a particular command for a channel.
 * Custom output requires the channel to be logged on the website of the API.
 */
function grabCommandOutput($channel, $command) {
    $db = connect_db();
    $query = "SELECT COUNT(id) FROM settingsApi WHERE LOWER(twitchUsername) = ?";
    $rep = $db->prepare($query);
    $rep->execute(array(strtolower($channel)));
    $foundUser = $rep->fetch(PDO::FETCH_NUM)[0];

    $data = array();

    if($foundUser) {
        $query = "SELECT data FROM settingsApi WHERE LOWER(twitchUsername) = ?";
        $rep = $db->prepare($query);
        $rep->execute(array($channel));
        $data = $rep->fetch(PDO::FETCH_ASSOC);
    }

    $query = "SELECT response, variables FROM commands WHERE called = ?";
    $rep = $db->prepare($query);
    $rep->execute(array($command));
    $data["default"] = $rep->fetch(PDO::FETCH_ASSOC);

    return $data;
}

/**
 * @param $channel
 * @return array|null
 *
 * Grabs the settings of the API for a specific channel.
 */
function getApiSettings($channel) {
    $db = connect_db();
    $query = "SELECT summonerId, summonerName, region, season, lang FROM settingsApi WHERE LOWER(twitchUsername) = ?";
    $rep = $db->prepare($query);
    $rep->execute(array($channel));

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $donnees;
    }

    if(!empty($data)) {
        return $data;
    }

    return NULL;
}

/**
 * @return array
 *
 * Grabs the available regions.
 */
function getRegions() {
    $db = connect_db();
    $query = "SELECT platformId, region FROM regions";
    $rep = $db->prepare($query);
    $rep->execute();

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[$donnees['region']] = $donnees['platformId'];
    }

    return $data;
}

function getChampions() {
    $db = connect_db();
    $query = "SELECT * FROM champions";
    $rep = $db->prepare($query);
    $rep->execute();

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[$donnees['id']] = $donnees;
    }

    return $data;
}

/**
 * @param $array
 * @param string $search
 * @return array
 *
 * Quicksort sorting algorithm.
 */
function quicksort($array, $search = "") {
    if(count($array) < 2) {
        return $array;
    }

    $left = $right = array();
    reset($array);
    $pivot_key  = key($array);
    $pivot  = array_shift($array);

    foreach($array as $k => $v) {
        if($search != "") {
            if($v[$search] < $pivot[$search]) {
                $left[$k] = $v;
            }
            else {
                $right[$k] = $v;
            }
        }
        else {
            if($v < $pivot) {
                $left[$k] = $v;
            }
            else {
                $right[$k] = $v;
            }
        }
    }
    return array_merge(quicksort($left, $search), array($pivot_key => $pivot), quicksort($right, $search));
}

/**
 * @param $string
 *
 * Pretty straight forward.
 */
function far_dump($string) {
    echo '<pre>';
    var_dump($string);
    echo '</pre>';
}

/**
 * @return PDO
 *
 * Database connexion.
 */
function connect_db() {
    global $dbName;
    global $dbAccount;
    global $dbPassword;

    try {
        $db = new PDO('mysql:host=localhost;dbname=' . $dbName, $dbAccount, $dbPassword);
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    return $db;
}

/**
 * Dispatch which command has been called.
 */
switch($_GET['action']) {
    case 'rank' :
        saveStats($_GET['channel'], $_GET['user'], 'rank', $_GET['bot'], $_GET['id'], $_GET['region'], "none");
        getRank($_GET['id'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'streak' :
        saveStats($_GET['channel'], $_GET['user'], 'streak', $_GET['bot'], $_GET['id'], $_GET['region']);
        getStreak($_GET['id'], $_GET['lang'], $_GET['channel'], 'streak', $_GET['region']);
        break;

    case 'rankname' :
        saveStats($_GET['channel'], $_GET['user'], 'rankof', $_GET['bot'], $_GET['id'], $_GET['region'], $_GET['name']);
        getRankName($_GET['name'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'mostplayed' :
        saveStats($_GET['channel'], $_GET['user'], 'mostplayed', $_GET['bot'], $_GET['id'], $_GET['region']);
        getMostPlayed($_GET['id'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'bestperformance' :
        saveStats($_GET['channel'], $_GET['user'], 'winrate', $_GET['bot'], $_GET['id'], $_GET['region']);
        getBestPerformance($_GET['id'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'lastGame' :
        saveStats($_GET['channel'], $_GET['user'], 'lastgame', $_GET['bot'], $_GET['id'], $_GET['region']);
        lastGame($_GET['id'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'stats' :
        saveStats($_GET['channel'], $_GET['user'], 'stats', $_GET['bot'], $_GET['id'], $_GET['region'], $_GET['champion']);
        getStats($_GET['id'], $_GET['user'], $_GET['champion'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'pentakills' :
        saveStats($_GET['channel'], $_GET['user'], 'pentakills', $_GET['bot'], $_GET['id'], $_GET['region']);
        getPentakills($_GET['id'], 'totalPentaKills', $_GET['min'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'quadrakills' :
        saveStats($_GET['channel'], $_GET['user'], 'quadrakills', $_GET['bot'], $_GET['id'], $_GET['region']);
        getPentakills($_GET['id'], 'totalQuadraKills', $_GET['min'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'triplekills' :
        saveStats($_GET['channel'], $_GET['user'], 'triplekills', $_GET['bot'], $_GET['id'], $_GET['region']);
        getPentakills($_GET['id'], 'totalTripleKills', $_GET['min'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'doublekills' :
        saveStats($_GET['channel'], $_GET['user'], 'doublekills', $_GET['bot'], $_GET['id'], $_GET['region']);
        getPentakills($_GET['id'], 'totalDoubleKills', $_GET['min'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'currentqueue' :
        saveStats($_GET['channel'], $_GET['user'], 'queue', $_GET['bot'], $_GET['id'], $_GET['region']);
        getCurrentQueue($_GET['id'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'masteries' :
        saveStats($_GET['channel'], $_GET['user'], 'masteries', $_GET['bot'], $_GET['id'], $_GET['region'], $_GET['details']);
        getMasteriesDetails($_GET['id'], $_GET['lang'], $_GET['channel'], $_GET['region']);
        break;

    case 'championPoints' :
        saveStats($_GET['channel'], $_GET['user'], 'championPoints', $_GET['bot'], $_GET['id'], $_GET['region'], $_GET['champion']);
        getChampionPoints($_GET['id'], $_GET['lang'], $_GET['channel'], $_GET['region'], $_GET['champion']);
        break;

    case 'addQuote' :
        addQuote($_GET['citation'], $_GET['user'], $_GET['userLevel'], $_GET['channel']);
        break;

    case 'deleteQuote' :
        deleteQuote($_GET['channel'], $_GET['userLevel'], $_GET['quoteId']);
        break;

    case 'fetchQuote' :
        fetchQuote($_GET['channel'], $_GET['search']);
        break;

    case 'testing' :
        getMostPlayed2('40579311', 'en', 'trahanqc', 'na');
        break;
}
