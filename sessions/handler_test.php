<?php

// handler_test.php is the tests for the handler.php file
//
// Run with:
// php -f handler_test.php
//
// It prints the first test that failed, and exits with error code 1 if
// any test failed. It does not print anything and returns 0 on success.

require_once("handler.php");

// Instance to test against
$sess = new SQLite3Session();

// Database to use
$filename = tempnam(sys_get_temp_dir(), 'rv-test-');

// Symbol-heavy string
define("SYMBOL_HEAVY", "`~!@#$%^&*()_-+=|\\}]{[:;\"'<,>.?/");

////////////
// open() //
////////////

// Non-existent file
if (!$sess->open($filename, "1337"))
{
    print "[FAIL] open() could not create a new database with non-existent file.\n";
    exit(1);
}
unlink($filename);

// Existing file, not SQLite3
file_put_contents($filename, "Not an SQLite3 Database!");
if ($sess->open($filename, "1337"))
{
    print "[FAIL] open() claims to have successfully loaded a non-SQLite3 database.\n";
    exit(1);
}
unlink($filename);

// No permissions to read
file_put_contents($filename, "does not matter");
chmod($filename, 0300);
if ($sess->open($filename, "1337"))
{
    print "[FAIL] open() claims to have successfully loaded a database without having UNIX read permissions.\n";
    exit(1);
}
unlink($filename);

// No permissions to write
file_put_contents($filename, "does not matter");
chmod($filename, 0500);
if ($sess->open($filename, "1337"))
{
    print "[FAIL] open() claims to have successfully written to a database without having UNIX write permissions.\n";
    exit(1);
}
unlink($filename);

// Existing SQLite3
$db = new SQLite3($filename);
$db->exec("CREATE TABLE foo (bar TEXT)");
$db->close();
if (!$sess->open($filename, "1337"))
{
    print "[FAIL] open() could not open and use existing SQLite3 database.\n";
    exit(1);
}
unlink($filename);

/////////////
// write() //
/////////////
$sess->open($filename, "1337");

// Simple write
if (!$sess->write("foo", "bar"))
{
    print "[FAIL] write() failed to write a simple value to an empty database.\n";
    exit(1);
}

// Weird value
if (!$sess->write("weird_value", SYMBOL_HEAVY))
{
    print "[FAIL] write() failed to write a symbol-heavy value to the database.\n";
    exit(1);
}

// Weird key
if (!$sess->write(SYMBOL_HEAVY, "weird_key"))
{
    print "[FAIL] write() failed to write a symbol-heavy key to the database.\n";
    exit(1);
}

////////////
// read() //
////////////

// Ensure read has proper simple value
if ($sess->read("foo") !== "bar")
{
    print "[FAIL] read() failed to read the simple value that was written to the database.\n";
    exit(1);
}

// Ensure read can successfully fetch weird values
if ($sess->read("weird_value") !== SYMBOL_HEAVY)
{
    print "[FAIL] read() failed to read the weird value that was written to the database.\n";
    exit(1);
}

// Ensure read can successfully fetch weird keys
if ($sess->read(SYMBOL_HEAVY) !== "weird_key")
{
    print "[FAIL] read() failed to read a value with a weird key that was written to the database.\n";
    exit(1);
}

/////////////
// close() //
/////////////

// Ensure it returns true
if (!$sess->close())
{
    print "[FAIL] close() returned false.\n";
    exit(1);
}

///////////////
// destroy() //
///////////////

// Ensure the are no errors
if (!$sess->destroy("foo"))
{
    print "[FAIL] destroy() failed to delete existing value from database.\n";
    exit(1);
}

// Ensure items get deleted
if ($sess->read("foo") === "bar")
{
    print "[FAIL] destroy() ran successfully but the item is still in the database.\n";
    exit(1);
}

//////////
// gc() //
//////////

// Ensure no error is returned
if ($sess->gc(3600) === false) // can't use !$sess->gc(3600) as "0" passes that check
{
    print "[FAIL] gc() failed to run and returned false.\n";
    exit(1);
}

// Ensure no records are deleted (we assume that runnings these tests takes less than 1h)
if ($sess->gc(3600) !== 0)
{
    print "[FAIL] gc() deleted records that it shouldn't.\n";
    exit(1);
}

// Ensure that all records are deleted
sleep(1);
if ($sess->gc(0) !== 2)
{
    print "[FAIL] gc() failed to delete all records.\n";
    exit(1);
}

//////////////////
// End of tests //
//////////////////
unlink($filename);

?>