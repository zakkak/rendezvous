<?php
function print_table($rs)
{
  if($rs instanceof ResultSet)
  {
    echo '<table class="table table-striped"><thread>';
    //echo "<table border=\"1\" width=\"80%\"><tr>";
    $ColumnNames = $rs->getColumnNames();
    for($col = 0; $col < $rs->getRowSize(); $col++)
    {
      //echo "<th>".ora_columnname($cursor, $col)."</th>";
      echo "<th><b>";
      echo filter_var($ColumnNames[$col],FILTER_SANITIZE_SPECIAL_CHARS);
      echo "</b></th>";
    }
    echo "</thread><tbody>";

    while($rs->next())
    {
      echo '<tr>';
      for($col = 0; $col < $rs->getRowSize(); $col++)
        echo '<td>'.$rs->getCurrentValueByNr($col).'</td>';
      echo '</tr>';
    }
    echo '</tbody></table>';
  }
  else if ($rs === false)
  {
    echo 'Query Failed!';
  }
  else
  {
    echo 'Query Executed!';
  }
}

function print_rendezvous($rs)
{
  echo '<table class="table table-striped">';
  echo '<thread><th>Title</th><th>Deadline</th><th>State</th><th>Deactivation</th></thread>';
  echo '<tbody>';
  while($rs->next())
  {
    echo '<tr>';
    echo '<td>'.$rs->getCurrentValueByNr(1).' </td>';
    echo '<td>'.date("F j, Y, g:i a", $rs->getCurrentValueByNr(2)).'</td>';
    if ($rs->getCurrentValueByNr(3) == 'Y' ||
        ($rs->getCurrentValueByNr(3) == 'A' &&
         $rs->getCurrentValueByNr(2) >= time()) )
    echo '<td>Active</td>';
    else
      echo '<td>Closed</td>';
    if( $rs->getCurrentValueByNr(3) == 'A')
      echo '<td>Automatic</td>';
    else
      echo '<td>Manual</td>';
    echo '</tr>';
  }
  echo "</tbody></table>";
}

?>
