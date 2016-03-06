<?php
$realUrl = $_SERVER['REQUEST_URI'];
$realUrl = explode('/', $realUrl);
$realUrl = $realUrl[3];
$realUrl = explode('?', $realUrl);
$realUrl = $realUrl[0];
?>
<ul class="nav navbar-nav side-nav">
    <li <?= ($realUrl == 'dashboard') ? 'class="active"' : ''; ?>>
        <a href="dashboard"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
    </li>
    <?php if(!empty($user)) : ?>
        <li <?= ($realUrl == 'settings') ? 'class="active"' : ''; ?>>
            <a href="settings"><i class="fa fa-fw fa-cogs"></i> Settings</a>
        </li>
    <?php endif; ?>
    <?php if(!empty($user) && $user[0]['twitchUsername'] == 'trahanqc') : ?>
        <li <?= ($realUrl == 'stats') ? 'class="active"' : ''; ?>>
            <a href="stats"><i class="fa fa-fw fa-bar-chart"></i> Stats</a>
        </li>
    <?php endif; ?>
    <li <?= ($realUrl == 'install') ? 'class="active"' : ''; ?>>
        <a href="install"><i class="fa fa-fw fa-wrench"></i> Install guide</a>
    </li>
    <li <?= ($realUrl == 'commands-generator') ? 'class="active"' : ''; ?>>
        <a href="commands-generator"><i class="fa fa-fw fa-code"></i> Commands generator</a>
    </li>
    <li <?= ($realUrl == 'commands-list') ? 'class="active"' : ''; ?>>
        <a href="commands-list"><i class="fa fa-fw fa-terminal"></i> Commands list</a>
    </li>
    <li <?= ($realUrl == 'patch-notes') ? 'class="active"' : ''; ?>>
        <a href="patch-notes"><i class="fa fa-fw fa-cog"></i> Patch notes</a>
    </li>
    <li <?= ($realUrl == 'forum' || $realUrl == 'add-message' || preg_match("/posts-([0-9])+/", $realUrl)) ? 'class="active"' : ''; ?>>
        <a href="forum"><i class="fa fa-fw fa-ticket"></i> Forum <span class="label label-default"></span></a>
    </li>
    <li <?= ($realUrl == 'donation') ? 'class="active"' : ''; ?>>
        <a href="donation"><i class="fa fa-fw fa-money"></i> Donation</a>
    </li>
</ul>
