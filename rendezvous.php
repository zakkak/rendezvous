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
<title>Submit-Rendezvous&nbsp;created by Michael Papamichael&nbsp;&copy;&nbsp;2007</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7">
<link rel="SHORTCUT ICON" HREF="<?php echo $favicon_path;?>">
<link type="text/css" rel="stylesheet" href="theme/style.css">
<!--[if IE 5]>
<link rel="stylesheet" type="text/css" href="theme/ie5style.css">
<![endif]-->
<script type="text/javascript" src="js/calendarDateInput.js">

/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/

</script>
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
        show_links($left_links=array("Create", "rendezvous.php?op=create", "Edit", "rendezvous.php?op=edit", "Review", "rendezvous.php?op=review", "Add Slots",
            "rendezvous.php?op=add_exam", "Remove Slots", "rendezvous.php?op=rem_exam", "Close", "rendezvous.php?op=close", "Delete", "rendezvous.php?op=delete"), 
        $right_links=array("Logout ".$_SESSION['login']." (admin)", "index.php?op=logout", "Help", "index.php?op=help"), $_GET['op']);
    }
    else			// simple users
    {
        show_links($left_links=array("Book", "rendezvous.php?op=book", "Cancel", "rendezvous.php?op=cancel", "Review", "rendezvous.php?op=review"), 
        $right_links=array("Logout ".$_SESSION['login'], "index.php?op=logout", "Help", "index.php?op=help"), $_GET['op']);
    }
}
else	// not logged in
{
    show_links($left_links=array("Login", "index.php?op=login"), $right_links=array("Help", "index.php?op=help"), $_GET['op']);
}

echo 	'<br><br>';
// safe mode check
if( ini_get('safe_mode') ){echo '<b>Warning:</b> PHP is running in SAFE MODE, which is known to cause problems with this site. To disable SAFE MODE contact your web server administrator.<br><br>';}


/*************  REST OF PAGE  *****************/

