<?php

/* Redirect to HTTPS and refuse to serve HTTP */
if($_SERVER['HTTPS'] != "on"){
  $_SERVER['FULL_URL'] = 'https://';
  if($_SERVER['SERVER_PORT']!='80')
    $_SERVER['FULL_URL'] .=  $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'];
  else
    $_SERVER['FULL_URL'] .=  $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
  if($_SERVER['QUERY_STRING']>' '){
    $_SERVER['FULL_URL'] .=  '?'.$_SERVER['QUERY_STRING'];
  }

  header("Location: " . $_SERVER['FULL_URL']);
  exit();
} else {
  /* Only use HTTPS for the next year */
  header("Strict-Transport-Security: max-age=31536000");
}

?>
