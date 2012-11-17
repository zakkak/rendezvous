<?php

/*  Copyright (c) 2007-12, Michael K. Papamichael <papamixATgmail.com>
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
<title>Submit-Rendezvous created by Michael Papamichael &copy; 2007-12</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="SHORTCUT ICON" HREF="<?php echo $favicon_path;?>">
<link type="text/css" rel="stylesheet" href="theme/style.css">
<!--[if IE 5]>
<link rel="stylesheet" type="text/css" href="theme/ie5style.css">
<![endif]-->
<script type="text/javascript" src="js/calendarDateInput.js"></script>
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
    if ($_SESSION['acc_type'] == "admin")	// admin users
    {
        show_links($left_links=array("Create", "submit.php?op=create", "Edit", "submit.php?op=edit", "Review", "submit.php?op=review", "Close", "submit.php?op=close", "Delete", "submit.php?op=delete"), 
        $right_links=array("Logout ".$_SESSION['login']." (admin)", "index.php?op=logout", "Help", "index.php?op=help"), $_GET['op']);
    }
    else			// simple users
    {
        show_links($left_links=array("Submit a File", "submit.php?op=submit", "Review", "submit.php?op=review"), 
        $right_links=array("Logout ".$_SESSION['login'], "index.php?op=logout", "Help", "index.php?op=help"), $_GET['op']);
    }
}
else	// not logged in
{
    show_links($left_links=array("Login", "index.php?op=login"), $right_links=array("Help", "index.php?op=help"), $_GET['op']);
}
echo '<br><br>';
// safe mode check
if( ini_get('safe_mode') ){echo '<b>Warning:</b> PHP is running in SAFE MODE, which is known to cause problems with this site. To disable SAFE MODE contact your web server administrator.<br><br>';}


/*************  REST OF PAGE  *****************/

