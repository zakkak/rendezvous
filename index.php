<?php
session_start();
session_save_path(DB_DIR);

include("db.php");     // include txtDB
include("conf.php");   // settings

include("header.inc.php");
include "php/show_links.php";
include("https_check.inc.php");  // check for https and redirect if necessary

// Show menu depending on user status
if (isset($_SESSION['login']) && $_SESSION['full_path'] == realpath(".") )
{          // logged in
  if ($_SESSION['acc_type'] == 'admin') // admin users
  {
    show_links($left_links=array("Status", "index.php?op=status"),
               $right_links=array("Logout ".$_SESSION['login']." (admin)",
                                  "index.php?op=logout"), $_GET['op']);
  }
  else                          // simple users
  {
    show_links($left_links=array("Status", "index.php?op=status"),
               $right_links=array("Logout ".$_SESSION['login'],
                                  "index.php?op=logout"),
               $_GET['op']);
  }
}
else                            // not logged in
{
  show_links($left_links=array("Login", "index.php?op=login"),
             $right_links=array(), $_GET['op']);
}
echo '<br><br>';
// safe mode check
if( ini_get('safe_mode') )
{
  echo '<b>Warning:</b> PHP is running in SAFE MODE, which is known to cause '.
       'problems with this site. To disable SAFE MODE contact your web server '.
       'administrator.<br><br>';
}


/*************  REST OF PAGE  *****************/

