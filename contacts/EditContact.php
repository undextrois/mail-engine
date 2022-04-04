<?php

require_once ("../lib/config.inc.php");

$userid = 1;
$lncontactid_PK = $_GET['gncontactid_PK'];

//$tpl->set_var("gncontactid_PK",$info['contactid_PK']);
						

$displayDate = date("l F d, Y");


//displaying contacts info for the specific user
dbconnect();
$db = new _ALTO_DB;
$tpl=new Template(".","keep");
$tpl->set_file(array("tplHandler"=>"html/EditContact.html"));


//set page properties
$tpl->set_var("page_title",'Today is '.date('jS of F')); 
	

$tpl->set_var("display_date",$displayDate);

$tpl->set_block("tplHandler","BLOCK","ROWjon");

//$lncontactid_PK = $_get['gncontactid_PK'];

$data = $db->opendb("SELECT * FROM contacts WHERE contactid_PK = $lncontactid_PK");

    foreach ($data as $info){
		//put filtered data here
		$tpl->set_var("contactid_PK",$info['contactid_PK']);
		$tpl->set_var("txtlastname",$info['lastname']);
		$tpl->set_var("txtfirstname",$info['firstname']);
		$tpl->set_var("txtmiddlename",$info['middlename']);
		$tpl->set_var("txtcompany",$info['company']);
		$tpl->set_var("txttelno",$info['telno']);
	}

$tpl->parse("tplHandler", array("tplHandler"));
$tpl->finish("tplHandler");
$tpl->p("tplHandler");
/*
	$tpl->parse('main', array('main'));
	$tpl->p("main");
*/
//close database
$db->closedb();
?>