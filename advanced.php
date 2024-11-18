<?php
session_start();
session_save_path(DB_DIR);

include("db.php");     // include txtDB
include("conf.php");   // settings
include("https_check.inc.php");  // check for https and redirect if necessary
include("functions.php");
include("header.inc.php");

// safe mode check
if( ini_get('safe_mode') )
{
  echo '<b>Warning:</b> PHP is running in SAFE MODE, which is known to cause problems with this site. To disable SAFE MODE contact your web server administrator.<br><br>';
}

/*************  REST OF PAGE  *****************/

if(check_db())
{
  if($_SERVER['REQUEST_METHOD'] == "POST")
  {
    validateToken();
  }
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
                    <tr><td align="right"><b> Rendezvous History: </b></td><td align="left">Select this option to view all of your previously booked rendezvous.</td></tr>
                    </table>
                    ';
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
          echo '<table class="table table-striped">';
          echo '<tr><th>Rendezvous Session</th><th>Time</th><th>Slot</th></tr>';
          while($rs->next())
          {
            echo '<tr><td>"';
            $query = 'select title from ren_sessions where ren_ses_id = '.
                     $rs->getCurrentValueByNr(0);
            $rs2 = $db->executeQuery($query);
            if($rs2->next())		// title found
              echo $rs2->getCurrentValueByNr(0);
            else
              echo "unknown";
            echo '" </td><td>'.
                 date("F j, Y, g:i a", $rs->getCurrentValueByNr(1)).
                 '</td><td>'.
                 $rs->getCurrentValueByNr(2).'</td></tr>';
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
                    <tr><td align="right"><b> Rendezvous History: </b></td><td align="left">Get detailed information about all available Rendezvous Sessions.</td></tr>
                    <tr><td align="right"><b> SQL Query: </b></td><td align="left">Perform direct SQL Queries on the database.</td></tr>
                    <tr><td align="right"><b> Reset System: </b></td><td align="left">Deletes everything and resets the whole system! </td></tr>
                    </table>
                    ';
      }

      /************* System Log *************/
      if ($_GET['op'] == 'view_log')
      {
        if (file_exists(DB_DIR."log.txt"))
        {
          /* $temp_log = 'temp_log.txt'; */
          /* $command = 'tac '.DB_DIR.'log.txt > /tmp/temp_log.txt'; */
          /* passthru($command); */

          if ($fp = fopen(DB_DIR."log.txt", "r"))
          {
            echo '<b>System Log:</b>&nbsp;(';
            echo exec('wc -l < '.DB_DIR.'log.txt');
            echo ' entries )<br>';
            echo '<textarea name="log" cols="80" rows="20" readonly="readonly">';

            /* $fp = fopen("/tmp/temp_log.txt", "r"); */
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
  <?php csrfToken(); ?>
    <b><font size = "4" >SQL Query : </font></b><br><br>
    <textarea name="textarea" cols="50" rows="5"
              wrap="PHYSICAL"><?php echo "$query";?></textarea></td> <br><br>
    <input type="submit" name="Submit" value="Submit">
  </form>
  <?php
  }	// query_form

  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    include ("txtDB/txt-db-api.php");
    if (!file_exists(DB_DIR . "mydb"))
    {		// Database doesn't exist
      echo 'No Database Found!<br>Please contact your instructor or teaching assistants.<br>';
    }
    else
    {
      $query = stripslashes($_POST['textarea']);
      $db = new Database("mydb");
      checkValidQuery($query);
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
    <?php csrfToken(); ?>
      <b>Are you sure you want to reset the System?</b><br>
      Warning: All database files will be deleted. <br><br>
      <input class="btn btn-danger" name="yes_btn" type="submit"
             id="yes_btn" value="Reset">
    </form>
    <?php
    }		//reset_form

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      //include ("db.php");
      reset_db();

      // log the user out!
      unset($_SESSION['login']);
      unset($_SESSION['email']);
      unset($_SESSION['name']);
      unset($_SESSION['acc_type']);

      echo "<br>System was successfully reset!<br>";
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
      $delay=0;
      echo '<meta http-equiv="refresh" content="'.
           $delay.';url=index.php?op=login">';
    }

    }

    /************* End of page *************/
    echo '</div>';	// content end
    include("footer.inc.html");
    echo '</div>';	// container end
    echo '</body></html>';

    ?>