if(check_db())
{
    // Define functions
    function select_sub_fields($title="Select Submit Session:", $button_text='Continue')
    {
        $db = new Database("mydb");
        $query = "select sub_ses_id, title from submit_sessions order by sub_ses_id";
        $rs = $db->executeQuery($query); 
        if ($rs->getRowCount() != 0)
        {
            ?>
            <b><?php echo $title ?></b><br><br>
            <select name="sub_ses_id">
            <?php    
                while($rs->next())
                    echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
            ?>
            </select><br><br><br>
            <input name="review_btn" type="submit" id="btn" value="<?php echo $button_text ?>">
            <?php    
        }
        else
        {
            echo "There are no available Submit Sessions!<br><br>";
        }
    }		//select_sub_fields		

    if (isset($_SESSION['login']) && $_SESSION['full_path'] == realpath(".") )			// logged in
    {
        /************* SIMPLE USER *************/
        if ($_SESSION['acc_type'] == 'user')	// simple user
        {
            /************* Normal Submit Page *************/
            if ($_GET['op'] == '')		
            {
                echo 'Welcome '.$_SESSION['login'].'!';
                echo ' You have the following options:<br><br>
                    <table>
                    <tr><td align="right"><b> Submit a File: </b></td><td align="left">Select this option to submit a file.</td></tr>
                    <tr><td align="right"><b> Review: </b></td><td align="left">Select this option to review a file submission.</td></tr>
                    </table>
                    ';
            }

            /************* Submit a file *************/
            if ($_GET['op'] == 'submit')		
            {				
                function upload_form1()
                {
                    $db = new Database("mydb");
                    $query = "select sub_ses_id, title from submit_sessions where active = 'Y' or (active = 'A' and deadline >= ".time().") order by sub_ses_id";
                    $rs = $db->executeQuery($query); 
                    if ($rs->getRowCount() != 0)
                    {					
                    ?>
                        <form name="upload_form1" method="POST" action="">
                            <b>Select Submit Session: </b><br><br>
                            <select name="sub_ses_id">
                            <?php
                            while($rs->next())
                                echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
                            ?>
                            </select>
                            <br><br><br>
                            <input name="review_btn" type="submit" id="review_btn" value="Continue">
                            <input type="hidden" value = "1" name="state">
                            </form>	
                    <?php
                    }
                    else
                    {
                        echo "There are no available active Submit Sessions!<br><br>";
                    }
                }		//upload_form1		
                ?>
                
                <?php
                function upload_form2($sub_ses_id)
                {
                ?>
                    <form enctype="multipart/form-data" name="upload_form2" method="POST" action="">
                        <b>Select a File to Submit: </b><br><br>

                        <?php
                        $db = new Database("mydb");
                        $query = "select filename, filesize from submit_sessions where sub_ses_id = ".$_POST['sub_ses_id'];
                        $rs = $db->executeQuery($query); 
                        if($rs->next())
                        {
                            $filename = $rs->getCurrentValueByNr(0);
                            $filesize = $rs->getCurrentValueByNr(1);
                            if(!empty($filename))
                                echo 'Required Filename: "'.$filename.'".<br>';
                            if($filesize != 0)
                                echo 'Filesize Limit: '.$filesize.' KB.<br>';

                            echo '<br><br>';
                            if($filesize !== 0)
                                echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.($filesize*1024).'" />';
                        }								
                        ?>	
                        <input name="userfile" type="file" />
                        <br><br><br>
                        <input name="review_btn" type="submit" id="review_btn" value="Submit">
                        <input type="hidden" value = "2" name="state">
                        <?php echo '<input type="hidden" value = "'.$sub_ses_id.'" name="sub_ses_id">'; ?>
                   </form>	
                <?php
                }		//upload_form2		


            // Process submitted forms 
                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {

                    if ($_POST['state'] == 1){
                        $sub_ses_id = $_POST['sub_ses_id'];
                        upload_form2($sub_ses_id);
                    }
                    else if ($_POST['state'] == 2)
                    {
                        $sub_ses_id = $_POST['sub_ses_id'];
                        $db = new Database("mydb");

                        // get path, filename, filesize, and active for submit_session
                        $query = "select sub_dir, filename, filesize, deadline, active from submit_sessions where sub_ses_id = ".$sub_ses_id;
                        $rs = $db->executeQuery($query); 
                        if($rs->next())
                        {
                            $sub_dir = $rs->getCurrentValueByNr(0);
                            $filename = $rs->getCurrentValueByNr(1);
                            $filesize = $rs->getCurrentValueByNr(2);
                            $deadline = $rs->getCurrentValueByNr(3);
                            $active = $rs->getCurrentValueByNr(4);

                            // check if this is the first submission
                            $query = "select * from submits where
                                sub_ses_id = ".$sub_ses_id."
                                AND login = '".$_SESSION['login']."'"; 		
                            $rs = $db->executeQuery($query); 
                            $update = $rs->next();

                            //print_r(is_uploaded_file($_FILES['user_file']['tmp_name']));
                            //print_r($_FILES);
                            if (!empty($filename) && $_FILES['userfile']['name'] !== $filename){
                                echo "<br>Wrong Filename!<br> The name of the file you submit has to be ".$filename."!<br><br>";
                                upload_form2($sub_ses_id);
                            }
                            else if ($active == "N"){
                                echo '<br>This submit session has been closed!<br><br>';
                            }
                            else if (($active == "A") && (time() > $deadline))
                            {
                                echo '<br>The deadline for this submit session ('.date("F j, Y, g:i a", $deadline).') is over!<br>';
                            }
                            else if ($_FILES['userfile']['error'] != 0)	// something went wrong with the upload
                            {
                                switch ($_FILES['userfile']['error']) 
                                {
                                case UPLOAD_ERR_OK:
                                    break;
                                case UPLOAD_ERR_INI_SIZE:
                                    echo "<br>The uploaded file exceeds the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.";
                                    break;
                                case UPLOAD_ERR_FORM_SIZE:
                                    echo '<br>You have exceeded the maximum allowed filesize for this submit session ('.$filesize.' KB)';
                                    break;
                                case UPLOAD_ERR_PARTIAL:
                                    echo "<br>The uploaded file was only partially uploaded.";
                                    break;
                                case UPLOAD_ERR_NO_FILE:
                                    echo "<br>No file was uploaded.";
                                    break;
                                case UPLOAD_ERR_NO_TMP_DIR:
                                    echo "<br>Missing a temporary folder.";
                                    break;
                                case UPLOAD_ERR_CANT_WRITE:
                                    echo "<br>Failed to write file to disk";
                                    break;
                                }
                            }
                            else	// save the file
                            {
                                //chmod($sub_dir."/submits", 0755);
                                $save_dir = $sub_dir."/submits/".$_SESSION['login'];
                                if($update) // I have already submitted a file
                                {
                                    // delete previous file
                                    foreach (glob($save_dir.'/*') as $filename)
                                    {
                                        //echo 'Deleting '.$filename;
                                        unlink($filename);
                                    }
                                }
                                else 	// first submission
                                {
                                    // create directory named after user
                                    mkdir($save_dir, 0700);
                                }

                                //chmod($save_dir, 0777);
                                $uploadfile = $save_dir ."/". basename($_FILES['userfile']['name']);
                                if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) 
                                {
                                    chmod($uploadfile, 0700);

                                    if ($update) // I have already submitted a file
                                    {
                                        $query = "update submits 
                                            set sub_time = ".time()."
                                            where sub_ses_id = ".$sub_ses_id."
                                            and login = '".$_SESSION['login']."'";
                                        $rs = $db->executeQuery($query); 
                                        if($rs == 1)
                                            echo '<br> File has been succesfully submitted (updated previous file)' ;
                                        else
                                            echo '<br> Operation failed!';

                                    }
                                    else				// this is the first time
                                    {
                                        $query = "insert into submits (sub_ses_id, login, sub_time)
                                            values (".$sub_ses_id.", '".$_SESSION['login']."', ".time().")";
                                        $rs = $db->executeQuery($query); 
                                        if($rs == 1)
                                            echo '<br> File has been succesfully submitted!' ;
                                        else
                                            echo '<br> Operation failed!';
                                    }

                                } 
                                else {
                                    echo "Operation failed! Please try again.<br><br>";
                                }
                            }
                        }
                    }
                    else
                    {
                        echo "Submit session not found! Please contact your instructor or teaching assistants";
                        exit;
                    }
                }
                else
                {
                    upload_form1();
                }
            }

            /************* Review a submission *************/
            if ($_GET['op'] == 'review')		
            {	
                function review_sub_form()
                {
                    echo '<form name="select_sub_form" method="POST" action="">';
                    select_sub_fields($title="Select Submit Session:", $button_text='Review');
                    echo '</form>';
                }				

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    $sub_ses_id = $_POST['sub_ses_id'];	
                    $db = new Database("mydb");
                    $query = "select deadline from submit_sessions where sub_ses_id = ".$sub_ses_id;
                    $rs = $db->executeQuery($query); 
                    if($rs->next())
                    {
                        echo "<br>The deadline for this submit session is on ".date("F j, Y, g:i a", $rs->getCurrentValueByNr(0)).".<br><br>";
                    }

                    $query = "select sub_time from submits where sub_ses_id = ".$sub_ses_id." and login = '".$_SESSION['login']."'";
                    $rs = $db->executeQuery($query); 
                    if($rs->next())		// I have a made submission
                    {
                        echo "<br>Your last file submission for the selected submit session was on ".date("F j, Y, g:i a", $rs->getCurrentValueByNr(0))."!<br>";
                    }
                    else
                    {
                        echo "<br>You have not submitted a file for the selected submit session!<br>";
                    }		
                }
                else
                {
                    review_sub_form();
                }
            }
        }
        else			/************* ADMIN *************/
                                    {

                                        function sub_fields($title="", $directory = "", $filename="", $filesize=0, $d_date="", $d_h=12, $d_m=0, $auto='A', $button_text='OK')
                                        {
?>
                                                <table border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                                <td>&nbsp; </td>
                                                                <td><table border="0" cellspacing="2" cellpadding="0">
                                                                                <tr>
                                                                                        <td><div align="right"><b><nobr>Title:&nbsp;</b></div></td>
                                                                                        <td><nobr><b> <input name="title" type="text" value="<?php echo "$title";?>"></b>
                                                                                                        &nbsp;(set a title - e.g. "HY-225: Exercise 2")</td>
                                                                                </tr>
                                                                                <tr>
                                                                                        <td><div align="right"><b><nobr>Save Directory:&nbsp;</b></div></td>
                                                                                        <td><nobr><b><input name="directory" type="text" value="<?php echo "$directory";?>"></b>
                                                                                                        &nbsp;(set absolute path* for submitted files - e.g. "/home/lessons/hy225/submit2")</td>
                                                                                </tr>
                                                                                <tr>
                                                                                        <td height="23"><div align="right"><b><nobr>Filename:&nbsp;</b></div></td>
                                                                                        <td><nobr><b><input name="filename" type="text" value="<?php echo "$filename";?>"></b>
                                                                                                        &nbsp;(set required filename, leave blank to allow any filename - e.g. "ask2.tar.gz")</td>
                                                                                </tr>
                                                                                <tr>
                                                                                        <td height="23"><div align="right"><b><nobr>Maximum Filesize:&nbsp;</b></div></td>
                                                                                        <td><nobr><b><input name="filesize" type="text" value="<?php echo "$filesize";?>"></b>
                                                                                                        &nbsp;(set filesize limit in KB, set to 0 for no limit - e.g. "2500")</td>
                                                                                </tr>
                                                                                <tr> <td>&nbsp;</td><td>&nbsp;(Note: maximum upload filesize allowed by php/webserver is: <?php echo ini_get("upload_max_filesize") ?>)</td></tr>
                                                                                <tr> <td>&nbsp;</td></tr>
                                                                                <tr>
                                                                                        <td><div align="right"><nobr><b>Submission Deadline&nbsp;&nbsp;</b> </div></td>
                                                                                </tr>
                                                                                <tr>
                                            <script>
                                            document.writeln('<td><div align="right"><nobr><b>Date:&nbsp;</b></div></td>');
                                            document.writeln('<td>');DateInput('date', true, 'DD/MM/YYYY'<?php if($d_date!= "") {echo ",'".$d_date."'";} ?>);document.writeln('</td>');
                                            </script>
                                                                                        <noscript>
                                                                                                <td><div align="right"><nobr><b>Date</b> (dd/mm/yyyy)<b>:&nbsp;</b></div></td>
                                                                                                <td><nobr><b><input name="date" type="text" value="<?php echo "$d_date";?>"></b>
                                                                                                &nbsp;(set date for deadline - e.g. "05/07/1983")
                                                                                                </td>
                                                                                        </noscript>													
                                                                                </tr>
                                                                                <tr>
                                                                                        <td><div align="right"><b> Time:&nbsp; </b></div></td>
                                                                                        <td><table><tr><td><nobr><b>

                                                                                        <select name="d_hour" ><?php for($h=0; $h<24; $h++){
                                                                                            if($h == $d_h)
                                                                                                echo '<option value="'.$h.'" selected="selected">'.($h<10?('0'.$h):$h).'</option>';
                                                                                            else
                                                                                                echo '<option value="'.$h.'">'.($h<10?('0'.$h):$h).'</option>';
                                                                                        }?>
                                                                                                </select>
                                                                                                :
                                                                                                <select name="d_min" ><?php for($m=0; $m<60; $m++){
                                                                                                    if($m == $d_m)
                                                                                                        echo '<option value="'.$m.'" selected="selected">'.($m<10?('0'.$m):$m).'</option>';
                                                                                                    else
                                                                                                        echo '<option value="'.$m.'">'.($m<10?('0'.$m):$m).'</option>';
                                                                                                }?>
                                                                                                </select>													
                                                                                        </b></td></tr></table></td>
                                                                                </tr>
                                                                                <tr>
                                                                                        <td><div align="right"><b><nobr>Automatic Deactivation:&nbsp;</b></div></td>
                                                                                        <td><nobr><input type="checkbox" name="auto" value="A" <?php if ($auto == 'A') echo ' checked="checked" ';?>>									
                                                                                                        &nbsp;(if selected, submit session will automatically close after deadline)</td>
                                                                                </tr>
                                                                                <tr> <td>&nbsp;</td></tr>
                                                                                <tr>
                                                                                        <td><div align="right"><input name="login_btn" type="submit" id="login_btn2" value="<?php echo $button_text; ?>"></div></td>
                                                                                        <td><div align="left"><b></b></div></td>
                                                                                </tr>										
                                                                        </table>
                                                                </td>
                                                                <td width="99%"> </td>
                                                        </tr>
                                                </table>
                                                <br><br><br>* Permissions of Save Directory need to be 777. 
                                                <br> &nbsp;&nbsp;Do NOT to put a "/" at the end of the path!
<?php
                                        }		//sub_fields

                                        /************* Normal Submit Page *************/
                                        if ($_GET['op'] == '')		
                                        {
                                            echo 'Welcome '.$_SESSION['login'].'!';
                                            echo ' You have the following options:<br><br>
                                                <table>
                                                <tr><td align="right"><b> Create: </b></td><td align="left">Create a Submit Session.</td></tr>
                                                <tr><td align="right"><b> Edit: </b></td><td align="left">Edit a Submit Session.</td></tr>
                                                <tr><td align="right"><b> Review: </b></td><td align="left">Get detailed information about a Submit Session.</td></tr>
                                                <tr><td align="right"><b> Close: </b></td><td align="left">Deactivate("close") an active ("open") Submit Session.</td></tr>
                                                <tr><td align="right"><b> Delete: </b></td><td align="left">Delete a Submit Session.</td></tr>
                                                </table>
                                                ';
                                        }

                                        /************* Crate a submit session *************/
                                        if ($_GET['op'] == 'create')		
                                        {				
                                            function create_sub_form($title="", $directory = "", $filename="", $filesize=0, $d_date="", $d_h=12, $d_m=0, $auto='A')
                                            {
                                                echo '<form name="register_form" method="POST" action="">';
                                                sub_fields($title, $directory, $filename, $filesize, $d_date, $d_h, $d_m, $auto, "Create");
                                                echo '</form>';
                                            }				

                                            if($_SERVER['REQUEST_METHOD'] == 'POST') 
                                            {
                                                //include "php/date_check.php";
                                                $title = $_POST['title'];
                                                $directory = $_POST['directory'];
                                                $filename = $_POST['filename'];
                                                if(!is_numeric($_POST['filesize']))
                                                    $filesize = 0;
                                                else
                                                    $filesize = $_POST['filesize'];
                                                $d_date = $_POST['date'];
                                                $h = $_POST['d_hour'];
                                                $m = $_POST['d_min'];
                                                $auto = $_POST['auto'];
                                                if ($auto == "A")
                                                    $active = "A";
                                                else
                                                    $active = "Y";

                                                $day = strtok($d_date, "/");
                                                $month = strtok("/");
                                                $year = strtok("/");

                                                // check date & time

                                                if ( empty($title) || empty($d_date) || empty($directory))
                                                {
                                                    echo "All Fields have to be completed! <br><br>";
                                                    create_sub_form($title, $directory, $filename, $filesize, $d_date, $h, $m, $auto);
                                                }
                                                else if(!is_dir($directory) )
                                                {
                                                    echo "Save Directory does not exist! <br><br>";
                                                    create_sub_form($title, "", $filename, $filesize, $d_date, $h, $m, $auto);
                                                }
                                                else if( !is_writable($directory) )
                                                {
                                                    echo 'Save Directory ('.$directory.') does not have write permissions! <br><br>';
                                                    create_sub_form($title, "", $filename, $filesize, $d_date, $h, $m, $auto);
                                                }					
                                                else if(!is_numeric($month) || !is_numeric($day) || !is_numeric($year) || !checkdate($month ,$day, $year))
                                                {
                                                    echo "Deadline Date is invalid! <br><br>";
                                                    create_sub_form($title, $directory, $filename, $filesize, "", $h, $m, $auto);
                                                }
                                                else if (!is_numeric($h) || !is_numeric($m) || $h > 23 || $h < 0 || $m>59 || $m<0) {
                                                    echo "The Deadline Time you entered is invalid! <br><br>";
                                                    create_sub_form($title, $directory, $filename, $filesize, $d_date, "", $auto);
                                                }
                                                else if(strtotime($month."/".$day."/".$year." ".$h.":".$m) < time())
                                                {
                                                    echo "The Deadline you entered has past (that would be pretty cruel)! <br><br>";
                                                    create_sub_form($title, $directory, $filename, $filesize, "", $h, $m, $auto);
                                                }
                                                else if(file_exists($directory.'/submits') || !mkdir($directory.'/submits', 0700))
                                                {
                                                    echo 'Could not create sub-directory ('.$directory.'/submits'.')!<br>
                                                        Please remove the directory if it already exists and try again. <br><br>';
                                                    create_sub_form($title, $directory, $filename, $filesize, $d_date, $h, $m, $auto);
                                                }
                                                else
                                                {		
                                                    $command = 'setfacl -m user:papamix:rwx '.$directory.'/submits';
                                                    passthru($command);
                                                    $command = 'setfacl -m mask:rwx '.$directory.'/submits';
                                                    passthru($command);
                                                    $query = "insert into submit_sessions (title, sub_dir, filename, filesize, deadline, active) 
                                                        values ('".$title."', '".$directory."', '".$filename."', ".$filesize.", ".strtotime($month."/".$day."/".$year." ".$h.":".$m).", '".$active."')";
                                                    //echo $query;
                                                    $db = new Database("mydb");
                                                    $rs = $db->executeQuery($query); 

                                                    echo '<br> Submit Session has been succesfully created!' ;
                                                }
                                            }
                                            else
                                            {
                                                create_sub_form();
                                            }
                                        }


                                        /************* Edit a submit session *************/
                                        if ($_GET['op'] == 'edit')		
                                        {		
                                            function select_sub_form()
                                            {
                                                echo '<form name="select_sub_form" method="POST" action="">';
                                                select_sub_fields($title="Select Submit Session:", $button_text='Edit');
                                                echo '<input type="hidden" value = "1" name="state">';
                                                echo '</form>';
                                            }				
                                            function edit_sub_form($sub_ses_id, $title="", $directory = "", $filename="", $filesize=0, $d_date="", $d_h=12, $d_m=0, $auto='A')
                                            {
                                                echo '<form name="register_form" method="POST" action="">';
                                                sub_fields($title, $directory, $filename, $filesize, $d_date, $d_h, $d_m, $auto, "Update");
                                                echo '<input type="hidden" value = "2" name="state">';
                                                echo '<input type="hidden" value = "'.$sub_ses_id.'" name="sub_ses_id">';
                                                echo '</form>';
                                            }				

                                            if($_SERVER['REQUEST_METHOD'] == 'POST') 
                                            {
                                                if($_POST['state'] == 1)
                                                {
                                                    $sub_ses_id = $_POST['sub_ses_id'];
                                                    $query = 'select * from submit_sessions where sub_ses_id = '.$sub_ses_id; // (title, sub_dir, filename, filesize, deadline, active) 
                                                    $db = new Database("mydb");
                                                    $rs = $db->executeQuery($query); 
                                                    $rs->next();
                                                    edit_sub_form($sub_ses_id, $rs->getCurrentValueByNr(1), $rs->getCurrentValueByNr(2), $rs->getCurrentValueByNr(3), $rs->getCurrentValueByNr(4), 
                                                    date("d/m/Y", $rs->getCurrentValueByNr(5)), date("G", $rs->getCurrentValueByNr(5)), date("i", $rs->getCurrentValueByNr(5)), $rs->getCurrentValueByNr(6));
                                                }
                                                else if ($_POST['state'] == 2)
                                                {
                                                    $sub_ses_id = $_POST['sub_ses_id'];
                                                    $title = $_POST['title'];
                                                    $directory = $_POST['directory'];
                                                    $filename = $_POST['filename'];
                                                    if(!is_numeric($_POST['filesize']))
                                                        $filesize = 0;
                                                    else
                                                        $filesize = $_POST['filesize'];
                                                    $d_date = $_POST['date'];
                                                    $h = $_POST['d_hour'];
                                                    $m = $_POST['d_min'];
                                                    $auto = $_POST['auto'];
                                                    if ($auto == "A")
                                                        $active = "A";
                                                    else
                                                        $active = "Y";

                                                    $day = strtok($d_date, "/");
                                                    $month = strtok("/");
                                                    $year = strtok("/");

                                                    // check date & time

                                                    if ( empty($title) || empty($d_date) || empty($directory))
                                                    {
                                                        echo "All Fields have to be completed! <br><br>";
                                                        edit_sub_form($sub_ses_id, $title, $directory, $filename, $filesize, $d_date, $h, $m, $auto);
                                                    }
                                                    else if(!is_dir($directory) )
                                                    {
                                                        echo "Save Directory does not exist! <br><br>";
                                                        edit_sub_form($sub_ses_id, $title, "", $filename, $filesize, $d_date, $h, $m, $auto);
                                                    }
                                                    else if( !is_writable($directory) )
                                                    {
                                                        echo 'Save Directory ('.$directory.') does not have write permissions! <br><br>';
                                                        edit_sub_form($sub_ses_id, $title, "", $filename, $filesize, $d_date, $h, $m, $auto);
                                                    }					
                                                    else if(!is_numeric($month) || !is_numeric($day) || !is_numeric($year) || !checkdate($month ,$day, $year))
                                                    {
                                                        echo "Deadline Date is invalid! <br><br>";
                                                        edit_sub_form($sub_ses_id, $title, $directory, $filename, $filesize, "", $h, $m, $auto);
                                                    }
                                                    else if (!is_numeric($h) || !is_numeric($m) || $h > 23 || $h < 0 || $m>59 || $m<0) {
                                                        echo "The Deadline Time you entered is invalid! <br><br>";
                                                        edit_sub_form($sub_ses_id, $title, $directory, $filename, $filesize, $d_date, "", $auto);
                                                    }
                                                    else if(strtotime($month."/".$day."/".$year." ".$h.":".$m) < time())
                                                    {
                                                        echo "The Deadline you entered has past (that would be pretty cruel)! <br><br>";
                                                        edit_sub_form($sub_ses_id, $title, $directory, $filename, $filesize, "", $h, $m, $auto);
                                                    }
                                                        /*else if(file_exists($directory.'/submits') || !mkdir($directory.'/submits', 0700))
                                                        {
                                                                echo 'Could not create sub-directory ('.$directory.'/submits'.')!<br>
                                                                                        Please remove the directory if it already exists and try again. <br><br>';
                                                                edit_sub_form($sub_ses_id, $title, $directory, $filename, $filesize, $d_date, $h, $m, $auto);
                                    }*/
                                else
                                {		
                                    $query = "update submit_sessions SET title = '".$title."', sub_dir = '".$directory."', filename = '".$filename."', 
                                        filesize = ".$filesize.", deadline = ".strtotime($month."/".$day."/".$year." ".$h.":".$m).", active = '".$active."' where sub_ses_id = ".$sub_ses_id;
                                    //echo $query;
                                    $db = new Database("mydb");
                                    $rs = $db->executeQuery($query); 
                                    if($rs === 1)
                                        echo '<br> <b>Submit Session has been succesfully updated and activated!</b><br>' ;
                                    else
                                        echo '<br> Update failed!';
                                }
                                                }
                                            }
                                            else
                                            {
                                                select_sub_form();
                                            }
                                        }

                                        /************* Review a submission *************/
                                        if ($_GET['op'] == 'review')		
                                        {			
                                            function review_sub_form()
                                            {
                                                echo '<form name="select_sub_form" method="POST" action="">';
                                                select_sub_fields($title="Select Submit Session:", $button_text='Review');
                                                echo '</form>';
                                            }				
                                            if($_SERVER['REQUEST_METHOD'] == 'POST') 
                                            {
                                                $sub_ses_id = $_POST['sub_ses_id'];
                                                $db = new Database("mydb");
                                                $query = "select * from submit_sessions where sub_ses_id = ".$sub_ses_id;
                                                $rs = $db->executeQuery($query); 
                                                if($rs->next())
                                                {
                                                    if ($rs->getCurrentValueByNr(6) == 'Y')
                                                        echo 'This Submit Session is <b>active</b> and will have to be <b>manually closed</b> (no automatic deactivation).<br>';
                                                    else if ($rs->getCurrentValueByNr(6) == 'A')
                                                    {
                                                        if($rs->getCurrentValueByNr(5) > time())
                                                            echo 'The deadline for this Submit Session is on <b>'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(5)).'</b>
                                                            and it will be <b>automatically deactivated</b>.<br>';
                                                        else
                                                            echo 'This Submit Session was <b>automatically closed on '.date("F j, Y, g:i a", $rs->getCurrentValueByNr(5)).'</b>.<br>';
                                                    }
                                                    else
                                                        echo 'This Submit Session has been <b>closed</b>.<br>';

                                                    echo 'Detailed info for this Submit Session:<br><br>';

                                                    $rs->reset();
                                                    include "php/print.php";
                                                    print_submits($rs);	

                                                    echo '<br><br>';

                                                    $deadline = $rs->getCurrentValueByNr(5);
                                                    $query = 'select * from submits where sub_ses_id = '.$sub_ses_id;
                                                    //echo $query;
                                                    $rs = $db->executeQuery($query); 
                                                    if($rs->getRowCount() == 0)
                                                    {
                                                        echo 'There are <b>no</b> valid file submissions for this Submit Session.<br><br>';
                                                    }
                                                    else
                                                    {
                                                        echo 'Number of valid file submissions for this Submit Session: <b>'.$rs->getRowCount().'</b><br>';
                                                        echo 'Overdue submissions are marked in <font color="red"><b> RED </b></font>.<br><br>';

                                                        echo '<table cellpadding="5" cellspacing="0" class="blue">';
                                                        echo '<tr><th><b>User</b></th><th><b>Submission Timestamp</b></th></tr>';
                                                        while($rs->next())
                                                        {
                                                            echo '<tr><td align="center">'.$rs->getCurrentValueByNr(1).' </td>';		
                                                            if($rs->getCurrentValueByNr(2) <= $deadline)		
                                                                echo '<td align="center">'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(2)).'</td></tr>';
                                                            else
                                                                echo '<td align="center"><font color="red"><b>'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(2)).'</b></font></td></tr>';
                                                        }
                                                        echo "</table>";
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                review_sub_form();
                                            }	
                                        }

                                        /************* Close a submission *************/
                                        if ($_GET['op'] == 'close')		
                                        {			
                                            function close_sub_form()
                                            {
                                                $db = new Database("mydb");
                                                $query = "select sub_ses_id, title from submit_sessions where active = 'A' or active = 'Y' order by sub_ses_id";
                                                $rs = $db->executeQuery($query); 
                                                if ($rs->getRowCount() != 0)
                                                {
?>
                                                        <form name="close_sub_form" method="POST" action="">
                                                                <b>Select Submit Session: </b><br><br>
                                                                <select name="sub_ses_id">
<?php
                                                    while($rs->next())
                                                        echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
?>
                                                                </select><br><br><br>
                                                                <input name="review_btn" type="submit" id="review_btn" value="Close">
                                                        </form>	
<?php
                                                }
                                                else
                                                {
                                                    echo "There are no active Submit Sessions to close!<br><br>";
                                                }
                                            }		//close_sub_form		

                                            if($_SERVER['REQUEST_METHOD'] == 'POST') 
                                            {
                                                $sub_ses_id = $_POST['sub_ses_id'];
                                                $db = new Database("mydb");
                                                $query = "update submit_sessions 
                                                    set active = 'N'
                                                    where sub_ses_id = ".$sub_ses_id;
                                                $rs = $db->executeQuery($query); 
                                                if($rs == 1)
                                                    echo '<br> Submit Session has been succesfully closed!' ;
                                                else
                                                    echo '<br> Operation failed!';
                                            }
                                            else
                                            {
                                                close_sub_form();
                                            }

                                        }

                                        /************* Delete a submit session *************/
                                        if ($_GET['op'] == 'delete')		
                                        {

                                            function del_sub_form()
                                            {
                                                echo '<form name="del_sub_form" method="POST" action="">';
                                                select_sub_fields($title="Select Submit Session:", $button_text='Delete');
                                                echo '</form>';
                                            }				

                                            if($_SERVER['REQUEST_METHOD'] == 'POST') 
                                            {
                                                $sub_ses_id = $_POST['sub_ses_id'];
                                                $db = new Database("mydb");
                                                $query = "delete from submit_sessions where sub_ses_id = ".$sub_ses_id;
                                                $rs = $db->executeQuery($query); 
                                                $query = "delete from submits where sub_ses_id = ".$sub_ses_id;
                                                $rs2 = $db->executeQuery($query); 
                                                if($rs == 1)
                                                    echo '<br><b>Submit Session has been succesfully deleted!</b><br>Note: '.$rs2.' submission records belonging to this Exam Period were also deleted.' ;
                                                else
                                                    echo '<br><b>Operation failed!</b><br>Probably someone else already deleted this Submit Session.';
                                            }
                                            else
                                            {
                                                del_sub_form();
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
