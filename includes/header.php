<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Custom League of Legends commands for multiple Twitch bots">
<meta name="keywords" content="Twitch,API,Trahan,Trahanqc,Trahanqc's API,Nightbot,League of Legends,lol,custom,commands,streamer">
<meta name="author" content="Marc-André Trahan, trahanqc">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../font-awesome/css/font-awesome.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/css/tether-theme-arrows-dark.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/css/tether-theme-arrows.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/css/tether-theme-basic.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/css/tether.min.css"/>

<?php
$statsCommands = getDateCommandNumber();
$messageGlobal = "The " . number_format($statsCommands['number'], 0, ".", ",") . "th command has been used on <a href='https://twitch.tv/" . $statsCommands['channel'] . "'>" . $statsCommands['channel'] . "'s</a> stream and it took " . $statsCommands['days'] . " days to reach that! :)";
?>