<?php
// Saving Edited Contact Info
require_once ("../lib/config.inc.php");
// Editing Email address
$userid = 1;

$lastname =  $_POST[txtlastname]; 
$firstname =  $_POST[txtfirstname]; 
$middlename =  $_POST[txtmiddlename]; 
$company =  $_POST[txtcompany]; 
$telno = $_POST[txttelno]; 
$error_validation = "You must complete all details";
//for contactid
$lncontactid_PK = $_POST['hiddencontactid_PK'];

//displaying contacts info for the specific user
dbconnect();
$db = new _ALTO_DB;
$tpl=new Template(".","keep");
		//data validation	
	
		//add contacts inf
		require_once ("../lib/config.inc.php");
		$db = new _ALTO_DB;
		$select = "	UPDATE 
						contacts				 
         			SET 
						lastname 		= '$lastname',
						firstname 		= '$firstname',
						middlename 		= '$middlename',
						company 		= '$company',
						telno 			= '$telno',
						contactid_PK 	= '$lncontactid_PK'
           			WHERE 
		   				contactid_PK  = '$lncontactid_PK'
					AND 
						userid_FK = '$userid'";

$db->savedb($select); 
header("Location: contactlist.php?id=idPK");
?>
