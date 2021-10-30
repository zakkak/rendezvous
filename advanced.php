<?php

require_once("const.php");
require_once("sessions/handler.php");
session_save_path(DB_FILE);
session_set_save_handler(new SQLite3Session());
session_start();

include("db.php");     // Include the database
include("conf.php");   // settings
include("https_check.inc.php");  // check for https and redirect if necessary

include("header.inc.php");
include "php/show_links.php";

// safe mode check
if( ini_get('safe_mode') )
{
  echo '<b>Warning:</b> PHP is running in SAFE MODE, which is known to cause problems with this site. To disable SAFE MODE contact your web server administrator.<br><br>';
}

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
                    <tr><td align="right"><b> Rendezvous History: </b></td><td align="left">Select this option to view all of your previously booked rendezvous.</td></tr>
                    </table>
                    ';
      }

      /************* Rendezvous History *************/
      if ($_GET['op'] == 'ren_hist')
      {

        echo '<b> Rendezvous History: </b>';
        $bookings = get_user_bookings($_SESSION['login']);
        if (!$bookings)
        {
          exit("A problem occurred in the server.");
        }

        if(count($bookings) === 0)
        {
          echo "You have never booked a rendezvous.<br>";
        }
        else
        {
          echo 'You have booked '.count($bookings).' appointments.<br><br>';
          echo '<table class="table table-striped">';
          echo '<tr><th>Rendezvous Session</th><th>Time</th><th>Slot</th></tr>';
          foreach($bookings as $book)
          {
            echo '<tr><td>"';
            $sess = get_session($book['ren_ses_id']);
            if (!$sess)
            {
              echo 'Unknown';
            }
            else
            {
              echo $sess['title'];
            }
            echo '</td><td>';
            echo date("F j, Y, g:i a", $book['ren_time']);
            echo '</td><td>';
            echo $book['ren_slot'];
            echo '</td></tr>';
          }
          echo '</table>';
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
        $sess = get_all_sessions();
        if (count($sess) === 0)
        {
          echo "No Rendezvous Sessions found in the database!.<br>";
        }
        else
        {
          echo "Found " . count($sess) . " Rendezvous Sessions in the database.<br><br>";
          echo "<table class=\"table table-striped\">";
          echo "<thread><th>Title</th><th>Deadline</th><th>State</th><th>Deactivation</th></thread>";
          echo "<tbody>";
          foreach($sess as $session)
          {
            echo "<tr>";
            echo "<td>" . $session['title'] . "</td>";
            echo "<td>" . date("F j, Y, g:i a", $session['deadline']) . "</td>";
            if ($session['active'] === "Y" || $session['active'] === "A" && $session['deadline'] >= time())
            {
              echo "<td>Active</td>";
            }
            else
            {
              echo "<td>Closed</td>";
            }
            if ($session['active'] === "A")
            {
              echo "<td>Automatic</td>";
            }
            else
            {
              echo "<td>Manual</td>";
            }
            echo "</tr>";
          }
          echo "</tbody></table>";
        }
      }

      /************* SQL Query *************/
      if ($_GET['op'] == 'query')
      {

        function query_form($query="")
        {
?>
  <form method="post" action="">
    <b>SQL Query : </b><br><br>
    <textarea name="sqlquery" cols="50" rows="5"
              wrap="PHYSICAL"><?php echo stripslashes($_POST['sqlquery']);?></textarea></td> <br><br>
    <input type="submit" name="Submit" value="Submit">
  </form>
<?php
        }	// query_form

  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    $results = raw_db_query(stripslashes($_POST['sqlquery']));
    if (!$results)
    {
      exit("Failed to execute query.");
    }
    require_once("print.php");
    echo "<b>Your SQL Query returned the following results:</b><br><br>";
    print_table($results);
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
      <b>Are you sure you want to reset the System?</b><br>
      Warning: All database files will be deleted. <br><br>
      <input class="btn btn-danger" name="yes_btn" type="submit"
             id="yes_btn" value="Reset">
    </form>
    <?php
    }		//reset_form

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      reset_db();

      // log the user out!
      unset($_SESSION['login']);
      unset($_SESSION['email']);
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
