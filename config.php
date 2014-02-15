<?php
  /**
   * RARflixTools - Addons/Utils for the Roku/Plex - RARflix channel
   * @link https://github.com/ljunkie/RARflixTools/
   * @author Rob Reed (ljunkie)
   * @copyright 2013-2014 Rob Reed
   * @license Mit Style
   *
   *  Created: 2013-12-27
   * Modified: 2014-02-15
   *
   */

  /* 
   * Do NOT edit -- these should not need changing.
   *
   * RARflixTools are only supported to run on the LOCAL PMS server. 
   *
   */

error_reporting(0); // build-in webserver does not play well if there are ANY errors

define('PMS_IP'  , '127.0.0.1');
define('PMS_PORT', '32400');

define('INDICATOR', 'CHECK'); 
//define('INDICATOR', 'DOT'); 

define('VERSION', '0.0.9');

?>
