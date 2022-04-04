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
//$contactid_PK = $_POST[]; 
//error validation
$error_validation = "You must complete all details";

//for contactid
$lncontactid_PK = $_GET['gncontactid_PK'];
echo $lncontactid_PK;

//displaying contacts info for the specific user
/*dbconnect();
$db = new _ALTO_DB;
$tpl=new Template(".","keep");
$tpl->set_file(array("tplHandler"=>"EditContact.html"));

	//data validation	
	if (($lastname == "") || ($firstname == "") || ($middlename == "") || ($company == "") || ($lastname == ""))
		header("Location: contacts.php");
	else{
		//add contacts inf
		require_once ("../lib/config.inc.php");
		$db = new _ALTO_DB;
		echo $lastname;
		echo $firstname;
		echo $middlaname;
		echo $company;
		echo $telno;
		echo $lncontactid_PK;

		$select = "UPDATE 
						contacts				 
         			SET 
						lastname 		= '$lastname',
						firstname 		= '$firstname',
						middlename 		= '$middlename',
						company 		= '$company',
						telno 			= '$telno',
						contactid_PK 	= '$lncontactid_PK'
           	WHERE 
		   				custsupidPK = '$custsupid_PK' 
			AND 
						useridFK = '$useridPK'";

$db->savedb($select); //header("Location: contactlist.php");
	}
*/
?>
