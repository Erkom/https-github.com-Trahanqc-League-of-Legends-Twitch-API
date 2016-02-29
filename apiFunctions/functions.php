<?php
function checkConnect() {
    if(isset($_SESSION['username'])) {
        return getAllSettings($_SESSION['username']);
    }

    return array();
}

function loginUser($username, $code, $access_token) {
    $db = connect_db();
    $query = "SELECT id FROM settingsApi WHERE LOWER(twitchUsername) = ?";
    $rep = $db->prepare($query);
    $rep->execute(array($username));
    $foundUser = $rep->fetch(PDO::FETCH_NUM)[0];

    if($foundUser != 0) {
        $query = "UPDATE settingsApi SET code = ?, access_token = ?, lastUsed = ? WHERE LOWER(twitchUsername) = ?";
        $rep = $db->prepare($query);
        $rep->execute(
            array($code,
                $access_token,
                date('Y-m-d H:i:s', strtotime('now -6hours')),
                $username
                ));
        $uid = $foundUser;
    }
    else {
        $query = "INSERT INTO settingsApi VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $rep = $db->prepare($query);
        $rep->execute(
            array('',
                $username,
                $code,
                $access_token,
                json_encode(array()),
                'YOURSUMMONERID',
                'YOURSUMMONERNAME',
                'YOURREGION',
                'SEASON2016',
                'en',
                date('Y-m-d H:i:s', strtotime('now -6hours'))
                ));
        $uid = $db->lastInsertId();
    }

    $_SESSION['uid'] = $uid;
    $_SESSION['username'] = $username;
}

function getAllSettings($username) {
    $db = connect_db();
    $query = "SELECT * FROM settingsApi WHERE LOWER(twitchUsername) = ?";
    $rep = $db->prepare($query);
    $rep->execute(array($username));

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $donnees;
    }

    return $data;
}

function getMilestones() {
    $db = connect_db();
    $query = "SELECT * FROM milestones";
    $rep = $db->prepare($query);
    $rep->execute();

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[$donnees['milestone']] = $donnees['achievement'];
    }

    return $data;
}

