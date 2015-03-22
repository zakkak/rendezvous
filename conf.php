<?php

/*****************************************************************************/
/*                                                                           */
/*                               Basic Settings                              */
/*                                                                           */
/*****************************************************************************/

  /* The LDAP server URI */
  $ldap_server = "ldap://ds.uoc.gr";
  /* The LDAP server port to connect to */
  $ldap_port   = "389";
  /* The LDAP base DN */
  $ldap_bdn    = "dc=uoc,dc=gr";

  /* The admins_file specified below contains the logins of the instructors
   * and the teaching assistants. (syntax: one login per line)
   * You can either create a new file (e.g. admins.txt) containing the desired
   * logins or you can have this file point to the course's .rhosts file.
   * (e.g. $admins_file = "home/lessons/hy120/.rhosts";)
   */
  //$admins_file = "../../.rhosts";
  $admins_file = "./admins";

  /* This string will appear on the top of the webpage right next to the main
   * title "Rendezvous".
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

  /* Set this to true to automatically send e-mail confirmations for
   * file rendezvous bookings */
  $email_confirmation = true;


/*****************************************************************************/
/*                                                                           */
/*                            Advanced Settings                              */
/*                                                                           */
/*****************************************************************************/
	// Error reporting
	error_reporting(0); // Turn off all error reporting
	//error_reporting(E_ALL); // Report all errors
	//error_reporting(E_ERROR | E_WARNING | E_PARSE); // Report simple running errors
	//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); // Also report notices

?>
