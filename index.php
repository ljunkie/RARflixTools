<?php

  /**
   * RARflixTools - Addons/Utils for the Roku/Plex - RARflix channel
   * @link https://github.com/ljunkie/RARflixTools/
   * @author Rob Reed (ljunkie)
   * @copyright 2013 Rob Reed
   * @lincense    Mit Style
   *
   *  Created: 2013-12-27
   * Modified: 2014-01-14
   *
   */

require_once 'config.php';

// include the tools -- so we can for one parse and validate
$tools = array(
	       "PosterTranscoder" => 'poster.php',
	       );

foreach ($tools as $tool) {
  include $tool;
}

$process = curl_init('http://'.PMS_IP.':'. PMS_PORT);
curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml; charset=utf-8', 'Content-Length: 0', 'X-Plex-Client-Identifier: RARflixTools'));
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_TIMEOUT, 5);
curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($process);

$authCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
if($authCode == 200) {
  $uri_base = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $uri_base = preg_replace("/\/$/",'',$uri_base);
  $arr = array(); // json_arr container
  
  // set some defaults
  $arr["rarflix"]['PosterTranscoder'] = false;
  $arr["rarflix"]['PMSaccess'] = false;
  $arr["rarflix"]['Version'] = VERSION;

  // is the PosterTranscoder available?
  if (function_exists("gd_info")) {
    if (gd_info()['JPEG Support'] == true and defined('PosterTranscoder') and PosterTranscoder == true) {
      $arr["rarflix"]['PosterTranscoder'] = true;
      $arr["rarflix"]['PosterTranscoderType'] = INDICATOR;
      $arr["rarflix"]['PosterTranscoderUrl'] = $uri_base . '/' . $tools['PosterTranscoder'];
    } else {
      $arr["error"]['PosterTranscoder'] = 'php-gd missing JPEG support';
    }
  } else {
    $arr["error"]['PosterTranscoder'] = 'php-gd not installed?';
  }
  
  // check to see if we have access to the PMS 
  //  we might want to try an load a photo for a better check
  $xml = simplexml_load_string($data);
  if (!empty($xml['machineIdentifier']))  $arr["rarflix"]['PMSaccess'] = true;
  
  // return json
  print json_encode($arr);
} else {
  if(curl_errno($process)) {
    $curlError = curl_error($process);
    echo $curlError;
    curl_close($process);
  } else {
    print $data;
  }
}
 
?>
