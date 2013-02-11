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
if (isset($_SESSION['login']) && $_SESSION['full_path'] == realpath(".") )			// logged in
{
    if ($_SESSION['acc_type'] == 'admin')	// admin users
    {
        show_links($left_links=array("View Log", "advanced.php?op=view_log", "Submit History", "advanced.php?op=sub_hist", "Rendezvous History", "advanced.php?op=ren_hist",
            "SQL Query", "advanced.php?op=query", "Reset System", "advanced.php?op=reset"),
        $right_links=array("Logout ".$_SESSION['login']." (admin)", "index.php?op=logout"), $_GET['op']);
    }
    else			// simple users
    {
        show_links($left_links=array("Submit History", "advanced.php?op=sub_hist", "Rendezvous History", "advanced.php?op=ren_hist"),
        $right_links=array("Logout ".$_SESSION['login'], "index.php?op=logout"), $_GET['op']);
    }
}
else	// not logged in
{
    show_links($left_links=array("Login", "index.php?op=login"), $right_links=array(), $_GET['op']);
}

echo 	'<br><br>';
// safe mode check
if( ini_get('safe_mode') ){echo '<b>Warning:</b> PHP is running in SAFE MODE, which is known to cause problems with this site. To disable SAFE MODE contact your web server administrator.<br><br>';}


/*************  REST OF PAGE  *****************/

