<?php

/*  Copyright (c) 2007-12, Michael K. Papamichael <papamixATgmail.com>
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

	include ("txtDB/txt-db-api.php");

	function create_db()
	{		
		echo '<br>Please wait...<br> Creating Database:<br>';
		$db = new Database(ROOT_DATABASE); 
		$db->executeQuery("CREATE DATABASE mydb;");
		$db = new Database("mydb");
		echo 'Creating Tables...<br>';
		$db->executeQuery("CREATE TABLE submit_sessions (sub_ses_id inc, title str, sub_dir str, filename str DEFAULT '', filesize int DEFAULT 0, deadline int, active str);"); 		
		$db->executeQuery("CREATE TABLE submits (sub_ses_id int, login str, sub_time int);"); 							
		$db->executeQuery("CREATE TABLE ren_sessions (ren_ses_id inc, title str, deadline int, active str);"); 							
		$db->executeQuery("CREATE TABLE ren_periods (ren_per_id inc, ren_ses_id int, ren_start int, ren_end int, ren_length int, ren_slots int);"); 							
		$db->executeQuery("CREATE TABLE rendezvous (ren_ses_id int, ren_per_id int, login str, ren_time int, ren_slot int);"); 							
		echo '&nbsp;&nbsp;&nbsp;&nbsp;  submit_sessions, submits, ren_sessions, ren_periods, rendezvous<br>';
		echo '<br> DONE!';
		
		return true;
	}
  
	function check_db()
  {	
		//echo substr(sprintf('%o', fileperms(DB_DIR)), -4);
		//if( substr(sprintf('%o', fileperms(DB_DIR)), -4) != '1777')		// check permissions of directory
		//{
		//	echo '<br> Please set permissions of database directory ( "'.realpath('.').'/'.DB_DIR.'" ) to 1777!<br>
		//						 This is done by executing the following command: <b>chmod 1777 '.realpath('.').'/'.DB_DIR.'</b>';
		//	echo '</body>';
		//	echo '</html>';
		//	return false;
		//}
		if (!file_exists(DB_DIR . "mydb")) 		// no database file found. Create the database.
		{		//Database exists
			create_db();
			$delay = "3"; // 3 second delay
			$url = "index.php"; // target of the redirect
			echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'">';
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
		echo 'Deleting Counter files...<br>'; unlink(DB_DIR . "visitors.txt"); unlink(DB_DIR . "hits.txt");
		
		foreach (glob(DB_DIR.'*') as $filename)
		{
			echo 'Deleting Session Data...<br>';
			unlink($filename);
		}
		//create_db();
		return true;
	}
	
?>
