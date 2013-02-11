<!--

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

-->

<hr size="4" color="#006699" noshade>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif" valign="middle"><b><a class="huge" href="index.php"><nobr>Submit - Rendezvous</nobr></a></b></td>
	<!-- <td valign="top"><span class="copyright"><nobr><sup>&nbsp;&copy;</sup></span></td> -->
	<!-- <td style="color:#BCBEBC;" valign="bottom"><span class="version"><nobr>version 1.5.00</td> -->
    <td class="title" valign="bottom" align="center" width="99%"><?php echo $title; ?></td>
    <td class="university" align="right" valign="bottom">
      <div><?php if($affil1_link==""){echo '<nobr>'.$affil1;} else { echo '<a class="links" href="'.$affil1_link.'"<nobr>'.$affil1.'</a>';}?></div>
      <div><?php if($affil2_link==""){echo '<nobr>'.$affil2;} else { echo '<a class="links" href="'.$affil2_link.'"<nobr>'.$affil2.'</a>';}?></div>
      <div><?php if($affil3_link==""){echo '<nobr>'.$affil3;} else { echo '<a class="links" href="'.$affil3_link.'"<nobr>'.$affil3.'</a>';}?></div>
    </td>
      <td>&nbsp;&nbsp;</td>
      <div></div>
      <td align="right" valign="bottom">
        <?php if($logo_link==""){echo '<img border="none" src="'.$logo_path.'" width="55" height="55" />';}
               else { echo '<a href="'.$logo_link.'"><img border="none" src="'.$logo_path.'" width="55" height="55" /></a>';}?>
      </td>
  </tr>
</table>
<hr size="4" color="#006699" noshade>
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
<?php
    if($submit_enabled) {
      echo '<li><a '; if($currentFile=='submit.php')echo 'id="current"'; echo 'href="submit.php">Submit</a></li>';
    }
?>
    <li><a <?php if($currentFile=='rendezvous.php')echo 'id="current"'?> href="rendezvous.php">Rendezvous</a></li>
    <li><a <?php if($currentFile=='advanced.php')echo 'id="current"'?> href="advanced.php">Advanced</a></li>
  </ul></td>

  <td align="right" valign="bottom" bgcolor="#FFFFFF">
  <div class="time"><nobr> <script>
    document.writeln('<nobr><div id="servertime"></div>');
  </script></div></td>
  <td width="60" align="right">
    <ul id="navlist">
      <!-- CSS Tabs -->
      <li><a <?php if($currentFile=='about.php')echo 'id="current"'?> href="about.php">About</a></li>
    </ul>
  </td>
</tr></table>