if(check_db())
{

    if (isset($_SESSION['login']) && $_SESSION['full_path'] == realpath(".") )			// logged in
    {
        if ($_SESSION['acc_type'] == 'user')	// simple user
        {
            /************* Normal Advanced Page *************/
            if ($_GET['op'] == '')
            {
                echo 'Welcome '.$_SESSION['login'].'!';
                echo ' You have the following options:<br><br>
                    <table>
                    <tr><td align="right"><b> Submit History: </b></td><td align="left">Select this option to view all of your submissions.</td></tr>
                    <tr><td align="right"><b> Rendezvous History: </b></td><td align="left">Select this option to view all of your previously booked rendezvous.</td></tr>
                    </table>
                    ';
            }

            /************* Submit History *************/
            if ($_GET['op'] == 'sub_hist')
            {
                echo '<b> Submit History: </b>';
                include ("txtDB/txt-db-api.php");
                $db = new Database("mydb");
                $query = 'select sub_ses_id, sub_time from submits where login = "'.$_SESSION['login'].'"';
                $rs = $db->executeQuery($query);
                if($rs->getRowCount() == 0)
                {
                    echo "You have never submitted any files.<br>";
                }
                else
                {
                    echo 'You have submitted '.$rs->getRowCount().' files.<br><br>';
                    echo '<table  cellpadding="5" cellspacing="0" class="blue">';
                    echo '<tr><th><b> Submit Session </b></th><th><b> Submission Time </b></th></tr>';
                    while($rs->next())
                    {
                        echo '<tr><td align="center">"';
                        $query = 'select title from submit_sessions where sub_ses_id = '.$rs->getCurrentValueByNr(0);
                        $rs2 = $db->executeQuery($query);
                        if($rs2->next())		// title found
                            echo $rs2->getCurrentValueByNr(0);
                        else
                            echo "unknown";
                        echo '" </td><td align="center">'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(1)).'</td></tr>';
                    }
                    echo "</table>";
                }

            }

            /************* Rendezvous History *************/
            if ($_GET['op'] == 'ren_hist')
            {

                echo '<b> Rendezvous History: </b>';
                include ("txtDB/txt-db-api.php");
                $db = new Database("mydb");
                $query = 'select ren_ses_id, ren_time, ren_slot from rendezvous where login = "'.$_SESSION['login'].'"';
                $rs = $db->executeQuery($query);
                if($rs->getRowCount() == 0)
                {
                    echo "You have never booked a rendezvous.<br>";
                }
                else
                {
                    echo 'You have booked '.$rs->getRowCount().' rendezvous.<br><br>';
                    echo '<table cellpadding="5" cellspacing="0" class="blue">';
                    echo '<tr><th><b> Rendezvous Session </b></th><th><b> Time </b></th><th><b> Slot </b></th></tr>';
                    while($rs->next())
                    {
                        echo '<tr><td align="center">"';
                        $query = 'select title from ren_sessions where ren_ses_id = '.$rs->getCurrentValueByNr(0);
                        $rs2 = $db->executeQuery($query);
                        if($rs2->next())		// title found
                            echo $rs2->getCurrentValueByNr(0);
                        else
                            echo "unknown";
                        echo '" </td><td align="center">'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(1)).'</td><td align="center">'.$rs->getCurrentValueByNr(2).'</td></tr>';
                    }
                    echo "</table>";
                }
            }
        }
        else	// admin
        {
            /************* Normal Submit Page *************/
            if ($_GET['op'] == '')
            {
                echo 'Welcome '.$_SESSION['login'].'!';
                echo ' You have the following options:<br><br>
                    <table>
                    <tr><td align="right"><b> View Log: </b></td><td align="left">View System Log.</td></tr>
                    <tr><td align="right"><b> Submit History: </b></td><td align="left">Get detailed information about all available Submit Sessions.</td></tr>
                    <tr><td align="right"><b> Rendezvous History: </b></td><td align="left">Get detailed information about all available Rendezvous Sessions.</td></tr>
                    <tr><td align="right"><b> SQL Query: </b></td><td align="left">Perform direct SQL Queries on the database.</td></tr>
                    <tr><td align="right"><b> Reset System: </b></td><td align="left">Deletes everything and resets the whole system! </td></tr>
                    </table>
                    ';
            }

            /************* Submit History *************/
            if ($_GET['op'] == 'view_log')
            {
                if (file_exists(DB_DIR."log.txt"))
                {
                    $temp_log = 'temp_log.txt';
                    $command = 'tac '.DB_DIR.'log.txt > /tmp/temp_log.txt';
                    passthru($command);

                    if ($fp = fopen(DB_DIR."log.txt", "r"))
                    {
                        echo '<b>System Log:</b>&nbsp;(';
                        echo exec('wc -l < '.DB_DIR.'log.txt');
                        echo ' entries )<br>';
                        echo '<textarea name="log" cols="80" rows="20" readonly="readonly">';

                        $fp = fopen("/tmp/temp_log.txt", "r");
                        while (!feof($fp))
                        {
                            echo fgets($fp);
                        }
                        fclose($fp);
                        echo '</textarea>';
                    }

                }
                else
                {
                    echo 'No log file found!';
                }

            }

            /************* Submit History *************/
            if ($_GET['op'] == 'sub_hist')
            {
                echo '<b> Submit History: </b>';
                $db = new Database("mydb");
                $query = 'select * from submit_sessions';
                $rs = $db->executeQuery($query);
                if($rs->getRowCount() == 0)
                {
                    echo "No Submit Sessions found in the database!.<br>";
                }
                else
                {
                    echo 'Found '.$rs->getRowCount().' Submit Sessions in the database.<br><br>';
                    include "php/print.php";
                    print_submits($rs);
                }
            }

            /************* Rendezvous History *************/
            if ($_GET['op'] == 'ren_hist')
            {
                echo '<b> Rendezvous History: </b>';
                $db = new Database("mydb");
                $query = 'select * from ren_sessions';
                $rs = $db->executeQuery($query);
                if($rs->getRowCount() == 0)
                {
                    echo "No Rendezvous Sessions found in the database!.<br>";
                }
                else
                {
                    echo 'Found '.$rs->getRowCount().' Rendezvous Sessions in the database.<br><br>';
                    include "php/print.php";
                    print_rendezvous($rs);
                }
            }

            /************* SQL Query *************/
            if ($_GET['op'] == 'query')
            {

                function query_form($query="")
                {
                ?>
                    <form name="form1" method="post" action="">
                            <b><font size = "4" >SQL Query : </font></b><br><br>
                            <textarea name="textarea" cols="50" rows="5" wrap="PHYSICAL"><?php echo "$query";?></textarea></td> <br><br>
                            <input type="submit" name="Submit" value="Submit">
                    </form>
                <?php
                }	// query_form

                if($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    include ("txtDB/txt-db-api.php");
                    if (!file_exists(DB_DIR . "mydb")) {		// Database doesn't exist
                        echo 'No Database Found!<br>Please constact your instructor or teaching assistants.<br>';
                    }
                    else
                    {
                        $query = stripslashes($_POST['textarea']);
                        $db = new Database("mydb");
                        $rs = $db->executeQuery($query);

                        echo "<b>Your SQL Query returned the following results:</b><br><br>";

                        //printing simple html
                        include "php/print.php";
                        print_table($rs);
                    }
                }
                else
                {
                    query_form();
                }

            }

            /************* Reset Database *************/
            if ($_GET['op'] == 'reset')
            {

                function reset_form()
                {
                ?>
                    <form name="reset_form" method="POST" action="">
                            <b>Are you sure you want to reset the System?</b><br>Warning: All database files will be deleted. <br><br>
                            <input name="yes_btn" type="submit" id="yes_btn" value="Reset">
                    </form>
                <?php
                }		//reset_form

                if($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    //include ("db.php");
                    reset_db();

                    // log the user out!
                    unset($_SESSION['login']);
                    unset($_SESSION['name']);
                    unset($_SESSION['acc_type']);

                    echo "<br>System was succesfully reset!<br>";
                    echo "Note: If you would like to delete the database directory (or this whole website) close this page and do it now.";

                }
                else
                {
                    reset_form();
                }

            }

        }
    }
    else		// not logged in
    {
        echo 'Not logged in! Please wait...';
        $delay=1;
        echo '<meta http-equiv="refresh" content="'.$delay.';url=index.php?op=login">';
    }

}

/************* End of page *************/
echo '</div>';	// content end
include("footer.inc.php");
echo '</div>';	// container end
echo '</body></html>';

?>
