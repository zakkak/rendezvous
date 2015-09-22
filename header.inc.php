<!DOCTYPE html>
<html>
  <head>
    <title>Rendezvous</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="SHORTCUT ICON" HREF="<?php echo $favicon_path;?>">
    <link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.4/cosmo/bootstrap.min.css"
          rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"
          rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link type="text/css" rel="stylesheet" href="theme/style.css">
    <script type="text/javascript" src="js/calendarDateInput.js">
     /***********************************************
      * Jason's Date Input Calendar- By Jason Moon
      * http://calendar.moonscript.com/dateinput.cfm
      * Script featured on and available at
      * http://www.dynamicdrive.com
      * Keep this notice intact for use.
      ***********************************************/
    </script>
  </head>

  <body>
    <div id="container"><div id="content" align="center">

      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="middle"><b><a class="huge" href="index.php">
            <nobr>Rendezvous</nobr>
          </a></b></td>
          <td style="color:#BCBEBC;" valign="bottom">
            <span class="version"><nobr>version 3.0.0</nobr></span>
          </td>
          <td class="title" valign="center" align="center" width="99%">
            <?php echo $title; ?>
          </td>
          <td class="university" align="right" valign="bottom">
            <div>
              <?php if($affil1_link==""){echo '<nobr>'.$affil1;}
                    else { echo '<a class="links" href="'.$affil1_link.'"><nobr>'.$affil1.'</a>';}?>
            </div>
            <div>
              <?php if($affil2_link==""){echo '<nobr>'.$affil2;}
                    else { echo '<a class="links" href="'.$affil2_link.'"><nobr>'.$affil2.'</a>';}?>
            </div>
            <div>
              <?php if($affil3_link==""){echo '<nobr>'.$affil3;}
                    else { echo '<a class="links" href="'.$affil3_link.'"><nobr>'.$affil3.'</a>';}?>
            </div>
          </td>
          <td>&nbsp;&nbsp;</td>
          <td align="right" valign="bottom">
            <?php if($logo_link==""){echo '<img border="none" src="'.$logo_path.'" width="55" height="55" />';}
                  else { echo '<a href="'.$logo_link.'"><img border="none" src="'.$logo_path.'" width="55" height="55" /></a>';}?>
          </td>
        </tr>
      </table>

      <!-- highlight the correct menu entry -->
      <?php
      $currentFile = $_SERVER["SCRIPT_NAME"];
      $parts = Explode('/', $currentFile);
      $currentFile = $parts[count($parts) - 1];
      ?>

<?php if (isset($_SESSION['login'])) { ?>
      <nav class="navbar navbar-inverse">
        <div class="container-fluid">
          <!--<div class="navbar-header">
          <a class="navbar-brand" href="#">Rendezvous</a>
          </div>-->
          <div id="navbar">
          <ul class="nav navbar-nav">
          <li <?php if($currentFile=='index.php') echo 'class="active"'?>>
          <a href="index.php">Home</a></li>
          <li class="dropdown <?php if($currentFile=='rendezvous.php') echo 'active'?>">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rendezvous<span class="caret"></span></a>
            <ul class="dropdown-menu">
<?php if (isset($_SESSION['acc_type']) && $_SESSION['acc_type'] == 'admin') { ?>
              <li><a href="rendezvous.php?op=create">Create</a></li>
              <li><a href="rendezvous.php?op=edit">Edit</a></li>
              <li><a href="rendezvous.php?op=review">Review</a></li>
              <li><a href="rendezvous.php?op=add_exam">Add Slots</a></li>
              <li><a href="rendezvous.php?op=rem_exam">Remove Slots</a></li>
              <li><a href="rendezvous.php?op=close">Close</a></li>
              <li><a href="rendezvous.php?op=delete">Delete</a></li>
<?php } else { ?>
              <li><a href="rendezvous.php?op=book">Book</a></li>
              <li><a href="rendezvous.php?op=review">Review</a></li>
              <li><a href="rendezvous.php?op=cancel">Cancel</a></li>
<?php } ?>
            </ul>
          </li>
          <li class="dropdown <?php if($currentFile=='advanced.php') echo 'active'?>">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Advanced<span class="caret"></span></a>
            <ul class="dropdown-menu">
<?php if (isset($_SESSION['acc_type']) && $_SESSION['acc_type'] == 'admin') { ?>
              <li><a href="advanced.php?op=view_log">View Log</a></li>
              <li><a href="advanced.php?op=ren_hist">Rendezvous History</a></li>
              <li><a href="advanced.php?op=query">SQL Query</a></li>
              <li><a href="advanced.php?op=reset">Reset System</a></li>
<?php } else { ?>
              <li><a href="advanced.php?op=ren_hist">Rendezvous History</a></li>
<?php } ?>
            </ul>
          </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
          <li <?php if($currentFile=='about.php') echo 'class="active"'?>>
          <a href="about.php">About</a></li>
          <li>
            <a href="index.php?op=logout">Logout:&nbsp <?php echo $_SESSION['login'];?></a></li>
          </ul>
          </div>
        </div>
      </nav>
<?php } ?>
