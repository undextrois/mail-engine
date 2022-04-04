<?php
// Saving Edited Contact Info
$userid = 1;
$lncontactid_PK = $_GET['gncontactid_PK']; 

		require_once ("../lib/config.inc.php");

		$db = new _ALTO_DB;
		$select = "DELETE FROM contacts WHERE contactid_PK = '$lncontactid_PK'" ;
		
		$db->savedb($select);
		$db->closedb();
		header("Location: contactlist.php");
?>
