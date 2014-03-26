<?php

if($_SERVER['HTTPS'] != "on"){ // if there was no secure connection, redirect to https version
  $_SERVER['FULL_URL'] = 'https://';
  if($_SERVER['SERVER_PORT']!='80')
    $_SERVER['FULL_URL'] .=  $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'];
  else
    $_SERVER['FULL_URL'] .=  $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
  if($_SERVER['QUERY_STRING']>' '){
    $_SERVER['FULL_URL'] .=  '?'.$_SERVER['QUERY_STRING'];
  }

  //echo $_SERVER['FULL_URL'];

  $delay = "5"; // 5 second delay
  echo "For security reasons encryption needs to be enabled! Make sure to accept any security alerts.<br>";
  echo "You will be redirected in 5 seconds. Please wait...<br><br>";
  echo "If redirection does not work please click <a href=".$_SERVER['FULL_URL'].">here</a>.";

  echo '<meta http-equiv="refresh" content="'.$delay.';url='.$_SERVER['FULL_URL'].'">';

  exit();
}
