<?php

// print_table() prints a SQL query result as an HTML table.
function print_table($result)
{
  echo '<table class="table table-striped"><thead>';
  if (count($result) === 0)
  {
    echo '<tr><th>No data returned</th></tr>';
    echo '</thead></table>';
  }

  // Print the column names
  echo '<tr>';
  foreach ($result[0] as $k => $v)
  {
    echo '<th>' . $k . '</th>';
  }
  echo '</tr></thead><tbody>';

  // Print the row values
  foreach ($result as $k => $v)
  {
    echo '<tr>';
    foreach ($v as $col => $row)
    {
      echo '<td>' . $row . '</td>';
    }
    echo '</tr>';
  }
  echo '</tbody></table>';
}

?>