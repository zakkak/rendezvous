<?php

/*  Copyright (c) 2007-13, Michael K. Papamichael <papamixATgmail.com>
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 *
 *      * Redistributions of source code must retain the above copyright
 *        notice, this list of conditions and the following disclaimer.
 *      * Redistributions in binary form must reproduce the above copyright
 *        notice, this list of conditions and the following disclaimer in the
 *        documentation and/or other materials provided with the distribution.
 *      * Any redistribution, use, or modification is done solely for personal
 *        benefit and not for any commercial purpose or for monetary gain.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *  AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 *  ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 *  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 *  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *  INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *  CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *  POSSIBILITY OF SUCH DAMAGE.
 */

$start_php_time = microtime(true);	// only works in php5
//$start_php_time = strtok(microtime(), ' ') + strtok('');	// also works with php4
include("db.php");     // include txtDB
include("conf.php");   // settings
include("https_check.inc.php");  // check for https and redirect if necessary

if( substr(sprintf('%o', fileperms(DB_DIR)), -4) == '1777')		// check permissions of directory - temporary fix until suphp is installed
session_save_path(DB_DIR);
//session_save_path(".");
session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Submit-Rendezvous</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="SHORTCUT ICON" HREF="<?php echo $favicon_path;?>">
<link type="text/css" rel="stylesheet" href="theme/style.css">
<!--[if IE 5]>
<link rel="stylesheet" type="text/css" href="theme/ie5style.css">
<![endif]-->
<script type="text/javascript">
/* Current Server Time script (SSI or PHP)- By JavaScriptKit.com (http://www.javascriptkit.com) For this and over 400+ free scripts, visit JavaScript Kit- http://www.javascriptkit.com/ This notice must stay intact for use. */
var currenttime = '<?php print date("F d, Y H:i:s", time())?>' //PHP method of getting server date
var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December")
var serverdate=new Date(currenttime)
function padlength(what){var output=(what.toString().length==1)? "0"+what : what; return output}
function displaytime(){
serverdate.setSeconds(serverdate.getSeconds()+1)
var datestring=montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear()
var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds())
document.getElementById("servertime").innerHTML=datestring+" "+timestring}
window.onload=function(){setInterval("displaytime()", 1000)}
</script>
</head>
<body>
<div id="container"><div id="content">
<?php
include("header.inc.php");
include "php/show_links.php";

// Show menu depending on user status
if (isset($_SESSION['login']) && $_SESSION['full_path'] == realpath(".") )			// logged in
{
  show_links($left_links=array(),
        $right_links=array("Logout ".$_SESSION['login'], "index.php?op=logout"), $_GET['op']);
}
else	// not logged in
{
    show_links($left_links=array("Login", "index.php?op=login"), $right_links=array(), $_GET['op']);
}
?>

<br/>
<h4>Submit-Rendezvous v. 1.7.0<br/> by Michael K. Papamichael © 2007-13</h4>
To get a copy or contribute to the development of the project visit
<a href="https://github.com/papamix/submit_rendezvous">
  https://github.com/papamix/submit_rendezvous
</a>.
<br/>
To get a copy of the license click <a href="./LICENSE">here</a>.
<br/>
<br/>
Comments?
<script type="text/javascript" language=javascript>
<!--
email='papamix@'+'gmail.com';
document.write('<a href="mailto:' + email + '"><img valign="bottom" border="none" src="./theme/mail.png"></a>');
//-->
</script>
<noscript><a href="mailto:papamix (at) gmail (dot) com"><img valign="bottom" border="none" src="./theme/mail.png"></a></noscript>

<br/>
<br/>
<br/>
<br/>
<br/>

<small>Contibutors:
<br/>
<script type="text/javascript" language=javascript>
<!--
email='foivos@'+'zakkak.net';
document.write('<a href="mailto:' + email + '">Foivos S. Zakkak</a>');
//-->
</script>
<noscript><a href="mailto:foivos (at) zakkak (dot) net">Foivos S. Zakkak</a></noscript>
</small>

<?php
/************* End of page *************/
echo '</div>';	// content end
include("footer.inc.php");
echo '</div>';	// container end
echo '</body></html>';
?>
