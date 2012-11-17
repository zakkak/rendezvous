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

/*****************************************************************************/
/*                                                                           */
/*                               Basic Settings                              */
/*                                                                           */
/*****************************************************************************/

  /* The mailserver specified below is used to authenticate all users (students 
   * and administrators). Needs to support imap.
   */
  $mailserver = "mailhost.csd.uoc.gr";
  
  /* The admins_file specified below contains the logins of the instructors 
   * and the teaching assistants. (syntax: one login per line)
   * You can either create a new file (e.g. admins.txt) containing the desired 
   * logins or you can have this file point to the course's .rhosts file.
   * (e.g. $admins_file = "home/lessons/hy120/.rhosts";)	
   */
  //$admins_file = "../../.rhosts";
  $admins_file = "./admins";

  /* Enable or disable the submit tab */
  $submit_enabled = true;

  /* This string will appear on the top of the webpage right next to the main 
   * title "Submit-Rendezvous".
  */
  $title = "Place Holder (edit conf.php to change)";
  //$title = "HY-225 Computer Organization; 
  //$title = "HY-225 Οργάνωση Υπολογιστών";

  /* These strings appear at the top right of the page next to the logo */
  $affil1      = "Computer Science Department";
  $affil1_link = "http://www.csd.uoc.gr"; // enter URL or leave blank for no link
  $affil2      = "University of Crete";
  $affil2_link = "http://www.uoc.gr";     // enter URL or leave blank for no link
  $affil3      = "Edit conf.php to change affiliation and logo";
  $affil3_link = "";                      // enter URL or leave blank for no link
  $logo_path   = "theme/csd_logo.jpg";    // specify path to logo
  $logo_link   = "http://www.uoc.gr";     // enter URL or leave blank for no link

  /* Change favicon */
  $favicon_path = "theme/logo.ico";         // specify path to favicon
  
  /* Set this to true to automatically send e-mail confirmations for file rendezvous bookings */
  $email_confirmation = true;


/*****************************************************************************/
/*                                                                           */
/*                            Advanced Settings                              */
/*                                                                           */
/*****************************************************************************/
	// Error reporting
	//error_reporting(0); // Turn off all error reporting
	//error_reporting(E_ALL); // Report all errors
	error_reporting(E_ERROR | E_WARNING | E_PARSE); // Report simple running errors
	//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); // Also report notices
		
?>
