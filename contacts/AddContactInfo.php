<?php 
//adds email addresses to contacts
$userid = 1;
//add contacts inf
require_once ("../lib/config.inc.php");
$db = new _ALTO_DB;
$select = " INSERT INTO contactinfo ( UserID_FK,
               mail
               ) VALUES ('$userid',
               '$_POST[txtemail]'
               ) " ;
savedb($select);
$db->closedb();
header("Location: contactlist.php");
?>