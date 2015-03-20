<?php
// Variables used in this script:
//   $summary     - text title of the event
//   $datestart   - the starting date (in seconds since unix epoch)
//   $dateend     - the ending date (in seconds since unix epoch)
//   $address     - the event's address
//   $uri         - the URL of the event (add http://)
//   $description - text description of the event
//   $filename    - the name of this file for saving (e.g. my-event-name.ics)
//

$summary = $_GET['summary'];
$datestart = $_GET['datestart'];
$dateend = $_GET['dateend'];
$address = $_GET['address'];
$uri = $_GET['uri'];
$description = $_GET['description'];
$filename = $_GET['filename'];

// 1. Set the correct headers for this file
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// 2. Perform required actions
function dateToCal($timestamp) {
  return date('Ymd\THis\Z', $timestamp);
}

// Escapes a string of characters
function escapeString($string) {
  return preg_replace('/([\,;])/','\\\$1', $string);
}

// 3. Echo out the ics file's contents
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VTIMEZONE
TZID:Asia/Beirut
TZURL:http://tzurl.org/zoneinfo-outlook/Asia/Beirut
X-LIC-LOCATION:Asia/Beirut
BEGIN:DAYLIGHT
TZOFFSETFROM:+0200
TZOFFSETTO:+0300
TZNAME:EEST
DTSTART:19700329T000000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0300
TZOFFSETTO:+0200
TZNAME:EET
DTSTART:19701025T000000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
DTEND:<?= dateToCal($dateend) ?>

UID:<?= uniqid() ?>

DTSTAMP:<?= dateToCal(time()) ?>

LOCATION:<?= escapeString($address) ?>

DESCRIPTION:<?= escapeString($description) ?>

URL;VALUE=URI:<?= escapeString($uri) ?>

SUMMARY:<?= escapeString($summary) ?>

DTSTART:<?= dateToCal($datestart) ?>

END:VEVENT
END:VCALENDAR
