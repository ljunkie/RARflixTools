RARflixTools
============

Supplemental Tools for RARflix ( a modified Plex chanel for the Roku )

* Currently this enable overlays on Posters. We can now have Watched/Progress inidcators
* more tools may come later...


#Requirements

* RARflixDev v3.0.8+
* Webserver (apache/nginx/etc) running on the same host as the PMS (Plex Media Server)
* root path for web server /RARflixTools
* Webserver listening on 32499
* PHP 5.x
* PHP-GD


#Install
* download the zip from github
* install a webserver on your PMS server
* set the webserver to listen on port 32499
* unzip RARflixTools dir in your webroot 
* if your webroot is /var/www/html then you should have /var/www/html/RARflixTools/
* CASE matters -- i.e. RARflixTools

# Test your Install
* Use a webbrowser and visit: http://[yourPMSip]:32499/RARflixTools  
* must return a json result
```
{"rarflix":{"PosterTranscoder":true,"PMSaccess":true,"PosterTranscoderUrl":"http:\/\/youPMSip:32499\/RARflixTools\/poster.php"}}
```

# Using with RARflixDev v3.0.8+
* There is nothing you have to do (as of now). RARflixDev will noticie your server has RARflixTools installed and will start using the tools. If for some reason you don't want the tools enabled, just remove this installation or rename RARflixTools to RARflixTools.disabled. A channel restart is required too. 


# Examples - Screenshots ( Poster Indicators )

![home screen](https://raw.github.com/ljunkie/RARflixTools/master/examples/PosterIndicators/1.home_screen.jpg)

![all seasons](https://raw.github.com/ljunkie/RARflixTools/master/examples/PosterIndicators/2.all_seasons.jpg)

![all seasons season](https://raw.github.com/ljunkie/RARflixTools/master/examples/PosterIndicators/5.all_seasons_season.jpg)

![episodes](https://raw.github.com/ljunkie/RARflixTools/master/examples/PosterIndicators/3.all_seasons_episodes.jpg)

![episode preplay](https://raw.github.com/ljunkie/RARflixTools/master/examples/PosterIndicators/4.all_seasons_episodes_preplay.jpg)

![all movies](https://raw.github.com/ljunkie/RARflixTools/master/examples/PosterIndicators/6.movie_rows.jpg)

![movie preplay](https://raw.github.com/ljunkie/RARflixTools/master/examples/PosterIndicators/7.movie_preplay.jpg)
