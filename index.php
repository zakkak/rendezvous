<?php

require_once("const.php");
require_once("sessions/handler.php");
session_save_path(DB_FILE);
session_set_save_handler(new SQLite3Session());
session_start();

include("db.php");     // include database
include("conf.php");   // settings

include("header.inc.php");
include "php/show_links.php";
include("https_check.inc.php");  // check for https and redirect if necessary

function ldaplogin($mail, $pass) {
    // Establish link with LDAP server
    global $ldap_server, $ldap_port, $ldap_bdn;
    $con = ldap_connect($ldap_server, $ldap_port)
        or die ("Could not connect to $ldap_server.");
    if (!is_resource($con)) {
        trigger_error("Unable to connect to $ldap_server.", E_USER_WARNING);
        return array(false, '');
    }
    ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($con, LDAP_OPT_REFERRALS, 0);
    
    $bind=false;
    
    // First bind anonymously
    $bind = ldap_bind($con, "", "");
    if (!$bind) {
        trigger_error("Unable to anonymously bind to $ldap_server.", E_USER_WARNING);
        return array(false, '');
    }
    
    // Search for the user
    $search=@ldap_search($con, $ldap_bdn, "(mail=$mail)", array('dn', 'mail'));
    $entry =@ldap_first_entry($con, $search);
    
    if (!$entry)
        return array(false, '');

    // Get Mail found from ldap to replace the one user gave for consistency
    $ldap_mail = @ldap_get_values($con, $entry, 'mail')[0];

    $bind = false;

    $dn   = @ldap_get_dn($con, $entry);
    $bind = ldap_bind($con, $dn, $pass);

    $lastError = ldap_errno($con);
    ldap_close($con);

    return array($bind,$ldap_mail);
}

function show_form($msg="")
{
?>
  <div style="width:300px">
<?php echo $msg; ?>
    <br><br>

    <form name="login_form" method="POST" action="" role="form">
      <div class="input-group margin-bottom-sm">
        <span class="input-group-addon"><i class="fa fa-at fa-fw"></i></span>
        <input name=login class="form-control" type="text"
               placeholder="e-mail" autofocus required>
      </div>

      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
        <input name="passwd" class="form-control" type="password"
               placeholder="password" required>
      </div>

      <b>Account Type:&nbsp;</b>
      <label class="radio-inline">
        <input type="radio" name="acc_type" value="user" checked="checked">Student
      </label>
      <label class="radio-inline">
        <input type="radio" name="acc_type" value="admin">Administrator
      </label>

      <input class="btn btn-primary btn-sm" name="login_btn" type="submit" 
             id="Login" value="Login">
    </form>
  </div>
  <?php
}   //show form

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
    /*   echo 'Welcome '.$_SESSION['login'].'!'; */
    /*   //echo exec('gfinger '.$_SESSION['login'].' | line'); */

    /*   echo 'You have the following options:<br><br><table><tr>'; */
    /*   echo '<td align="right"><b> Rendezvous: </b></td>'; */

    /*   if ($_SESSION['acc_type'] == 'user')    // simple user */
    /*   { */
    /*     echo '<td align="left">Select this tab to book/cancel a rendezvous.</td>'; */
    /*     echo '</tr><tr>'; */
    /*     echo '<td align="right"><b> Advanced: </b></td>'; */
    /*     echo '<td align="left">Select this tab for advanced options.</td>'; */
    /*     echo '</tr></table>'; */
    /*   } */
    /*   else    // admin */
    /*   { */
    /*     echo '<td align="left">Select this tab to manage Rendezvous Sessions.</td>'; */
    /*     echo '</tr><tr>'; */
    /*     echo '<td align="right"><b> Advanced: </b></td>'; */
    /*     echo '<td align="left">Select this tab to perform Advanced Tasks.</td>'; */
    /*     echo '</tr></table>'; */
    /*   } */

    /* } */

    /* /\************* Status Page *************\/ */
    /* if ($_GET['op'] == 'status')        // Status Page */
    /* { */
      $sessions = get_available_sessions();
      if (!$sessions)
      {
        exit("A problem occurred in the server.");
      }

      echo '<b> Rendezvous Sessions: </b>';

      if(count($sessions) === 0)
      {
        echo "No available active rendezvous sessions.<br>";
      }
      else
      {
        echo count($sessions)." available active rendezvous sessions.<br><br>";
        echo '<table class="table table-striped">';
        echo '<tr><th>Title</th><th>Deadline</th></tr><tbody>';
        foreach ($sessions as $rs)
        {
          echo '<tr><td>"'.$rs["title"].'" </td><td>'.date("F j, Y, g:i a", $rs["deadline"]).'</td></tr>';
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
        unset($_SESSION['email']);
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

  }
  else        // not logged in
  {
    /************* Login *************/
    if ($_GET['op'] == 'login')
    {

  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    //getting posted variables
    $login = $_POST['login'];
    $passwd = $_POST['passwd'];
    $acc_type = $_POST['acc_type'];

    if ( empty($login))
    {
      show_form("User Name cannot be empty!");
    }
    else if( empty($passwd))
    {
      show_form("Password cannot be empty!");
    }
    else    // verify password
    {
      $verified = false;

      $results   = ldaplogin($login, $passwd);
      $bind      = $results[0]; // Get the bind return Value
      $ldap_mail = $results[1]; // Get the mail from ldap for consistency

      if ($bind)
      {

        if($acc_type == 'user'){        // simple user verification
          $verified = true;
        }
        if($acc_type == 'admin')        // admin verification
        {
          foreach($admins as $admin){
            if($admin === $login){
              $verified = true;
              break;
            }
          }

          if (!$verified) // You were not found in the administrators list
          {
            echo 'Your login ('.$login.') was not found in the list of administrators!<br>';
            echo 'Please check the list of admins specified in the "conf.php" file ('.realpath('.').'/conf.php).';
            exit;
          }

        }
      }

      if ($verified)      // user verified
      {
        $email = $ldap_mail;
        $login = explode("@", $email);
        $_SESSION['login'] = $login[0];
        $_SESSION['email'] = $email;
        $_SESSION['acc_type'] = $acc_type;
        $_SESSION['full_path'] = realpath(".");
        // I could add a lock for exclusive access,
        // but I don't really care if a few entries of
        // the log become corrupt.
        $fp = fopen(DB_DIR."log.txt", "a+");
        fwrite($fp, $email.' logged in at '.date("F j, Y, G:i:s", time()).' as '.$acc_type."\r\n");
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
        show_form("E-mail or Password incorrect! Please try again!");
      }
    }
  }
  else
  {
    show_form("Welcome! Please log in to continue.");
  }
    }

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
