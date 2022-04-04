<?php

require_once ("../lib/config.inc.php");

$lnuserid_PK = $_GET['gnuserid_PK'];
$lncontactid_PK = $_GET['gncontactid_PK'];

//echo $lnuserid_PK . "   ";
//echo $lncontactid_PK . "    ";

$displayDate = date("l F d, Y");

//displaying contacts info for the specific user
dbconnect();
$db = new _ALTO_DB;
$tpl=new Template(".","keep");
$tpl->set_file(array("tplHandler"=>"html/managecontactmail.html"));

//set page properties
//$tpl->set_var("page_title",'Today is '.date('jS of F'));
//$tpl->set_var("display_date",$displayDate);
//  <input name="radiobutton" type="radio" value="radiobutton">

//searching fullname by lncontactid_PK

$select = " select * from contacts where contactid_PK = $lncontactid_PK";
$data = $db->opendb($select);

$tpl->set_block("tplHandler","BLOCK1","ROWjon1");
 
         foreach ($data as $info){
						$tpl->set_var("name",$info['lastname'] . ", " . $info['firstname'] . " " . $info['middlename'] );                        											
						$tpl->parse("ROWjon1","BLOCK1",true);   						
		 }


$tpl->set_block("tplHandler","BLOCK","ROWjon");
$data = $db->opendb("SELECT * FROM contactinfo WHERE contactid_FK = $lncontactid_PK");
			//AND contacts.userid_FK = $lnuserid_PK ");

            foreach ($data as $info){
						//setting up hidden field
						$tpl->set_var("c",'<input type="radio" name="defualtmail" value="">');
													
						//$tpl->set_var("contactid_PK",$lncontactid_PK);                        

						$tpl->set_var("mail",$info['mail']);                        
						
						$tpl->parse("ROWjon","BLOCK",true);   						     

			}
$tpl->parse("tplHandler", array("tplHandler"));
$tpl->finish("tplHandler");
$tpl->p("tplHandler");


$db->closedb();
?>