if(check_db())
{
  if (isset($_SESSION['login']) && $_SESSION['full_path'] == realpath(".") )
  {          // logged in

    /************* Normal Home Page *************/
    if ($_GET['op'] == '')      // Normal Index Page
    {
      echo 'Welcome '.$_SESSION['login'].'!';
      //echo exec('gfinger '.$_SESSION['login'].' | line');

      if ($_SESSION['acc_type'] == 'user')    // simple user
      {
        echo ' You have the following options:<br><br><table><tr>';

        echo '<td align="right"><b> Rendezvous: </b></td>';
        echo '<td align="left">Select this tab to book/cancel a rendezvous.</td>';
        echo '</tr><tr>';
        echo '<td align="right"><b> Advanced: </b></td>';
        echo '<td align="left">Select this tab for advanced options.</td>';
        echo '</tr></table>';
      }
      else    // admin
      {
        echo '<br><br>You have the following options:<br><br>';
        echo '<table><tr>';

        echo '<td align="right"><b> Rendezvous: </b></td>';
        echo '<td align="left">Select this tab to manage Rendezvous Sessions.</td>';
        echo '</tr><tr>';
        echo '<td align="right"><b> Advanced: </b></td>';
        echo '<td align="left">Select this tab to perform Advanced Tasks.</td>';
        echo '</tr></table>';
      }

    }

    /************* Status Page *************/
    if ($_GET['op'] == 'status')        // Status Page
    {
      include ("txtDB/txt-db-api.php");
      $db = new Database("mydb");

      echo '<b> Rendezvous Sessions: </b>';
      $query = "select title, deadline from ren_sessions where active = 'Y' or (active = 'A' and deadline >= ".time().")";
      $rs = $db->executeQuery($query);
      if($rs->getRowCount() == 0)
      {
        echo "No available active rendezvous sessions.<br>";
      }
      else
      {
        echo $rs->getRowCount()." available active rendezvous sessions.<br><br>";
        echo '<table class="blue">';
        echo '<tr><th>Title</th><th>Deadline</th></tr><tbody>';
        while($rs->next())
        {
          echo '<tr><td>"'.$rs->getCurrentValueByNr(0).'" </td><td>'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(1)).'</td></tr>';
        }
        echo "</tbody></table>";
      }
    }

    /************* Logout **************/
    if ($_GET['op'] == 'logout')
    {
      if (!isset($_SESSION['login'])) {
        $url = "index.php"; // target of the redirect
        $delay = "1"; // 1 second delay
        echo "You were not logged in!";
        echo "Please wait...";
        echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'">';
      }
      else
      {
        unset($_SESSION['login']);
        //unset($_SESSION['name']);
        unset($_SESSION['acc_type']);
        unset($_SESSION['full_path']);
        $url = "index.php"; // target of the redirect
        $delay = "0"; // 1 second delay
        echo "You have succesfully logged out<br>";
        echo "Please wait...";
        echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'">';
      }
    }

    /************* Help for Users *************/
    //if ($_GET['op'] == 'help')
    //{
    //echo 'Log in and select a tab to see more options about each tab.';
    //}

  }
  else        // not logged in
  {
    /************* Login *************/
    if ($_GET['op'] == 'login')
    {
      function show_form($user_name="", $mailserver="")
      {
?>
  <div style="width:300px">
    Welcome! Please log in to continue.<br><br>

    <form name="login_form" method="POST" action="">
      <div class="input-group margin-bottom-sm">
        <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
        <input name=login class="form-control" type="text" placeholder="login" autofocus required>
      </div>

      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
        <input name="passwd" class="form-control" type="password" placeholder="password" required>
      </div>
      (mail server used for authentication: <?php echo $mailserver; ?>)
      <br>

      <b>Account Type:&nbsp;</b>
      <select class="selectpicker" data-style="btn-info" name="acc_type">
        <option value="user">Student</option>
        <option value="admin">Administrator</option>
      </select>

      <input name="login_btn" type="submit" id="Login" value="Login">
    </form>
  </div>
  <?php
  }   //show form

  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    //getting posted variables
    $login = $_POST['login'];
    $passwd = $_POST['passwd'];
    $acc_type = $_POST['acc_type'];

    if ( empty($login))
    {
      echo "<p>User Name cannot be empty! <br></p>";
      show_form($login, $mailserver);
    }
    else if( empty($passwd))
    {
      echo "<p>Password cannot be empty! <br></p>";
      show_form($login, $mailserver);
    }
    else    // verify password
    {
      $verified = false;

      //if(false)
      //$connection = ssh2_connect('gate1.csd.uoc.gr', 22);
      //if (ssh2_auth_password($connection, $login, $passwd))
      if($mbox=@imap_open("{".$mailserver.":993/imap/ssl/novalidate-cert}", $login, $passwd, OP_HALFOPEN))
      {
        imap_close($mbox);

        if($acc_type == 'user'){        // simple user verification
          $verified = true;
        }
        if($acc_type == 'admin')        // admin verification
        {
          if ( !is_readable($admins_file) || !$fh = fopen($admins_file, 'r')){
            echo 'Could not open the file that lists the administrators ("'.$admins_file.'")!<br>
                                    Please specify a valid file in the "conf.php" file ("'.realpath('.').'/conf.php").<br>
                                    Make sure that this file is readable and has the appropriate permissions.';
            exit;
          }
          //echo $fh;

          // check if specified username is present in admin file
		  
		  fclose($fh);
		  
		  $adminFileRead = file_get_contents($admins_file);
		  $adminDatabase = explode("\n", $adminFileRead);
		  
		  foreach($adminDatabase as $admin){
			  if($admin == $login){
				  $verified = true;
			  }
		  }
		  
          if (!$verified)                                         // You were not found in the administrators list
          {
            echo 'Your login ('.$login.') was not found in the list of administrators ("'.$admins_file.'")!<br>
                                    Please check the admins_file specified by the "conf.php" file ("'.realpath('.').'/conf.php"). ';
            exit;
          }

        }
      }
      if ($verified)      // user verified
      {
        $_SESSION['login'] = $login;
        $_SESSION['acc_type'] = $acc_type;
        $_SESSION['full_path'] = realpath(".");
        // I could add a lock for exclusive access,
        // but I don't really care if a few entries of
        // the log become corrupt.
        $fp = fopen(DB_DIR."log.txt", "a+");
        fwrite($fp, $_SESSION['login'].' logged in at '.date("F j, Y, G:i:s", time()).' as '.$_SESSION['acc_type']."\r\n");
        fclose($fp);
        //$_SESSION['name'] = ora_getcolumn($cursor, 1);
        $url = "index.php"; // target of the redirect
        $delay = "1"; // 1 second delay
        echo "<b>You have succesfully logged in.</b><br>";
        echo "Please wait...";

        echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'">';
      }
      else
      {
        echo "<p>Password incorrect! Please try again! <br></p>";
        show_form("", $mailserver);
      }
    }
  }
  else
  {
    show_form("", $mailserver);
  }
  }

  /************* Help for Strangers *************/
  //else if ($_GET['op'] == 'help')
  //{
  //echo 'Please log in first.!';
  //}
  else        // Go to Login page
  {
    echo 'Welcome! Please wait...';
    $delay=0;
    echo '<meta http-equiv="refresh" content="'.$delay.';url=index.php?op=login">';
  }

  }
  /************* End of page *************/
  }
  echo '</div>';  // content end
  include("footer.inc.html");
  echo '</div>';  // container end
  echo '</body></html>';

  ?>
