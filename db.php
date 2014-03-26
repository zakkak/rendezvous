<?php

include ("txtDB/txt-db-api.php");

function create_db()
{
	echo '<br>Please wait...<br> Creating Database:<br>';
	if (!file_exists(DB_DIR)) 		// no database directory found. Create it.
		{
		$rc=mkdir (realpath('.').'/'.DB_DIR , 0711);
		if(!$rc)
			{
				print_error_msg("Cannot create Database " . DB_DIR);
				return false;
			}
		}

	$db = new Database(ROOT_DATABASE);
	$db->executeQuery("CREATE DATABASE mydb;");
	$db = new Database("mydb");
	echo 'Creating Tables...<br>';
	$db->executeQuery("CREATE TABLE ren_sessions (ren_ses_id inc, title str, deadline int, active str);");
	$db->executeQuery("CREATE TABLE ren_periods (ren_per_id inc, ren_ses_id int, ren_start int, ren_end int, ren_length int, ren_slots int);");
	$db->executeQuery("CREATE TABLE rendezvous (ren_ses_id int, ren_per_id int, login str, ren_time int, ren_slot int);");
	echo '&nbsp;&nbsp;&nbsp;&nbsp;  ren_sessions, ren_periods, rendezvous<br>';
	echo '<br> DONE!';

	return true;
}

function check_db()
{
	//echo substr(sprintf('%o', fileperms(API_HOME_DIR)), -4);
	if (!file_exists(DB_DIR . "mydb")) 		// no database file found. Create the database.
		{		//Database exists
			if( substr(sprintf('%o', fileperms(API_HOME_DIR)), -4) != '1777')		// check permissions of txtDB directory
				{
					echo '<br> Please set the permissions of the database directory ';
					echo '( "'.API_HOME_DIR.'" ) to 1777!<br>';
					echo 'This is done by executing the following command: ';
					echo '<pre>chmod 1777 '.API_HOME_DIR.'</pre><br>';
					echo 'This is needed only for the first setup of the system,';
					echo ' later you will be asked to change them to 0711.';
					echo '</body>';
					echo '</html>';
					return false;
				}
			create_db();
			$delay = "3"; // 3 second delay
			$url = "index.php"; // target of the redirect
			echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'">';
			echo '</body>';
			echo '</html>';
			return false;
		}
	if( substr(sprintf('%o', fileperms(API_HOME_DIR)), -4) != '0711')		// check permissions of txtDB directory
		{
			echo '<br> Please set permissions of database directory ';
			echo '( "'.API_HOME_DIR.'" ) to 0711!<br>';
			echo 'This is done by executing the following command: ';
			echo '<pre>chmod 0711 '.API_HOME_DIR.'</pre>';
			echo '</body>';
			echo '</html>';
			return false;
		}
	return true;
}

function reset_db()
{

	if (file_exists(DB_DIR . "mydb")) {		// Check if Database exists
		echo 'Deleting Database...<br>';
		$db = new Database(ROOT_DATABASE);
		$db->executeQuery("DROP DATABASE mydb");
	}
	if (file_exists(DB_DIR . "lock.txt"))
		unlink(DB_DIR . "lock.txt");
	echo 'Deleting Log file...<br>'; unlink(DB_DIR . "log.txt");

	foreach (glob(DB_DIR.'*') as $filename)
		{
			echo 'Deleting Session Data...<br>';
			unlink($filename);
		}

	if (file_exists(DB_DIR)) {		// Check if Database exists
		echo 'Deleting Database directory...<br>';
		unlink(DB_DIR);
	}
	//create_db();
	return true;
}

?>
