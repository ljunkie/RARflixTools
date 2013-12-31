<?php

  /**
   * RARflixTools - Addons/Utils for the Roku/Plex - RARflix channel
   * @link https://github.com/ljunkie/RARflixTools/
   * @author Rob Reed (ljunkie)
   * @copyright 2013 Rob Reed
   * @lincense    Mit Style
   *
   *  Created: 2013-12-27
   * Modified: 2013-12-30
   *
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
  if (!isset($progress)) $progress=0;
  
  // continue with a valid image
  if (!empty($url)) {
    $base_dir = dirname(__FILE__);

    // image size ( cropping is possible -- not needed )
    $size = getimagesize($url);
    $dst_x = 0;   // X-coordinate of destination point. 
    $dst_y = 0;   // Y --coordinate of destination point. 
    $src_x = 0;   // Crop Start X position in original image
    $src_y = 0;   // Crop Srart Y position in original image
    $img_w = $size[0];
    $img_h = $size[1];

    // 
    $im = imagecreatefromjpeg($url);
    
    // colors 
    $orange_alpha = imagecolorallocatealpha($im, 249, 159, 27, 40);
    $white_alpha  = imagecolorallocatealpha($im, 255, 255, 255, 40);
    $black_alpha  = imagecolorallocatealpha($im, 32,32,32,40);
    $red_alpha    = imagecolorallocatealpha($im, 255, 0, 0, 40);
    $white        = imagecolorallocate($im, 255, 255, 255);
    $grey         = imagecolorallocate($im, 128, 128, 128);
    $black        = imagecolorallocate($im, 0, 0, 0);

    // font
    $font_dir = $base_dir . '/fonts/';
    $style = "din1451alt.ttf";
    $font = $font_dir.$style;
    $font_size = 18;
    
    // this could use some work
    
    $pad_top=.35;  // we have to pad the top due to the Plex/Channel stretching images to fit
    $pad_right=.2; // same goes for padding on the right
    
    // Portrait
    if ($img_h/$img_w > 1.4) {
      $pad_top=.35;
      $pad_right=.2;
    }
    // 16x9
    else if ($img_w/$img_h > 1.5) {
      $pad_top=.35;
      $pad_right=.2;
    }
    // landscape(ish)
    else {
      $pad_top= 1;
      $pad_right=.2;
    }
    
    /* deprecated - // calculate the width of bar indicator  
    $max = 100;
    $progress_width = round(($progress * ($size[0]-3)) / 100);  
    $full_width = round((100 * ($size[0]-3)) / 100);  
    */
    
    // this are also the bare mininums ( if the font size is smaller )
    $min_font_size  = 16;
    $min_bar_height = 25;
    
    $default_bar_height = 30;
    $default_dot_size   = 18;
    $default_check_size = 18;
    
    $bar_height = $default_bar_height; // progress bar ( now background of progress/check mark )
    $dot_size   = $default_dot_size;
    
    // some bad math to allow random sizes
    // if image is smaller than expected
    $expected_width = 200;
    if ($img_w/$expected_width < 1) {
      $mp = $img_w/$expected_width;
      $dot_size   = ceil($dot_size*$mp);
      $bar_height = ceil($bar_height*$mp);
      $font_size  = ceil($font_size*$mp);
    }
    
    // if image is larger than expected
    if ($img_w/$expected_width > 1.2) {
      $mp = $img_w/$expected_width;
      $dot_size   = ceil($dot_size*$mp);
      $bar_height = ceil($bar_height*$mp);
      $font_size  = ceil($font_size*$mp);
    }
    
    // if font size is too small.. go with defaults
    $isMin = false;
    if ($font_size < $min_font_size or $bar_height < $min_bar_height) {
      $isMin = true;
      $font_size = $min_font_size;
      $mp = $min_font_size/$font_size;
      $dot_size   = $default_dot_size;
      $bar_height = $min_bar_height;
    }
    
    $text_height = $bar_height*.85;
    
    // fill the indicator bar ( x deprecated - fixed width )
    // $x1 = 0;
    // $x2 = $progress_width;
    $y1 = 0+($bar_height*$pad_top);
    $y2 = $bar_height+($bar_height*$pad_top);

    $check_box_size = $bar_height+1;
    
    // vertical center of progress bar for dot
    $dot_center = $y1+(($y2-$y1)/2);
    
    /***  progress area  -- we will still setup the area to keep the formatting consistent even if we don't have progress set **/
    $pos_right =  $img_w-($img_w*$pad_right);
    
    // check padding and width ( overrun )
    $right_padding = ($check_box_size*$pad_right);
    $ov_width = $pos_right+$check_box_size+$right_padding; // overlay width
    if ($ov_width >= $img_w) { $pos_right = $pos_right - ( $ov_width - $img_w); }
    

    // fill in the area/text with the progress
    $text = round($_GET['progress']) . "%";
    
    //testing
    $pos_left = $pos_right-($check_box_size*1.5);
    
    if ($isMin and $progress >= 10 and $progress != 11) { $text_mp = 1.4;  }
    else if ($progress >= 10)       { $text_mp = 1.25; } 
    else                            { $text_mp = 1.1;  }
    
    $text_left = $pos_right-($check_box_size*$text_mp);
    
    if(!isset($_GET['watched']) or $_GET['watched'] == 0) {
      $pos_left  = $pos_left+$check_box_size;
      $pos_right = $pos_right+$check_box_size;
      $text_left = $text_left+$check_box_size;
    }
    
    if ($progress > 0 and $progress < 100) {
      $fg_color = $white;
      $bg_color = $black_alpha;
      imagefilledrectangle($im, $pos_left, $y1, $pos_right, $y2, $bg_color);  
      imagettftext($im, $font_size, 0, $text_left, $text_height+($text_height*$pad_top), $fg_color, $font, $text);
    }
    

    /**  Watched status ( DOT or Check Mark ) **/
    imagealphablending($im,true);
    if (INDICATOR == "DOT") {
      // half dot if partially watched
      if ($pos_right) {	$pos_left=$pos_right; }
      else            { $pos_left =  $img_w-($img_w*$pad_right); }
      $pos_left = $pos_left*(1.1);
      
      if ($progress > 0 ) {	imageSmoothArc ($im ,  $pos_left , $dot_center  , $dot_size , $dot_size ,  array( 255, 165, 0, 5 ),  30, M_PI/2 ); }
      // full dot if NOT watched ( plex style )
      else if(!isset($_GET['watched']) or $_GET['watched'] == 0) { imageSmoothArc ($im ,  $pos_left, $dot_center  , $dot_size , $dot_size ,  array( 255, 165, 0, 5 ),  M_PI/2, 0 ); }
      
    }
    
    if (INDICATOR == "CHECK") {
      // mark it if watched
      if(isset($_GET['watched']) and $_GET['watched'] == 1) {
	$overlay = imagecreatefrompng('images/check-mark-512.png');
	// use the last right position if set for the new left
	if ($pos_right) { $pos_left = $pos_right; }
	else            { $pos_left = $img_w-($img_w*$pad_right); }
	$pos_right =  $pos_left+$check_box_size;
	
	$right_padding = ($check_box_size*$pad_right);
	$ov_width = $pos_right+$right_padding; // overlay width
	if ($ov_width > $img_w) {
	  $pos_right = $pos_right - ($ov_width - $img_w);
	  $pos_left  = $pos_left  - ($ov_width - $img_w);
	}
	
	imagefilledrectangle($im, $pos_left, $y1, $pos_right, $y2, $orange_alpha);        
	imagecopyresampled($im, $overlay, $pos_left, $y1, 0,0, $check_box_size, $check_box_size,  512, 512);  
      }
    }
    
    
    /**  Don't forget to output a correct header **/
    header('Content-Type: image/jpg');
    imagejpeg($im);
  }
}

?>
