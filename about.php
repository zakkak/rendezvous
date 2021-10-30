<?php

require_once("const.php");
require_once("sessions/handler.php");
session_save_path(DB_FILE);
session_set_save_handler(new SQLite3Session());
session_start();

include("db.php");     // Include the database
include("conf.php");   // Settings

include("header.inc.php");
include("php/show_links.php");
include("https_check.inc.php");  // Use HTTPS exclusively

?>

<br/>
To get a copy or contribute to the development of the project visit
<a href="https://github.com/zakkak/rendezvous">
  it on GitHub
</a>.
<br/>
<br/>
To get a copy of the license click <a href="./LICENSE">here</a>.

<?php
/************* End of page *************/
echo '</div>';	// content end
include("footer.inc.html");
echo '</div>';	// container end
echo '</body></html>';
?>
