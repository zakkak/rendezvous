<?php

require_once("const.php");

// create_db() creates the database if it does not exist, and initializes it
// with the Rendezvous schema. It returns true upon success, or false if there
// was an error. Please note that it prints HTML to the page when called.
function create_db()
{
  echo '<br>Please wait...<br> Creating Database:<br>';

  $db = new SQLite3(DB_FILE);
  echo 'Creating Tables...<br>';
  // The exec() function returns true on success, false on failure. The $ok
  // variable contains whether or not the result was okay. Each query is
  // bitwise-AND'd with the previous result. That way all need to return
  // true to proceed, if any returns false, we can handle the error.
  $ok = true;
  $ok &= $db->exec("CREATE TABLE ren_sessions (ren_ses_id INTEGER, title TEXT, deadline INTEGER, active TEXT)");
  $ok &= $db->exec("CREATE TABLE ren_periods (ren_per_id INTEGER, ren_ses_id INTEGER, ren_start INTEGER, ren_end INTEGER, ren_length INTEGER, ren_slots INTEGER)");
  $ok &= $db->exec("CREATE TABLE rendezvous (ren_ses_id INTEGER, ren_per_id INTEGER, login TEXT, ren_time INTEGER, ren_slot INTEGER, book_time INTEGER)");
  if (!$ok)
  {
    echo 'An error occurred during table creation. Cannot proceed.<br>';
    return false;
  }
  echo '&nbsp;&nbsp;&nbsp;&nbsp;  ren_sessions, ren_periods, rendezvous<br>';
  echo '<br> DONE!';

  if (!$db->close())
  {
    echo 'An error occurred while closing the connection to the database. Cannot proceed.<br>';
    return false;
  }

  if (!chmod(DB_FILE, 0700))
  {
    echo 'An error occurred while changing database permissions to 0700. Cannot proceed.<br>';
    return false;
  }

  return true;
}

// check_db() checks whether the database exists and has the appropriate file
// permissions set. If the database does not exist, it is created. If the
// permissions are wrong, an attempt is made to fix them. If anything fails,
// an error is printed, and the function exit()s. The function will redirect
// the user if it changed the database to a working state.
function check_db()
{
  $db = new SQLite3(DB_FILE, SQLITE3_OPEN_READONLY);
  if (!$db->close())
  {
    if (!create_db())
    {
      exit('Database creation failed. Please contact an administrator.');
    }
    echo '<meta http-equiv="refresh" content="' . REDIRECT_DELAY . ';url=index.php">';
    echo '</body>';
    echo '</html>';
    exit();
  }

  $dbperm = fileperms(DB_FILE);
  if (!$dbperm)
  {
    exit('Failed to check database permissions. Please contact an administrator.');
  }

  // Check if the permission bits are rwx------
  // We mask with 0777 to only get those bits, and not any special ones
  if (($dbperm & 0777) !== 0700)
  {
    if (!chmod(DB_FILE, 0700))
    {
      exit("Failed to set database permissions to 0700. Please do this manually and try again.");
    }
  }
}

// reset_db() deletes the database. It exit()s if it fails to do so.
function reset_db()
{
  if (!unlink(DB_FILE))
  {
    exit("Failed to delete database. Please do this manually.");
  }
}

// get_available_sessions() returns a list of all active and available
// rendezvous sessions, or false if an error occurred.
function get_available_sessions()
{
  $db = new SQLite3(DB_FILE, SQLITE3_OPEN_READONLY);

  $statement = $db->prepare("SELECT title, deadline FROM ren_sessions WHERE active = 'Y' OR (active = 'A' AND deadline >= :timenow)");
  $statement->bindValue(":timenow", time(), SQLITE3_INTEGER);
  $res = $statement->execute();
  if (!$res)
  {
    return false;
  }

  $sessions = array();
  while ($row = $res->fetchArray(SQLITE3_ASSOC) !== false)
  {
    array_push($sessions, $row);
  }

  return $sessions;
}

// get_user_bookings() returns a list of all the bookings that the
// particular user has, or false if an error occurred.
function get_user_bookings($user)
{
  $db = new SQLite3(DB_FILE, SQLITE3_OPEN_READONLY);

  $statement = $db->prepare("SELECT * FROM rendezvous WHERE login = :user");
  $statement->bindValue(":user", $user, SQLITE3_TEXT);
  $res = $statement->execute();
  if (!$res)
  {
    return false;
  }

  $bookings = array();
  while ($row = $res->fetchArray(SQLITE3_ASSOC) !== false)
  {
    array_push($bookings, $row);
  }

  return $bookings;
}

// get_session() returns a rendezvous session that has the particular
// id, or false if an error occurred or the session wasn't found.
function get_session($id)
{
  $db = new SQLite3(DB_FILE, SQLITE3_OPEN_READONLY);

  $statement = $db->prepare("SELECT * FROM ren_sessions WHERE ren_ses_id = :id");
  $statement->bindValue(":id", $id, SQLITE3_TEXT);
  $res = $statement->execute();
  if (!$res)
  {
    return false;
  }

  return $res->fetchArray(SQLITE3_ASSOC);
}

// get_all_sessions() returns all rendezvous sessions from the database,
// or false if an error occurred.
function get_all_sessions()
{
  $db = new SQLite3(DB_FILE, SQLITE3_OPEN_READONLY);

  $res = $db->query("SELECT * FROM ren_sessions");
  if (!$res)
  {
    return false;
  }

  $sessions = array();
  while ($row = $res->fetchArray(SQLITE3_ASSOC) !== false)
  {
    array_push($sessions, $row);
  }

  return $sessions;
}

// raw_db_query() runs an arbitrary query in the database and returns the
// array of all the results, or false if an error occurred. The query is
// only allowed to read data, not to write any. Be very careful with this!
function raw_db_query($query)
{
  $db = new SQLite3(DB_FILE, SQLITE3_OPEN_READONLY);

  $res = $db->query($query);
  if (!$res)
  {
    return false;
  }

  $rows = array();
  while ($row = $res->fetchArray(SQLITE3_ASSOC) !== false)
  {
    array_push($rows, $row);
  }

  return $rows;
}

?>