if(check_db())
{
    function select_ren_fields($title="Select Rendezvous Session:", $button_text='Continue')
    {
        $db = new Database("mydb");
        $query = "select ren_ses_id, title from ren_sessions order by ren_ses_id desc";
        $rs = $db->executeQuery($query); 
        if ($rs->getRowCount() != 0)
        {
              ?>
              <b><?php echo $title ?></b><br><br>
              <select name="ren_ses_id">
              <?php
                while($rs->next())
                    echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
              ?>
              </select><br><br><br>
              <input name="review_btn" type="submit" id="review_btn" value="<?php echo $button_text ?>">
              <?php
        }
        else
        {
              echo "There are no available Rendezvous Sessions!<br><br>";
        }
    }   //select_ren_fields

    if (isset($_SESSION['login']) && $_SESSION['full_path'] == realpath(".") )			// logged in
    {
        if ($_SESSION['acc_type'] == 'user')	// simple user
        {
            /************* Normal Submit Page *************/
            if ($_GET['op'] == '')		
            {
                echo 'Welcome '.$_SESSION['login'].'!';
                echo ' You have the following options:<br><br>
                    <table>
                    <tr><td align="right"><b> Book: </b></td><td align="left">Select this option to book a rendezvous.</td></tr>
                    <tr><td align="right"><b> Review: </b></td><td align="left">Select this option to review a rendezvous.</td></tr>
                    <tr><td align="right"><b> Cancel: </b></td><td align="left">Select this option to cancel a rendezvous.</td></tr>
                    </table>
                    ';
            }

            /************* Book a Rendezvous *************/
            if ($_GET['op'] == 'book')		
            {	
                function book_form1()
                {
                    $db = new Database("mydb");
                    $query = "select ren_ses_id, title from ren_sessions where active = 'Y' or (active = 'A' and deadline >= ".time().") order by ren_ses_id desc";
                    $rs = $db->executeQuery($query); 
                    if ($rs->getRowCount() != 0)
                    {
                    ?>
                    <form name="book_form1" method="POST" action="">
                        <b>Select Rendezvous Session: </b><br><br>
                        <select name="ren_ses_id">
                    <?php
                        while($rs->next())
                            echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
                    ?>
                        </select><br><br><br>
                        <input name="review_btn" type="submit" id="review_btn" value="Continue">
                        <input type="hidden" value = "1" name="state">
                    </form>	
                    <?php
                    }
                    else
                    {
                        echo "There are no available active Rendezvous Sessions!<br><br>";
                    }
                }		//book_form1		

                function book_form2($ren_ses_id)
                {
                    $db = new Database("mydb");
                    $query = "select * from ren_periods where ren_ses_id = ".$ren_ses_id." order by ren_start";
                    $ren_periods = $db->executeQuery($query); 

                    if($ren_periods->getRowCount() > 0)		// There are slots
                    {
                        $looking_for_free_slot = true;
                        echo '<form name="book_form2" method="POST" action="">';
                        echo '<b>Select an available slot and click </b>';
                        echo '<input name="review_btn" type="submit" id="review_btn" value="Book"><br><br>';
                        echo '<table border="0" cellpadding="0" cellspacing="0"><tr>';

                        while($ren_periods->next())		// for each rendezvous period shot table with slots
                        {
                            echo '<td valign="top">';
                            $ren_per_id = $ren_periods->getCurrentValueByNr(0);
                            $ren_ses_id = $ren_periods->getCurrentValueByNr(1);
                            $ren_start = $ren_periods->getCurrentValueByNr(2);
                            $ren_end = $ren_periods->getCurrentValueByNr(3);
                            $ren_length = $ren_periods->getCurrentValueByNr(4);
                            $ren_slots = $ren_periods->getCurrentValueByNr(5);

                            $date = date("D, j/n/Y", $ren_start);
                            $s_h = date("H", $ren_start);
                            $s_m = date("i", $ren_start);
                            $e_h = date("H", $ren_end);
                            $e_m = date("i", $ren_end);

                            $start_time = $s_h * 60 + $s_m;
                            $end_time = $e_h * 60 + $e_m;

                            //echo $date.'&nbsp;('.$s_h.':'.$s_m.' - '.$e_h.':'.$e_m.')';
                            //echo "<table border=\"1\" width=\"80%\">";
                            echo '<table  cellpadding="5" cellspacing="0" class="blue">';
                            //echo '<table border="1" >';
                            echo '<tr><th align = "center"><font size = 4><nobr>'.$date.'</nobr></font></th>';
                            for ($i=1; $i<=$ren_slots; $i++)
                            {
                                echo '<th align = "center"><font size = 4><nobr>&nbsp;Slot&nbsp;'.$i.'&nbsp;</nobr></font></th>';
                            }
                            echo '</tr>';

                            $booked = array();
                            $logins = array();
                            $query = "select ren_time, ren_slot, login from rendezvous where ren_per_id = ".$ren_per_id;
                            $b_rs = $db->executeQuery($query); 
                            while($b_rs->next())
                            {
                                array_push($booked, array($b_rs->getCurrentValueByNr(0), $b_rs->getCurrentValueByNr(1)) );
                                array_push($logins, $b_rs->getCurrentValueByNr(2) );
                            }
                            //print_r($booked);
                            //print_r($logins);

                            for ($time = $ren_start; $time < $ren_end; $time += ($ren_length*60))
                            {
                                $slot_start = date("H:i", $time);
                                $slot_end = date("H:i", $time+($ren_length*60));
                                echo '<tr><th align = "center"><nobr>&nbsp;'.$slot_start.' - '.$slot_end.'&nbsp;</nobr></th>';

                                for ($i=1; $i<=$ren_slots; $i++)
                                {
                                    $found = array_search( array($time, $i), $booked );
                                    if($found !== false)		// Slot is reserved
                                    {
                                        //echo '<td align = "center"> RESERVED </td>';			
                                        echo '<td align = "center"><nobr>&nbsp;'. $logins[$found] .'&nbsp;</nobr></td>';			
                                    }
                                    else		// Slot is free
                                    {
                                        /*echo '<td align = "center"><input name="rendezvous" type="radio" 
                                        value="'.$ren_ses_id.'|'.$ren_per_id.'|'.$time.'|'.$i.'|" checked = "checked"></td>';*/			
                                        echo '<td align = "center"><input name="rendezvous" type="radio" value="'.$ren_ses_id.'|'.$ren_per_id.'|'.$time.'|'.$i.'|"';
                                        if($looking_for_free_slot)
                                        {
                                            echo ' checked="checked" ';
                                            $looking_for_free_slot = false;
                                        }
                                        echo '></td>';	
                                    }
                                }
                                echo '</tr>';					
                            }
                            echo '</table><br><br>';
                            echo '</td><td>&nbsp;&nbsp;&nbsp;</td>';
                        }

                        ?>
                            </tr></table>
                            <input type="hidden" value = "2" name="state">
                            </form>	
                        <?php
                    }
                    else		// There are exam periods
                    {
                        echo "No Exam Periods found for this Rendezvous Session! <br> 
                            Please ask your instructor or teaching assistants to add Exam Periods for this Rendezvous Session.<br><br>";
                        exit;
                    }
                }		//book_form2		


                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {	
                    if ($_POST['state'] == 1){
                        $ren_ses_id = $_POST['ren_ses_id'];
                        if (empty($ren_ses_id))
                        {
                            echo "Invalid Rendezvous Session!<br>Please create a rendezvous first. <br><br>";
                            book_form1();
                        }		
                        else{
                            book_form2($_POST['ren_ses_id']);
                        }
                    }
                    else if ($_POST['state'] == 2)
                    {
                        if (empty($_POST['rendezvous']))
                        {
                            echo "Invalid Selection!<br>Please contact your instructor or teaching assistants. <br><br>";
                            //book_form1(	);
                        }		
                        else
                        {		
                            $ren_ses_id = strtok($_POST['rendezvous'], "|");
                            $ren_per_id = strtok("|");
                            $time = strtok("|");
                            $slot = strtok("|");

                            $db = new Database("mydb");
                            $query = "select ren_per_id from rendezvous where
                                ren_ses_id = ".$ren_ses_id."
                                AND login = '".$_SESSION['login']."'"; 		
                            $rs = $db->executeQuery($query); 
                            $update = $rs->next();

                            //lock
                            $fp = fopen(DB_DIR."lock.txt", "w");

                            if (flock($fp, LOCK_EX)) 
                            { // do an exclusive lock								
                                $query = "select login from rendezvous where
                                    ren_ses_id = ".$ren_ses_id."
                                    AND ren_per_id = ".$ren_per_id." 		
                                    AND ren_time = ".$time." 
                                    AND ren_slot = ".$slot; 		
                                $rs = $db->executeQuery($query);
                                if($rs->next())		// somebody was faster
                                { 
                                    echo '<br> Please try again! Someone already booked the slot you selected.' ;
                                    book_form2($ren_ses_id);
                                }
                                else			// you booked it!
                                {
                                    if($update)		
                                    {
                                        $query = "update rendezvous set ren_time = ".$time.", ren_per_id = ".$ren_per_id.", ren_slot = ".$slot." where ren_ses_id = ".$ren_ses_id." and login = '".$_SESSION['login']."'";
                                        $rs2 = $db->executeQuery($query); 
                                        if($rs2 == 1)
                                        {
                                            echo '<b>Rendezvous succesfully updated!</b><br>';
                                            if($email_confirmation)
                                            {
                                                $query = "select title from ren_sessions where ren_ses_id = ".$ren_ses_id;
                                                $rs4 = $db->executeQuery($query);
                                                $rs4->next();
                                                $title = $rs4->getCurrentValueByNr(0);
                                                $email = $_SESSION['login'].'@csd.uoc.gr';
                                                $subject = "Rendezvous Confirmation for ".$title;
                                                $message = "You have succesfully updated your rendezvous with the following details:\n\nRendezvous Session: ".$title."\nDate: ".date("F j, Y", $time)."\nTime: ".date("H:i", $time)."\nSlot: ".$slot."\n\n\nPlease do not reply to this message";
                                                if( mail($email, $subject, $message, "From: Submit-Rendezvous <donotreply>\r\nContent-Type: text/plain;charset=iso-8859-7") )
                                                    echo 'A confirmation e-mail has been sent to '.$email ;
                                            }
                                        }
                                        else
                                        {
                                            echo '<br> Operation failed! Please try again.';
                                        }
                                    }
                                    else					// user had no rendezvous before
                                    {
                                        $query = "insert into rendezvous
                                            (ren_ses_id, ren_per_id, login, ren_time, ren_slot)
                                            values ( '".$ren_ses_id."', '".$ren_per_id."', '".$_SESSION['login']."', ".$time.", ".$slot.")";
                                        $rs3 = $db->executeQuery($query); 
                                        if($rs3 == 1)
                                        {
                                            echo '<b>Rendezvous succesfully booked!</b><br>';

                                            if($email_confirmation)
                                            {
                                                $query = "select title from ren_sessions where ren_ses_id = ".$ren_ses_id;
                                                $rs4 = $db->executeQuery($query);
                                                $rs4->next();
                                                $title = $rs4->getCurrentValueByNr(0);
                                                $email = $_SESSION['login'].'@csd.uoc.gr';
                                                $subject = "Rendezvous Confirmation for ".$title;
                                                $message = "You have succesfully booked a rendezvous with the following details:\n\nRendezvous Session: ".$title."\nDate: ".date("F j, Y", $time)."\nTime: ".date("H:i", $time)."\nSlot: ".$slot."\n\n\nPlease do not reply to this message";
                                                if( mail($email, $subject, $message, "From: Submit-Rendezvous <donotreply>\r\nContent-Type: text/plain;charset=iso-8859-7") )
                                                    echo 'A confirmation e-mail has been sent to '.$email ;
                                            }
                                        }
                                        else
                                        {
                                            echo '<br> Operation failed! Please try again.';
                                        }

                                    }
                                }
                                flock($fp, LOCK_UN); // release the lock							
                            } 
                            else 
                            {
                                echo "Couldn't lock the file!";
                            }
                            
                            // unlock
                            fclose($fp);

                        }
                    }	

                }
                else
                {
                    book_form1();
                }

            }


            /************* Cancel a Rendezvous *************/
            if ($_GET['op'] == 'cancel')		
            {			
                function cancel_ren_form()
                {
                    $db = new Database("mydb");
                    $query = "select ren_ses_id, title from ren_sessions where active = 'Y' or (active = 'A' and deadline >= ".time().") order by ren_ses_id desc";
                    $rs = $db->executeQuery($query); 
                    if ($rs->getRowCount() != 0)
                    {
                    ?>
                    <form name="cancel_ren_form" method="POST" action="">
                            <b>Select Rendezvous Session: </b><br><br>
                            <select name="ren_ses_id">
                    <?php
                        while($rs->next())
                            echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
                    ?>
                            </select><br><br><br>
                            <input name="review_btn" type="submit" id="review_btn" value="Cancel">
                            <input type="hidden" value = "1" name="state">
                    </form>	
                    <?php
                    }
                    else
                    {
                        echo "There are no available Rendezvous Sessions!<br><br>";
                    }
                }		//cancel_ren_form

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    $ren_ses_id = $_POST['ren_ses_id'];
                    if (empty($ren_ses_id))
                    {
                        echo "Invalid Rendezvous Session!<br>Please create a rendezvous first. <br><br>";
                        close_ren_form(	);
                    }		
                    else{
                        $db = new Database("mydb");
                        $query = "delete from rendezvous where ren_ses_id = ".$ren_ses_id." and login = '".$_SESSION['login']."'";
                        $rs = $db->executeQuery($query); 
                        if($rs == 1)
                            echo '<b>Rendezvous has been succesfully canceled!</b>' ;
                        else
                            echo '<br> No booking found to cancel!<br>You probably did not have a booking for this rendezvous session.';
                    }
                }
                else
                {
                    cancel_ren_form();
                }	

            }

            /************* Review a Rendezvous *************/
            if ($_GET['op'] == 'review')		
            {

                function review_form()
                {
                    echo '<form name="review_form" method="POST" action="">';
                    select_ren_fields($title="Select Rendezvous Session:", $button_text='Review');
                    echo '</form>';
                }	// review_form

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {

                    $ren_ses_id = $_POST['ren_ses_id'];
                    $db = new Database("mydb");
                    $query = "select * from rendezvous where ren_ses_id = ".$ren_ses_id.
                        " and login = '".$_SESSION['login']."'";
                    $rs = $db->executeQuery($query); 
                    if($rs->next())
                    {
                        $time = $rs->getCurrentValueByNr(3);
                        $slot = $rs->getCurrentValueByNr(4);
                        echo "<br>You have booked a rendezvous for ".date("F j, Y, g:i a", $time)." (in slot ".$slot.").<br><br>";
                    }
                    else
                    {
                        echo "<br>You have not booked a rendezvous for the selected rendezvous session!<br><br>";
                    }

                    /*$rs->reset();
                    echo "<br>Detailed database entry for this submit session:<br><br>";
                    include "php/print.php";
                    print_table($rs);*/

                }
                else
                {
                    review_form();
                }

            }

        }
        else	// admin
        {

            function ren_fields($title="", $d_date="", $d_hour=12, $d_min=0, $active="A", $button_text='OK')
            {
?>
          <table    border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td>&nbsp; </td>
              <td><table border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td><div align="right"><b><nobr>Title:&nbsp;</b></div></td>
                    <td><nobr><b> <input name="title" type="text" value="<?php echo "$title";?>"></b>
                        &nbsp;(set a title - e.g. "HY-225: Examination of exercise 3")
                    </td>
                  </tr>
                  <tr> <td>&nbsp;</td></tr>
                  <tr>
                    <td><div align="right"><nobr><b>Booking Deadline&nbsp;&nbsp;</b> </div></td>
                  </tr>
                  <tr>
                <script>
                document.writeln('<td><div align="right"><nobr><b>Date:&nbsp;</b></div></td>');
                document.writeln('<td>');DateInput('d_date', true, 'DD/MM/YYYY'<?php if($d_date!= "") {echo ",'".$d_date."'";} ?>);document.writeln('</td>');
                </script>
                    <noscript>
                      <td><div align="right"><nobr><b>Date</b> (dd/mm/yyyy)<b>:&nbsp;</b></div></td>
                      <td><nobr><b><input name="d_date" type="text" value="<?php echo "$d_date";?>"></b>
                      &nbsp;(set date for deadline - e.g. "05/07/1983")
                      </td>
                    </noscript>													
                  </tr>
                  <tr>
                    <td><div align="right"><b> Time:&nbsp; </b></div></td>
                    <td><table><tr><td><nobr><b>

                    <select name="d_hour" ><?php for($h=0; $h<24; $h++){
                        if($h == $d_hour)
                            echo '<option value="'.$h.'" selected="selected">'.($h<10?('0'.$h):$h).'</option>';
                        else
                            echo '<option value="'.$h.'">'.($h<10?('0'.$h):$h).'</option>';
                    }?>
                      </select>
                      :
                      <select name="d_min" ><?php for($m=0; $m<60; $m++){
                          if($m == $d_min)
                              echo '<option value="'.$m.'" selected="selected">'.($m<10?('0'.$m):$m).'</option>';
                          else
                              echo '<option value="'.$m.'">'.($m<10?('0'.$m):$m).'</option>';
                      }?>
                      </select>													
                    </b></td></tr></table></td>
                  </tr>
                  <tr>
                    <td><div align="right"><b><nobr>Automatic Deactivation:&nbsp;</b></div></td>
                    <td><nobr><input type="checkbox" name="active" value="A" <?php if($active == "A")echo 'checked="checked"';?> >
                        &nbsp;(if selected, rendezvous session will automatically close after deadline)
                    </td>
                  </tr>
                  <tr> <td>&nbsp;</td></tr>
                  <tr>
                    <td><div align="right"><input name="login_btn" type="submit" id="login_btn2" value="<?php echo $button_text ?>"></div></td>
                    <td><div align="left"><b></b></div></td>
                  </tr>										
                </table>
              </td>
              <td width="99%"> </td>
            </tr>
          </table>
<?php
            }

            /************* Normal Rendezvous Page *************/
            if ($_GET['op'] == '')		
            {
                echo 'Welcome '.$_SESSION['login'].'!';
                echo ' You have the following options:<br><br>
                    <table>
                    <tr><td align="right"><b> Create: </b></td><td align="left">Create a Rendezvous Session. Don\'t forget to add examination slots afterwards! </td></tr>
                    <tr><td align="right"><b> Edit: </b></td><td align="left">Edit a Rendezvous Session.</td></tr>
                    <tr><td align="right"><b> Review: </b></td><td align="left">Get detailed information about a Rendezvous Session.</td></tr>
                    <tr><td align="right"><b> Add Slots: </b></td><td align="left">Add Examination Slots to a Rendezvous Session.</td></tr>
                    <tr><td align="right"><b> Remove Slots: </b></td><td align="left">Remove Examination Slots from a Rendezvous Session.</td></tr>
                    <tr><td align="right"><b> Close: </b></td><td align="left">Deactivate ("close") an active ("open") Rendezvous Session.</td></tr>
                    <tr><td align="right"><b> Delete: </b></td><td align="left">Delete a Rendezvous Session.</td></tr>
                    </table>
                    ';
            }

            /************* Crate a rendezvous session *************/
            if ($_GET['op'] == 'create')		
            {

                function create_ren_form($title="", $d_date="", $d_hour=12, $d_min=0, $active="A")
                {
                    echo '<form name="create_ren_form" method="POST" action="">';
                    ren_fields($title, $d_date, $d_hour, $d_min, $active, $button_text='Create');				
                    echo '</form>';
                }		//create_ren_form

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    //include "php/date_check.php";
                    $title = $_POST['title'];
                    $d_date = $_POST['d_date'];
                    $h = $_POST['d_hour'];
                    $m = $_POST['d_min'];
                    if ($_POST['active'] == "A")
                        $active = "A";
                    else
                        $active = "Y";

                    $day = strtok($d_date, "/");
                    $month = strtok("/");
                    $year = strtok("/");

                    // check date & time
                    if ( empty($title) || empty($d_date))
                    {
                        echo "All Fields have to be completed! <br><br>";
                        create_ren_form($title, $d_date, $h, $m, $active);
                    }
                    else if(!is_numeric($month) || !is_numeric($day) || !is_numeric($year) || !checkdate($month ,$day, $year))
                    {
                        echo "Deadline Date is invalid! <br><br>";
                        create_ren_form($title, "", $h, $m, $active);
                    }
                    else if (!is_numeric($h) || !is_numeric($m) || $h > 23 || $h < 0 || $m>59 || $m<0) {
                        echo "The Deadline Time you entered is invalid! <br><br>";
                        create_ren_form($title, $d_date, 12, 0, $active);
                    }
                    else if(strtotime($month."/".$day."/".$year." ".$h.":".$m) < time())
                    {
                        echo "The Deadline you entered has past (that would be pretty cruel)! <br><br>";
                        create_ren_form($title, "", $h, $m, $active);
                    }
                    else
                    {														
                        $query = "insert into ren_sessions (title, deadline, active) 
                            values ('".$title."', ".strtotime($month."/".$day."/".$year." ".$h.":".$m).", '".$active."')";
                        //echo $query;
                        $db = new Database("mydb");
                        $rs = $db->executeQuery($query); 

                        echo '<br><b> Rendezvous Session has been succesfully created!</b><br>
                            Do not forget to add Examination Slots to the Rendezvous Session you just created.';
                    }

                }
                else
                {
                    create_ren_form("", "", 12, 0, "");
                }
            }

            /************* Edit a rendezvous session *************/
            if ($_GET['op'] == 'edit')		
            {
                function select_ren_form()
                {
                    echo '<form name="select_ren_form" method="POST" action="">';
                    select_ren_fields($title="Select Rendezvous Session:", $button_text='Edit');
                    echo '<input type="hidden" value = "1" name="state">';
                    echo '</form>';
                }	// review_form

                function edit_ren_form($ren_ses_id, $title="", $d_date="", $d_hour=12, $d_min=0, $active="A")
                {
                    echo '<form name="create_ren_form" method="POST" action="">';
                    ren_fields($title, $d_date, $d_hour, $d_min, $active, $button_text='Update');	
                    echo '<input type="hidden" value = "2" name="state">';
                    echo '<input type="hidden" value = "'.$ren_ses_id.'" name="ren_ses_id">';
                    echo '</form>';
                }		//create_ren_form

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    if($_POST['state'] == 1)
                    {
                        $ren_ses_id = $_POST['ren_ses_id'];
                        $query = 'select * from ren_sessions where ren_ses_id = '.$ren_ses_id; // (title, sub_dir, filename, filesize, deadline, active) 
                        $db = new Database("mydb");
                        $rs = $db->executeQuery($query); 
                        $rs->next();
                        edit_ren_form($ren_ses_id, $rs->getCurrentValueByNr(1), date("d/m/Y", $rs->getCurrentValueByNr(2)), date("G", $rs->getCurrentValueByNr(2)), 
                        date("i", $rs->getCurrentValueByNr(2)), $rs->getCurrentValueByNr(3));
                    }
                    else if ($_POST['state'] == 2)
                    {		
                        $ren_ses_id = $_POST['ren_ses_id'];
                        $title = $_POST['title'];
                        $d_date = $_POST['d_date'];
                        $h = $_POST['d_hour'];
                        $m = $_POST['d_min'];
                        if ($_POST['active'] == "A")
                            $active = "A";
                        else
                            $active = "Y";

                        $day = strtok($d_date, "/");
                        $month = strtok("/");
                        $year = strtok("/");

                        // check date & time
                        if ( empty($title) || empty($d_date))
                        {
                            echo "All Fields have to be completed! <br><br>";
                            edit_ren_form($ren_ses_id, $title, $d_date, $h, $m, $active);
                        }
                        else if(!is_numeric($month) || !is_numeric($day) || !is_numeric($year) || !checkdate($month ,$day, $year))
                        {
                            echo "Deadline Date is invalid! <br><br>";
                            edit_ren_form($ren_ses_id, $title, "", $h, $m, $active);
                        }
                        else if (!is_numeric($h) || !is_numeric($m) || $h > 23 || $h < 0 || $m>59 || $m<0) {
                            echo "The Deadline Time you entered is invalid! <br><br>";
                            edit_ren_form($ren_ses_id, $title, $d_date, 12, 0, $active);
                        }
                        else if(strtotime($month."/".$day."/".$year." ".$h.":".$m) < time())
                        {
                            echo "The Deadline you entered has past (that would be pretty cruel)! <br><br>";
                            edit_ren_form($ren_ses_id, $title, "", $h, $m, $active);
                        }
                        else
                        {														
                            $query = "update ren_sessions set title = '".$title."', deadline = ".strtotime($month."/".$day."/".$year." ".$h.":".$m).", active = '".$active."' where ren_ses_id = ".$ren_ses_id;
                            $db = new Database("mydb");
                            $rs = $db->executeQuery($query); 
                            if($rs === 1)
                                echo '<br><b> Rendezvous Session has been succesfully updated and activated!</b><br>';
                            else
                                echo '<br> Update failed!';
                        }
                    }
                }
                else
                {
                    select_ren_form();
                }
            }				

            /************* Review a rendezvous session *************/
            if ($_GET['op'] == 'review')		
            {			

                function review_form()
                {
                    echo '<form name="review_form" method="POST" action="">';
                    select_ren_fields($title="Select Rendezvous Session:", $button_text='Review');
                    echo '</form>';
                }	// review_form

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    $ren_ses_id = $_POST['ren_ses_id'];
                    $db = new Database("mydb");
                    $query = "select * from ren_sessions where ren_ses_id = ".$ren_ses_id;
                    $rs = $db->executeQuery($query); 
                    if($rs->next())
                    {
                        if ($rs->getCurrentValueByNr(3) == 'Y')
                            echo 'This rendezvous session is <b>open</b> for booking and will have to be <b>manually closed</b> (no automatic deactivation).<br>';
                        else if ($rs->getCurrentValueByNr(3) == 'A')
                        {
                            if($rs->getCurrentValueByNr(2) > time())
                                echo 'The booking deadline for this rendezvous session is on <b>'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(2)).'</b>
                                and it will be <b>automatically deactivated</b>.<br>';
                            else
                                echo 'This rendezvous session was <b>automatically closed on '.date("F j, Y, g:i a", $rs->getCurrentValueByNr(2)).'</b>.<br>';
                        }
                        else
                            echo 'This rendezvous session has been <b>closed</b>.<br>';
                    }
                    echo 'Detailed info for this Rendezvous Session:<br><br>';

                    $rs->reset();
                    include "php/print.php";
                    print_rendezvous($rs);	

                    echo '<br><br>';

                    $query = 'select * from rendezvous where ren_ses_id = '.$ren_ses_id;
                    $rs = $db->executeQuery($query); 
                    if($rs->getRowCount() == 0)
                        echo 'There are <b>no</b> bookings for this Rendezvous Session.<br><br>';
                    else
                        echo 'Number of bookings for this Rendezvous Session: <b>'.$rs->getRowCount().'</b><br><br>';

                    $query = "select * from ren_periods where ren_ses_id = ".$ren_ses_id." order by ren_start";
                    $ren_periods = $db->executeQuery($query); 

                    if($ren_periods->getRowCount() > 0)		// There are slots
                    {
                        echo '<table border="0" cellpadding="0" cellspacing="0"><tr>';

                        while($ren_periods->next())		// for each rendezvous period show table with slots
                        {
                            echo '<td valign="top">';
                            $ren_per_id = $ren_periods->getCurrentValueByNr(0);
                            $ren_ses_id = $ren_periods->getCurrentValueByNr(1);
                            $ren_start = $ren_periods->getCurrentValueByNr(2);
                            $ren_end = $ren_periods->getCurrentValueByNr(3);
                            $ren_length = $ren_periods->getCurrentValueByNr(4);
                            $ren_slots = $ren_periods->getCurrentValueByNr(5);

                            $date = date("D, j/n/Y", $ren_start);
                            $s_h = date("H", $ren_start);
                            $s_m = date("i", $ren_start);
                            $e_h = date("H", $ren_end);
                            $e_m = date("i", $ren_end);

                            $start_time = $s_h * 60 + $s_m;
                            $end_time = $e_h * 60 + $e_m;

                            //echo $date.'&nbsp;('.$s_h.':'.$s_m.' - '.$e_h.':'.$e_m.')';
                            //echo "<table border=\"1\" width=\"80%\">";
                            echo '<table  cellpadding="5" cellspacing="0" class="blue">';
                            //echo '<table border="1" >';
                            echo '<tr><th><b><div align = "center"><font size = 4><nobr>&nbsp;'.$date.'&nbsp;</nobr></font></div></b></th>';
                            for ($i=1; $i<=$ren_slots; $i++)
                            {
                                echo '<th><div align = "center"><font size = 4><nobr>&nbsp;Slot&nbsp;'.$i.'&nbsp;</nobr></font></div></th>';
                            }
                            echo '</tr>';

                            $booked = array();
                            $logins = array();
                            $query = "select ren_time, ren_slot, login from rendezvous where ren_per_id = ".$ren_per_id;
                            $b_rs = $db->executeQuery($query); 
                            while($b_rs->next())
                            {
                                array_push($booked, array($b_rs->getCurrentValueByNr(0), $b_rs->getCurrentValueByNr(1)) );
                                array_push($logins, $b_rs->getCurrentValueByNr(2) );
                            }
                            //print_r($booked);
                            //print_r($logins);

                            for ($time = $ren_start; $time < $ren_end; $time += ($ren_length*60))
                            {
                                $slot_start = date("H:i", $time);
                                $slot_end = date("H:i", $time+($ren_length*60));
                                echo '<tr><th><b><div align = "center"><font size = 3><nobr>&nbsp;'.$slot_start.' - '.$slot_end.'&nbsp;</nobr></font></div></b></th>';

                                for ($i=1; $i<=$ren_slots; $i++)
                                {
                                    $found = array_search( array($time, $i), $booked );
                                    if($found !== false)		// Slot is reserved
                                    {
                                        echo '<td><div align = "center"><font size = 3><nobr>&nbsp;'. $logins[$found] .'&nbsp;</nobr></font></div></td>';			
                                    }
                                    else		// Slot is free
                                    {
                                        echo '<td align="center">&nbsp;</td>';
                                    }
                                }
                                echo '</tr>';					
                            }
                            echo '</table><br><br>';
                            echo '</td><td>&nbsp;&nbsp;&nbsp;</td>';
                        }
                        echo '</tr></table>';

                    }
                    else		// There are exam periods!
                    {
                        echo "No Examination Slots found for this Rendezvous Session! <br> 
                            Please Add Exam Slots for this Rendezvous Session.<br><br>";
                        exit;
                    }

                }
                else
                {
                    review_form();
                }


            }


            /************* Add Exam Period *************/
            if ($_GET['op'] == 'add_exam')		
            {

                function add_exam_form($ren_ses_id="", $date="", $s_h=12, $s_m=0, $e_h=12, $e_m=0, $length="", $slots="")
                {
                ?>
                <form name="add_exam_form" method="POST" action="">
                    <table    border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>&nbsp; </td>
                            <td><table border="0" cellspacing="2" cellpadding="0">
                                <tr>
                                    <td><div align="right"><b><nobr>Select Rendezvous:&nbsp;</b></div></td>
                                    <td><nobr><select name="ren_ses_id">
                    <?php
                    $db = new Database("mydb");
                    $query = "select ren_ses_id, title from ren_sessions order by ren_ses_id desc";
                    $rs = $db->executeQuery($query); 
                    while($rs->next())
                    {
                        echo '<option ';
                        if($rs->getCurrentValueByNr(0) == $ren_ses_id)
                            echo 'selected="selected" ';
                        echo 'value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
                    }
                    ?>
                                   </select>
                                   &nbsp;(select the rendezvous that this exam period belongs to)</td>
                                </tr>
                            <tr> <td>&nbsp;</td></tr>
                       <tr>
                            <td><div align="right"><nobr><b>Examination Date/Time&nbsp;&nbsp;</b> </div></td>
                       </tr>
                       <tr>
                    <script>
                    document.writeln('<td><div align="right"><nobr><b>Date:&nbsp;</b></div></td>');
                    document.writeln('<td>');DateInput('date', true, 'DD/MM/YYYY'<?php if($date!= "") {echo ",'".$date."'";} ?>);document.writeln('</td>');
                    </script>
                                           <noscript>
                                                   <td><div align="right"><nobr><b>Date</b> (dd/mm/yyyy)<b>:&nbsp;</b></div></td>
                                                   <td><nobr><b><input name="date" type="text" value="<?php echo "$d_date";?>"></b>
                                                   &nbsp;(set examination date  - e.g. "05/07/1983")
                                                   </td>
                                           </noscript>													
                                        </tr>
                                        <tr>
                                            <td><div align="right"><b> Start Time:&nbsp; </b></div></td>
                                            <td><table><tr><td><nobr><b>
                                            <select name="s_h" ><?php for($h=0; $h<24; $h++){
                                                if($h == $s_h)
                                                    echo '<option value="'.$h.'" selected="selected">'.($h<10?('0'.$h):$h).'</option>';
                                                else
                                                    echo '<option value="'.$h.'">'.($h<10?('0'.$h):$h).'</option>';
                                            }?>
                                                    </select>
                                                    :
                                                    <select name="s_m" ><?php for($m=0; $m<60; $m++){
                                                        if($m == $s_m)
                                                            echo '<option value="'.$m.'" selected="selected">'.($m<10?('0'.$m):$m).'</option>';
                                                        else
                                                            echo '<option value="'.$m.'">'.($m<10?('0'.$m):$m).'</option>';
                                                    }?>
                                                    </select>													
                                            </b></td></tr></table></td>
                                        </tr>
                                        <tr>
                                            <td><div align="right"><b> End Time:&nbsp; </b></div></td>
                                            <td><table><tr><td><nobr><b>
                                            <select name="e_h" ><?php for($h=0; $h<24; $h++){
                                                if($h == $e_h)
                                                    echo '<option value="'.$h.'" selected="selected">'.($h<10?('0'.$h):$h).'</option>';
                                                else
                                                    echo '<option value="'.$h.'">'.($h<10?('0'.$h):$h).'</option>';
                                            }?>
                                                    </select>
                                                    :
                                                    <select name="e_m" ><?php for($m=0; $m<60; $m++){
                                                        if($m == $e_m)
                                                            echo '<option value="'.$m.'" selected="selected">'.($m<10?('0'.$m):$m).'</option>';
                                                        else
                                                            echo '<option value="'.$m.'">'.($m<10?('0'.$m):$m).'</option>';
                                                    }?>
                                                    </select>													
                                            </b></td></tr></table></td>
                                        </tr>
                                        <tr> <td>&nbsp;</td></tr>
                                        <tr>
                                                <td><div align="right"><b> Slot Length:&nbsp; </b></div></td>
                                                <td><nobr><b><input name="length" type="text" value="<?php echo "$length";?>"></b>
                                                &nbsp;(set examination slot length in minutes - e.g. "10")</td>
                                        </tr>	
                                        <tr>
                                                <td><div align="right"><b> Number of Examiners:&nbsp; </b></div></td>
                                                <td><nobr><b><input name="slots" type="text" value="<?php echo "$slots";?>"></b>
                                                &nbsp;(set number of available examination slots - e.g. "3")</td>
                                        </tr>										
                                        <tr> <td>&nbsp;</td></tr>
                                        <tr>
                                                <td><div align="right"><input name="login_btn" type="submit" id="login_btn2" value="Add"></div></td>
                                                <td><div align="left"><b></b></div></td>
                                        </tr>										
                                    </table>
                                </td>
                                <td width="99%"> </td>
                            </tr>
                        </table>
                    </form>

                <?php
                }		//add_exam_form

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    //include "php/date_check.php";
                    $ren_ses_id = $_POST['ren_ses_id'];
                    $date = $_POST['date'];
                    $s_h = $_POST['s_h'];
                    $s_m = $_POST['s_m'];
                    $e_h = $_POST['e_h'];
                    $e_m = $_POST['e_m'];
                    $length = $_POST['length'];
                    $slots  = $_POST['slots'];

                    $day = strtok($date, "/");
                    $month = strtok("/");
                    $year = strtok("/");

                    // check date & time

                    if (empty($ren_ses_id))
                    {
                        echo "Invalid Rendezvous Session!<br>Please create a rendezvous first. <br><br>";
                        add_exam_form($ren_ses_id, $date, $s_h, $s_m, $e_h, $e_m, $length, $slots);
                    }		
                    else if ( empty($date) || empty($length) || empty($slots) )
                    {
                        echo "All Fields have to be completed! <br><br>";
                        add_exam_form($ren_ses_id, $date, $s_h, $s_m, $e_h, $e_m, $length, $slots);
                    }
                    else if(!is_numeric($month) || !is_numeric($day) || !is_numeric($year) || !checkdate($month ,$day, $year))
                    {
                        echo "Exam Date is invalid! <br><br>";
                        add_exam_form($ren_ses_id, "", $s_h, $s_m, $e_h, $e_m, $length, $slots);
                    }
                    else if (!is_numeric($s_h) || !is_numeric($s_m) || $s_h > 23 || $s_h < 0 || $s_m>59 || $s_m<0) {
                        echo "The Start Time you entered is invalid! <br><br>";
                        add_exam_form($ren_ses_id, $date, "", "", $e_h, $e_m, $length, $slots);
                    }
                    else if (!is_numeric($e_h) || !is_numeric($e_m) || $e_h > 23 || $e_h < 0 || $e_m>59 || $e_m<0) {
                        echo "The End Time you entered is invalid! <br><br>";
                        add_exam_form($ren_ses_id, $date, $s_h, $s_m,  "",  "", $length, $slots);
                    }
                    else if (!is_numeric($length) || $length < 1)  {
                        echo "The Slot Length you entered is invalid! <br><br>";
                        add_exam_form($ren_ses_id, $date, $s_h, $s_m, $e_h, $e_m, "", $slots);
                    }
                    else if (!is_numeric($slots) || $slots < 1)  {
                        echo "The Number of Examiners you entered is invalid! <br><br>";
                        add_exam_form($ren_ses_id, $date, $s_h, $s_m, $e_h, $e_m, $length, "");
                    }
                    else if(strtotime($month."/".$day."/".$year." ".$s_h.":".$s_m) < time())
                    {
                        echo "The Examination Date you entered has past (that would be pretty cruel)! <br><br>";
                        add_exam_form($ren_ses_id, "", $s_h, $s_m, $e_h, $e_m, $length, $slots);
                    }
                    else
                    {
                        include ("txtDB/txt-db-api.php");
                        if (!file_exists(DB_DIR . "mydb")) {		// Database doesn't exist
                            echo 'No Database Found!<br>Please reset the Database from the Advanced tab.<br>';
                            exit;
                        }
                        $ren_start = strtotime($month."/".$day."/".$year." ".$s_h.":".$s_m);
                        $ren_end = strtotime($month."/".$day."/".$year." ".$e_h.":".$e_m);
                        $query = "insert into ren_periods (ren_ses_id, ren_start, ren_end, ren_length, ren_slots) 
                            values (".$ren_ses_id.", ".$ren_start.", ".$ren_end.", ".$length.", ".$slots." )";
                        //echo $query;
                        $db = new Database("mydb");
                        $rs = $db->executeQuery($query); 

                        echo '<br> Rendezvous Session has been succesfully created!' ;
                    }

                }
                else
                {
                    add_exam_form();
                }	
            }


            /************* Remove Exam Period *************/
            if ($_GET['op'] == 'rem_exam')		
            {				

                function del_exam_form1()
                {
                    echo '<form name="del_exam_form1" method="POST" action="">';
                    select_ren_fields($title="Select Rendezvous Session:", $button_text='Continue');
                    echo '<input type="hidden" value = "1" name="state">';
                    echo '</form>';
                }	// del_exam_form1

                function del_exam_form2($ren_ses_id)
                {
                ?>
                <form name="del_exam_form2" method="POST" action="">
                        <b>Select Exam Period: </b><br><br>
                        <select name="ren_per_id">
                <?php
                    $db = new Database("mydb");
                    $query = "select ren_per_id, ren_start, ren_end from ren_periods where ren_ses_id = ".$ren_ses_id;
                    $rs = $db->executeQuery($query); 
                    while($rs->next()){
                        //$date = date("D, j/n/Y", $rs->getCurrentValueByNr(1));
                        $date = date("j/n/Y", $rs->getCurrentValueByNr(1));
                        $s_time = date("H:i", $rs->getCurrentValueByNr(1));
                        $e_time = date("H:i", $rs->getCurrentValueByNr(2));
                        echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$date.'&nbsp;('.$s_time.' - '.$e_time.') </option>';
                    }
                ?>
                        </select><br><br><br>
                        <input name="review_btn" type="submit" id="review_btn" value="Remove">
                        <input type="hidden" value = "2" name="state">
                </form>
                <?php
                }		//del_exam_form2

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    if ($_POST['state'] == 1){
                        $ren_ses_id = $_POST['ren_ses_id'];
                        if (empty($ren_ses_id))
                        {
                            echo "Invalid Rendezvous Session!<br>Please create a rendezvous first. <br><br>";
                            del_exam_form1();
                        }		
                        else{
                            del_exam_form2($_POST['ren_ses_id']);
                        }
                    }
                    else if ($_POST['state'] == 2){
                        $ren_per_id = $_POST['ren_per_id'];
                        if (empty($ren_per_id))
                        {
                            echo "Invalid Exam Period!<br>There are no exam periods for this rendezvous. <br><br>";
                            //del_exam_form2(0);
                        }		
                        else{
                            $db = new Database("mydb");
                            $query = "delete from ren_periods where ren_per_id = ".$ren_per_id;
                            $rs = $db->executeQuery($query); 
                            $query = "delete from rendezvous where ren_per_id = ".$ren_per_id;
                            $rs2 = $db->executeQuery($query); 
                            if($rs == 1 && $rs2 !== false)
                                echo '<br><b>Exam Period has been succesfully removed!</b><br>Note: '.$rs2.' bookings belonging to this Exam Period were also deleted.' ;
                            else
                                echo '<br><b>Operation failed!</b> <br>Probably someone else already deleted this Exam Period.';
                        }
                    }		
                }
                else
                {
                    del_exam_form1();
                }			
            }

            /************* Close a submission *************/
            if ($_GET['op'] == 'close')		
            {

                function close_ren_form()
                {
                ?>
                <form name="close_ren_form" method="POST" action="">
                        <b>Select Rendezvous Session: </b><br><br>
                        <select name="ren_ses_id">
                <?php
                    $db = new Database("mydb");
                    $query = "select ren_ses_id, title from ren_sessions where active = 'A' or active = 'Y' order by ren_ses_id desc";
                    $rs = $db->executeQuery($query); 
                    while($rs->next())
                        echo '<option value = "'.$rs->getCurrentValueByNr(0).'">'.$rs->getCurrentValueByNr(0).':&nbsp;'.$rs->getCurrentValueByNr(1).' </option>';
                ?>
                        </select><br><br><br>
                        <input name="review_btn" type="submit" id="review_btn" value="Close">
                </form>	
                <?php
                }		//review_form		

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    $ren_ses_id = $_POST['ren_ses_id'];
                    if (empty($ren_ses_id))
                    {
                        echo "Invalid Rendezvous Session!<br>Please create a rendezvous first. <br><br>";
                        close_ren_form(	);
                    }		
                    else{
                        $db = new Database("mydb");
                        $query = "update ren_sessions 
                            set active = 'N'
                            where ren_ses_id = ".$ren_ses_id;
                        $rs = $db->executeQuery($query); 
                        if($rs == 1)
                            echo '<br> Rendezvous Session has been succesfully closed!' ;
                        else
                            echo '<br> Operation failed!';
                    }
                }
                else
                {
                    close_ren_form();
                }

            }

            /************* Delete a Rendezvous Session *************/
            if ($_GET['op'] == 'delete')		
            {			
                function del_ren_form()
                {
                    echo '<form name="del_ren_form" method="POST" action="">';
                    select_ren_fields($title="Select Rendezvous Session:", $button_text='Delete');
                    echo '</form>';
                }	// review_form

                if($_SERVER['REQUEST_METHOD'] == 'POST') 
                {
                    $ren_ses_id = $_POST['ren_ses_id'];
                    if (empty($ren_ses_id))
                    {
                        echo "Invalid Rendezvous Session!<br>Please create a rendezvous first. <br><br>";
                        del_ren_form(	);
                    }		
                    else{					
                        $db = new Database("mydb");
                        $query = "delete from ren_sessions where ren_ses_id = ".$ren_ses_id;
                        $rs = $db->executeQuery($query); 
                        $query = "delete from ren_periods where ren_ses_id = ".$ren_ses_id;
                        $rs2 = $db->executeQuery($query); 
                        $query = "delete from rendezvous where ren_ses_id = ".$ren_ses_id;
                        $rs3 = $db->executeQuery($query); 

                        if($rs == 1)
                            echo '<br><b>Rendezvous Session has been succesfully deleted!</b><br>
                            Note: '.$rs2.' Exam Periods and '.$rs3.' bookings belonging to this Rendezvous Session were also deleted.';
                        else
                            echo '<br><b>Operation failed!</b><br>Probably someone else already deleted this Rendezvous Session.';
                    }
                }
                else
                {
                    del_ren_form();
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
