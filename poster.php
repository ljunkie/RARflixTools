<?php

  /**
   * RARflixTools - Addons/Utils for the Roku/Plex - RARflix channel
   * @link https://github.com/ljunkie/RARflixTools/
   * @author Rob Reed (ljunkie)
   * @copyright 2013 Rob Reed
   * @lincense    Mit Style
   */

require_once 'config.php';
require_once ("inc/imageSmoothArc_optimized.php");
define('PosterTranscoder', true);

if (is_array($_GET)) {
  $thumb_vars = '';  
  foreach ($_GET as $key => $val) {
    if (preg_match("/progress/i",$key)) {
      $progress = $val;
    } elseif (preg_match("/watched/i",$key)) {
      $watched = 1;
    } elseif (preg_match("/thumb/i",$key)) {
      // set base thumb url ( replace any external IP as this will be local )
      $thumb_base = preg_replace("/^http:\/\/[^\/]+/",'http://'.PMS_IP.':'.PMS_PORT,$val);
    } else{
      // save any additional vars to append to the base_url
      $thumb_vars .= "&$key=$val";
    }
  }
  if (!empty($thumb_base) and !empty($thumb_vars))  $url = $thumb_base . $thumb_vars;

  if (!empty($url)) {
    $base_dir = dirname(__FILE__);

    $src_image = imagecreatefromjpeg($url);
    $size = getimagesize($url);
    $dst_image = imagecreatetruecolor($size[0],$size[1]);
    
    // colors 
    $orange_alpha = imagecolorallocatealpha($dst_image, 255, 165, 0, 10);
    $white_alpha = imagecolorallocatealpha($dst_image, 255, 255, 255, 30);
    $red = imagecolorallocatealpha($dst_image, 255, 0, 0, 40);
    $white = imagecolorallocate($dst_image, 255, 255, 255);
    $grey =  imagecolorallocate($dst_image, 128, 128, 128);
    $black = imagecolorallocate($dst_image, 0, 0, 0);



    // font
    $font_dir = $base_dir . '/fonts/';
    $style = "din1451alt.ttf";
    $font = $font_dir.$style;
    $font_size = 18;

    // image size ( cropping is possible )
    $dst_x = 0;   // X-coordinate of destination point. 
    $dst_y = 0;   // Y --coordinate of destination point. 
    $src_x = 0;   // Crop Start X position in original image
    $src_y = 0;   // Crop Srart Y position in original image
    $dst_w = $size[0];
    $dst_h = $size[1];
    $src_w = $dst_w;
    $src_h = $dst_h;
    
    
    // this needs some work
    $pad_top=.35;
    $pad_right=.2;

    // landscape(ish)
    // Portrait
    if ($src_h/$src_w > 1.4) {
      $pad_top=.35;
      $pad_right=.2;
    }
    // 16x9
    else if ($src_w/$src_h > 1.5) {
      $pad_top=.35;
      $pad_right=.2;
    } else {
      $pad_top= 1;
      $pad_right=.2;
    }
    
    // create the dst_image from the poster image
    imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    
    // calculate the width of bar indicator  
    $max = 100;
    $progress=0;
    if(isset($_GET['progress'])) $progress = $_GET['progress'];
    $progress_width = round(($progress * ($size[0]-3)) / 100);  
    $full_width = round((100 * ($size[0]-3)) / 100);  

    // some bad math to allow random sizes
    $expected_width = 200;
    
    // this are also the bare mininums ( if the font size is smaller )
    $min_font_size = 20;
    
    $default_bar_height = 30;
    $default_dot_size = 18;
    
    
    $bar_height = $default_bar_height;
    $dot_size = $default_dot_size;
    
    // if image is smaller than expected
    if ($size[0]/$expected_width < 1) {
      $mp = $size[0]/$expected_width;
      $dot_size   = ceil($dot_size*$mp);
      $bar_height = ceil($bar_height*$mp);
      $font_size  = ceil($font_size*$mp);
    }
    
    // if image is larger than expected
    if ($size[0]/$expected_width > 1.2) {
      $mp = $size[0]/$expected_width;
      $dot_size   = ceil($dot_size*$mp);
      $bar_height = ceil($bar_height*$mp);
      $font_size  = ceil($font_size*$mp);
    }
    
    // if font size is too small.. go with defaults
    if ($font_size < $min_font_size) {
      $font_size = $min_font_size;
      $mp = $min_font_size/$font_size;
      $dot_size   = $default_dot_size;
      $bar_height = $default_bar_height;
    }
    

    $text_height = $bar_height*.85;
    // fill the indicator bar
    $x1 = 0;
    $y1 = 0+($bar_height*$pad_top);
    $x2 = $progress_width;
    $y2 = $bar_height+($bar_height*$pad_top);

    // vertical center of progress bar for dot
    $center = $y1+(($y2-$y1)/2);
    // end
    


    // Watched status
    imagealphablending($dst_image,true);
    if (INDICATOR == "DOT") {
      // half dot if partially watched
      if ($progress > 0 ) {
	imageSmoothArc ($dst_image ,  $size[0]-($size[0]*$pad_right) , $center  , $dot_size , $dot_size ,  array( 255, 165, 0, 5 ),  30, M_PI/2 );
      }
      // full dot if NOT watched ( plex style )
      else if(!isset($_GET['watched']) or $_GET['watched'] == 0) {
	imageSmoothArc ($dst_image ,  $size[0]-($size[0]*$pad_right) , $center  , $dot_size , $dot_size ,  array( 255, 165, 0, 5 ),  M_PI/2, 0 );
      } 
    }

    if (INDICATOR == "CHECK") {
      // mark it if watched
      if(isset($_GET['watched']) and $_GET['watched'] == 1) {
	$overlay = imagecreatefrompng('images/check-mark-512.png');
	$pos_right =  $size[0]-($size[0]*$pad_right);
	
	// set the mininum height
	$h_w = $size[0]*.1358;
	if ($h_w < 25) $h_w = 25; // min height for checkbox is 20x20
	
	// verify the image will not overrun the right
	$diff = $size[0]-$pos_right;
	if ($diff < $h_w) $pos_right = $size[0] - ($h_w*1.1);

	$start = $size[0]*.94828;
	$end = $size[0]*.77589;

	
	if ($start-$end < $h_w) $end = $start-$h_w;
	//imagefilledrectangle($dst_image, $start, $y1, $end, $y2, $white_alpha);  
	imagecopyresampled($dst_image, $overlay, $pos_right, $y1*1.12, 0,0, $h_w, $h_w,  512, 512);  
      }
    }
    // end watched
 


    if ($progress > 0) {
      
      if ($progress >= 10) {
	$min_progress_width = ceil(38*$mp);
      } else {
	$min_progress_width = ceil(28*$mp);
      }
      if ($progress_width < $min_progress_width)  $x2 = $min_progress_width;
      
      imagefilledrectangle($dst_image, $x1, $y1, $x2, $y2, $white_alpha);  
      
      // text for indicator bar
      $text = round($_GET['progress']) . "%";
      //imagettftext($dst_image, 20, 0, $progress_width/2.001, 25, $grey, $font, $text);
      $text_left = $progress_width-($font_size*2);
      if ($text_left < 3) $text_left = 3;
      imagettftext($dst_image, $font_size, 0, $text_left, $text_height+($text_height*$pad_top), $black, $font, $text);
      // end
    }

   
    // Don't forget to output a correct header
    header('Content-Type: image/jpg');
    imagejpeg($dst_image);
  }
}

?>