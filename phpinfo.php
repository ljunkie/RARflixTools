<?php

if (function_exists("gd_info")) {
  print "<pre>";
  print_r(gd_info());
  var_dump(gd_info());
  print "<pre>";
} else {
  print "Warning: <b>php-gd</b> is *probably* not installed or enabled";
}

phpinfo();

?>
