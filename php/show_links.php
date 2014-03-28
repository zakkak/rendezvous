<?php
function show_links($left_links=array(""), $right_links=array(""), $highlight='')
{
  echo '<table width="100%" border="0" align="left" cellpadding="0'.
       '" cellspacing="0" bgcolor="#006699"><tr><td height = "30"'.
       'bgcolor="#006699">  &nbsp;&nbsp;  </td>';
  // Typwnw aristera links
  for($i=0; $i < count($left_links); $i = $i + 2)
  {
    echo    '<td bgcolor="#006699"> <div align = "left" ><span id="sublinks"><a ';
    if ($highlight == substr(strstr($left_links[$i+1], "op="), 3)  )
      echo ' id="current" ';
    echo ' href="'.$left_links[$i+1].'"><nobr>'.
         $left_links[$i].'</a></span></div></td>';
    // If this is not the last link
    if ($i+2 < count($left_links) ) {
      echo '<td  bgcolor="#006699"> <div align="left"><span class="seperators">'.
           '&nbsp;&nbsp;<strong>|</strong>&nbsp;&nbsp;</span></div></td>';
    }
  }
  // typwnw dexia links
  echo '<td width="99%" bgcolor="#006699"><div align = "center">&nbsp;</div></td>';
  for($i=0; $i < count($right_links); $i += 2) {
    echo    '<td  bgcolor="#006699"><div align = "right"><span id="sublinks"><a ';
    if ($highlight == substr(strstr($right_links[$i+1], "op="), 3)  )
      echo ' id="current"';
    echo ' href="'.$right_links[$i+1].'"><nobr>'.$right_links[$i].'</a></span></div></td>';
    if ($i+2 < count($right_links))         // this is not the last link - add seperator
      echo  '<td  bgcolor="#006699"> <div align="right"><span class="seperators">'.
            '&nbsp;&nbsp;<strong>|</strong>&nbsp;&nbsp;</span></div></td>';
    else
      echo  '<td  bgcolor="#006699"> <div align="right"><span class="seperators">'.
            '&nbsp;&nbsp;</span></div></td>';
  }
  echo '</tr></table>';
}   // show_links

?>
