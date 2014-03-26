<!DOCTYPE html>
<html>
<head>
<title>Rendezvous</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="SHORTCUT ICON" HREF="<?php echo $favicon_path;?>">
<link type="text/css" rel="stylesheet" href="theme/style.css">
<script type="text/javascript" src="js/calendarDateInput.js">
/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/
</script>
<link href="//netdna.bootstrapcdn.com/bootswatch/3.1.1/spacelab/bootstrap.min.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</head>

<body>
<div id="container"><div id="content" align="center">

<hr size="4" color="#006699" noshade>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="middle"><b><a class="huge" href="index.php"><nobr>Rendezvous</nobr></a></b></td>
	<td style="color:#BCBEBC;" valign="bottom"><span class="version"><nobr>version 1.8.0</td>
    <td class="title" valign="center" align="center" width="99%"><?php echo $title; ?></td>
    <td class="university" align="right" valign="bottom">
      <div><?php if($affil1_link==""){echo '<nobr>'.$affil1;}
                 else { echo '<a class="links" href="'.$affil1_link.'"><nobr>'.$affil1.'</a>';}?></div>
      <div><?php if($affil2_link==""){echo '<nobr>'.$affil2;}
                 else { echo '<a class="links" href="'.$affil2_link.'"><nobr>'.$affil2.'</a>';}?></div>
      <div><?php if($affil3_link==""){echo '<nobr>'.$affil3;}
                 else { echo '<a class="links" href="'.$affil3_link.'"><nobr>'.$affil3.'</a>';}?></div>
    </td>
    <td>&nbsp;&nbsp;</td>
      <td align="right" valign="bottom">
        <?php if($logo_link==""){echo '<img border="none" src="'.$logo_path.'" width="55" height="55" />';}
               else { echo '<a href="'.$logo_link.'"><img border="none" src="'.$logo_path.'" width="55" height="55" /></a>';}?>
      </td>
  </tr>
</table>
<hr size="3" color="#006699" noshade>
<br>

<!-- highlight the correct menu entry -->
<?php
    $currentFile = $_SERVER["SCRIPT_NAME"];
    $parts = Explode('/', $currentFile);
    $currentFile = $parts[count($parts) - 1];
?>

<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
  <td width="280"><nobr><ul id="navlist">
    <!-- CSS Tabs -->
    <li><a <?php if($currentFile=='index.php')echo 'id="current"'?> href="index.php">Home</a></li>
    <li><a <?php if($currentFile=='rendezvous.php')echo 'id="current"'?> href="rendezvous.php">Rendezvous</a></li>
    <li><a <?php if($currentFile=='advanced.php')echo 'id="current"'?> href="advanced.php">Advanced</a></li>
  </ul></td>

  <td align="right" valign="bottom" bgcolor="#FFFFFF"></td>
  <td width="60" align="right">
    <ul id="navlist">
      <!-- CSS Tabs -->
      <li><a <?php if($currentFile=='about.php')echo 'id="current"'?> href="about.php">About</a></li>
    </ul>
  </td>
</tr></table>
