# League-of-Legends-Twitch-API
I created this API for League of Legends Twitch streamers that wanted to have some commands on their bot to display some statistics about their League of Legends account.  To get stated, you can go on the website [http://gotme.site-meute.com/api/v1/dashboard](http://gotme.site-meute.com/api/v1/dashboard)


## Install Guide
To install any of those commands, you are required to use a bot that has an access to an Custom API.  I currently support Nightbot, Deepbot and hnlBot but there is many others that you could use.

#### Adding a command
To add a command, you can go into the [Command generator](http://gotme.site-meute.com/api/v1/commands-generator).  You will need to grab your League of Legends ID from that page and select which command you want to add into your bot.  Once every settings is done, generate the command and copy paste the output into your chat or your bot panel.

#### Login with the website
To have access to settings and multiple statistics, login with your Twitch account.  You will then be able to change the summoner name that the commands will be fetching from, the season to fetch from, etc. You will also be able to edit the output of these commands (translating in one way).

## Available commands
There is a lot of commands currently available through the application.  So see further notes on these commands, go check out the [commands list](http://gotme.site-meute.com/api/v1/commands-list)

#### !championPoints champion_name
Display your champion points with a champion and if a chest has been granted.
```
!championPoints tristana
> Champion level 5 (58190 points).  Chest granted!
```

#### !doublekills
Display the numbers of doublekills you have and with which champions.
```
!doublekills
> Ekko (22), Shaco (13), Malphite (4), Zac (3)
```

#### !lastgame
Display the statistics of your last ranked game.
```
!lastgame
> Last game won with Gragas 5/4/11 (4 KDA with 51.6% kill participation) 1 double kill
```

#### !masteries
Display your masteries for the current game. Note: The summoner must be in a game.
```
!masteries
> 12/18/0 (Thunderlord's Decree)
```

#### !mostplayed
Display the 5 most played champions in ranked games.
```
!mostplayed
> 1. Ekko (35), 2. Shaco (16), 3. Zac (9), 4. Malphite (9), 5. Brand (6)
```

#### !pentakills
Display the number of pentakills you have and with which champions.
```
!pentakills
> Ekko (1)
```

#### !quadrakills
Display the number of quadrakills you have and with which champions.
```
!quadrakills
> Ekko (2)
```

#### !queue
Display your current queue type. Note: The summoner must be in a game.
```
!queue
> Ranked 5v5 Draft Pick
```

#### !rank
Displays the current ranking of the summoner name.  It will display the LP and if your are in series, you're current performance in series as well.
```
!rank
> Gold V (100 LP) X O -
```

#### !rankof summoner_name
Displays the current ranking of the `summoner_name`.  It will display the LP and if your are in series, you're current performance in series as well.
```
!rankof trahanqc
> Gold V (100 LP) X O -
```

#### !stats champion_name
Display your current stats with `champion_name` in ranked games.
```
!stats ekko
> 57.1% [20/15]. KDA: 8.1/7.7/8.6 1 penta kill
```

#### !streak
Display your current winning/losing streak in ranked games.
```
!streak
> Win (1)
```

#### !triplekills
Display the number of triplekills you have and with which champions.
```
!triplekills
> Ekko (4), Malphite (1), Pantheon (1), Gragas (1)
```

#### !winrate
Display your overall winning pourcentage and your top 5 champions with 5 games or more in ranked games.
```
!winrate
> Overall : 48% Top 5 : 1. Blitzcrank (83.3%), 2. Brand (66.7%), 3. Ekko (57.1%), 4. Shaco (56.3%), 5. Morgana (40%)
```

## Questions/Suggestions/Problems
Feel free to contact me on [Twitch](http://www.twitch.tv/trahanqc/profile) or use the [forum](http://gotme.site-meute.com/api/v1/forum) on the API Website