function getStatsUsername($username) {
    $db = connect_db();

    $stats = array();

    /**
     * Gather all commands that has been done for a channel
     */

    $query = "SELECT COUNT(*) FROM lolStats WHERE LOWER(channelName) = ?";
    $rep = $db->prepare($query);
    $rep->execute(array($username));
    $stats["allCommands"] = $rep->fetch(PDO::FETCH_NUM)[0];

    /**
     * Gather most used commands for a channel
     */
    $query = "SELECT COUNT(*) as nbCommand, command FROM lolStats WHERE LOWER(channelName) = ? GROUP BY command ORDER BY nbCommand DESC, command ASC";
    $rep = $db->prepare($query);
    $rep->execute(array($username));

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $stats["mostUsed"][$donnees['command']] = $donnees['nbCommand'];
    }

    /**
     * Gather user who uses the most commands
     */
    $query = "SELECT COUNT(*) as nbCommand, username FROM lolStats WHERE LOWER(channelName) = ? GROUP BY username ORDER BY nbCommand DESC, username ASC";
    $rep = $db->prepare($query);
    $rep->execute(array($username));

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $stats["mostUsername"][$donnees['username']] = $donnees['nbCommand'];
    }

    /**
     * Gather most commands per day
     */
    $query = "SELECT COUNT(*) as nbCommands, dateUsed FROM lolStats WHERE LOWER(channelName) = ? GROUP BY dateUsed ORDER BY nbCommands DESC, dateUsed DESC";
    $rep = $db->prepare($query);
    $rep->execute(array($username));

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $stats["mostPerDay"][$donnees["dateUsed"]] = $donnees['nbCommands'];
    }

    /**
     * Milestones
     */
    $query = "SELECT username, dateUsed FROM lolStats WHERE LOWER(channelName) = ? ORDER BY id ASC";
    $rep = $db->prepare($query);
    $rep->execute(array($username));

    $count = 1;
    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        switch($count) {
            case 1 : $stats["milestones"]["First"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 50 : $stats["milestones"]["50th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 100 : $stats["milestones"]["100th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 200 : $stats["milestones"]["200th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 300 : $stats["milestones"]["300th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 400 : $stats["milestones"]["400th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 500 : $stats["milestones"]["500th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 1000 : $stats["milestones"]["1000th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
            case 10000 : $stats["milestones"]["10000th"] = '<strong>' . $donnees['username'] . '</strong> on <strong>' . format_date($donnees['dateUsed']) . '</strong>'; break;
        }
        $count++;
    }

    return $stats;
}

function getStatsAdmin() {
    $db = connect_db();
    $query = "SELECT DISTINCT(channelName) FROM lolStats ORDER BY channelName ASC";
    $rep = $db->prepare($query);
    $rep->execute();

    $stats = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $stats[$donnees['channelName']] = getStatsUsername($donnees['channelName']);
    }

    return $stats;
}

function getSettingsAdmin() {
    $db = connect_db();
    $query = "SELECT twitchUsername, summonerName, region, season, lang FROM settingsApi ORDER BY twitchUsername ASC";
    $rep = $db->prepare($query);
    $rep->execute();

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[$donnees['twitchUsername']] = $donnees['summonerName'] . ' &#8212; ' . $donnees['region'] . ' &#8212; ' . $donnees['season'] . ' &#8212; ' . $donnees['lang'];
    }

    return $data;
}

function getStatsGlobal() {
    $db = connect_db();

    $stats = array();

    /**
     * Gather all the commands
     */
    $query = "SELECT COUNT(*) FROM lolStats";
    $rep = $db->prepare($query);
    $rep->execute();
    $stats["nbCommands"] = $rep->fetch(PDO::FETCH_NUM)[0];

    /**
     * Gather all the commands for each channel
     */
    $query = "SELECT COUNT(*) as nbCommands, channelName FROM lolStats GROUP BY channelName ORDER BY nbCommands DESC, channelName ASC";
    $rep = $db->prepare($query);
    $rep->execute();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $stats["channels"][$donnees['channelName']] = $donnees['nbCommands'];
    }

    /**
     * Gather all the commands for each day
     */
    $query = "SELECT COUNT(*) as nbCommands, dateUsed FROM lolStats GROUP BY dateUsed ORDER BY dateUsed DESC";
    $rep = $db->prepare($query);
    $rep->execute();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $stats["commandsPerDay"][$donnees['dateUsed']] = $donnees["nbCommands"];
    }

    return $stats;
}

function format_date($date) {
    $newDate = date('F d, Y', strtotime($date));
    $newDate = explode(' ', $newDate);
    //$newDate[1] = moisFr($newDate[1]);
    return implode(' ', $newDate);
}

function getCommands($index = "") {
    $db = connect_db();
    $query = "SELECT * FROM commands ORDER BY name ASC";
    $rep = $db->prepare($query);
    $rep->execute();

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        if($index != "") {
            $data[$donnees[$index]] = $donnees;
        }
        else {
            $data[] = $donnees;
        }
    }

    return $data;
}

function getRegions() {
    $db = connect_db();
    $query = "SELECT platformId, region FROM regions";
    $rep = $db->prepare($query);
    $rep->execute();

    $data = array();

    while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $donnees;
    }

    return $data;
}

function updateSummonerName($summonerName, $region, $summonerId, $season, $lang) {
    if(isset($_SESSION['username'])) {
        $db = connect_db();
        $query = "UPDATE settingsApi SET summonerId = ?, summonerName = ?, region = ?, season = ?, lang = ? WHERE id = ?";
        $rep = $db->prepare($query);
        $rep->execute(array($summonerId, $summonerName, $region, $season, $lang, $_SESSION['uid']));
        return true;
    }
    else {
        return "Your session has expired.  Please relog";
    }
}

