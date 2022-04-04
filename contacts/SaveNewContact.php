<?php
$userid = 1;

$lastname =  $_POST[txtlastname]; 
$firstname =  $_POST[txtfirstname]; 
$middlename =  $_POST[txtmiddlename]; 
$company =  $_POST[txtcompany]; 
$telno = $_POST[txttelno]; 

$error_validation = "You must complete all details";

	//data validation	
	if (($lastname == "") || ($firstname == "") || ($middlename == "") || ($company == "") || ($lastname == ""))
		header("Location: contacts.php");
	
	else{
		//add contacts inf
		require_once ("../lib/config.inc.php");
		$db = new _ALTO_DB;
		$select = " INSERT INTO contacts ( userid_FK,
					   lastname,
					   firstname,
					   middlename,
					   company,
					   telno
					   ) VALUES ('$userid',
					   '$_POST[txtlastname]',
					   '$_POST[txtfirstname]',
					   '$_POST[txtmiddlename]',
					   '$_POST[txtcompany]',
					   '$_POST[txttelno]'
					   ) " ;
		savedb($select);
		$db->closedb();
		header("Location: contactlist.php");
	}
?>