function fetchMessages($category = "all") {
    $db = connect_db();
    $query = "SELECT COUNT(id) FROM blogPosts";
    $query .= ($category != "all") ? " WHERE categoryId = " . $category : "";
    $rep = $db->prepare($query);
    $rep->execute();
    $nb = $rep->fetch(PDO::FETCH_NUM)[0];

    $query = "SELECT p.id, p.title, p.twitchUsername, p.views, p.datePosted
            FROM blogPosts p
            LEFT JOIN blogPostsComments bpc ON bpc.postId = p.id
            LEFT JOIN blogComments c ON c.id = bpc.commentId
            INNER JOIN blogCategory bc ON bc.id = p.categoryId";
    $query .= ($category != "all") ? " WHERE categoryId = " . $category : "";
    $query .= " ORDER BY p.datePosted DESC";
    $rep = $db->prepare($query);
    $rep->execute();

    $max = 16;

    if($nb != 0) :
    ?>
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Created by</th>
                    <th>Views</th>
                    <th>Activity</th>
                </tr>
            </thead>
            <tbody>
                <?php while($donnees = $rep->fetch(PDO::FETCH_ASSOC)) : ?>
                    <tr>
                        <td><a href="posts-<?= $donnees['id']; ?>"><?= $donnees['title']; ?></a></td>
                        <td><?= $donnees['twitchUsername']; ?></td>
                        <td><?= $donnees['views']?></td>
                        <td><time class="timeago" datetime="<?= $donnees['datePosted']; ?>"><?= format_date($donnees['datePosted']); ?></time></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php
        if($nb < $max) :
            for($x = $nb ; $x < $max; $x++) :
            ?>
                <div class="blank"></div>
            <?php
            endfor;
        endif;
    else : ?>
        <p>There is currently no messages in this category</p>

        <?php for($x = 0; $x < 18; $x++): ?>
            <div class="blank"></div>
        <?php
        endfor;
    endif;
}

function fetchPost($id) {
    $id = (int)$id;
    $db = connect_db();
    $query = "SELECT p.*, c.category FROM blogPosts p INNER JOIN blogCategory c ON c.id = p.categoryId WHERE p.id = ?";
    $rep = $db->prepare($query);
    $rep->execute(array($id));
    $post = $rep->fetch(PDO::FETCH_ASSOC);

    $query2 = "SELECT p.title, p.twitchUsername, p.views, p.datePosted
            FROM blogPosts p
            LEFT JOIN blogPostsComments bpc ON bpc.postId = p.id
            LEFT JOIN blogComments c ON c.id = bpc.commentId
            INNER JOIN blogCategory bc ON bc.id = p.categoryId
            WHERE p.id = ?
            ORDER BY p.datePosted ASC, c.datePosted ASC";
    ?>
    <div class="post">
        <div class="post-title">
            <h3><?= $post['title']; ?> &#8212; <small class="text-muted"><?= $post['category']; ?></small></h3>
        </div>

        <div class="blank"></div>

        <div class="post-content">
            <div class="post-details">
                <?= $post['twitchUsername']; ?> <br>
                <time class="timeago" datetime="<?= $post['datePosted']; ?>"><?= format_date($post['datePosted']); ?></time>
            </div>

            <div class="post-message">
                <?= nl2br($post['message']); ?>
            </div>
        </div>
    </div>
    <?php
}

function addBlogPost($title, $category, $message, $username) {
    $db = connect_db();
    $query = "INSERT INTO blogPosts VALUES(?, ?, ?, ?, ?, ?, ?)";
    $rep = $db->prepare($query);
    $rep->execute(
        array('',
            $username,
            $category,
            $title,
            $message,
            0,
            date('Y-m-d H:m:i', strtotime("now -6hours"))
            ));
    return $db->lastInsertId();
}

function quicksort($array, $search = "") {
    if( count( $array ) < 2 ) {
        return $array;
    }
    $left = $right = array( );
    reset( $array );
    $pivot_key  = key( $array );
    $pivot  = array_shift( $array );
    foreach( $array as $k => $v ) {
        if($search != "") {
            if( $v[$search] < $pivot[$search] )
                $left[$k] = $v;
            else
                $right[$k] = $v;
        }
        else {
            if( $v < $pivot )
                $left[$k] = $v;
            else
                $right[$k] = $v;
        }
    }
    return array_merge(quicksort($left, $search), array($pivot_key => $pivot), quicksort($right, $search));
}