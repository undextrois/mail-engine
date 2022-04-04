<?php

require_once ("../lib/config.inc.php");

$userid = 1;
//$contactid_PK;
$displayDate = date("l F d, Y");

//displaying contacts info for the specific user
dbconnect();
$db = new _ALTO_DB;
$tpl=new Template(".","keep");
$tpl->set_file(array("tplHandler"=>"html/contactlist.htm"));

//set page properties
$tpl->set_var("page_title",'Today is '.date('jS of F'));
$tpl->set_var("display_date",$displayDate);

$tpl->set_block("tplHandler","BLOCK","ROWjon");
$data = $db->opendb("SELECT * FROM contacts LEFT JOIN contactinfo ON contacts.contactid_PK = contactinfo.contactid_FK 
			WHERE contacts.userid_FK = $userid AND contactinfo.default = 1");

            foreach ($data as $info){
						//setting up hidden field
	     			    $tpl->set_var("gncontactid_PK",$info['contactid_PK']);
						
						//check box for deletion 
						$tpl->set_var("c",'<input type="checkbox" name="" value="">');
  
				    	$tpl->set_var("name",$info['lastname'] . ", " . $info['firstname'] 
								. " " . $info['middlename'] );
       
						//for client mail
						if ($info['telno'] == "")
							$tpl->set_var("telno",'---');                        							
						else 
							$tpl->set_var("telno",$info['telno']);      
							
						//for tel no
						if ($info['mail'] == "")
							$tpl->set_var("mail",'---');                        							
						else 
							$tpl->set_var("mail",$info['mail']);      
	
		 			   $icons ='<a href="#" onclick="location.href=(\'viewcontactdetail.php?gncontactid_PK='.$info['contactid_PK'].'\')"><img border="0" src="../images/edit1.gif" alt="Edit Contact" width="16" height="16"></a>
								<a href="#" onclick="location.href=(\'managecontactmail.php?gncontactid_PK='.$info['contactid_PK'].'&gnuserid_PK='.$userid.'\')"><img border="0" src="../images/apbdoc.gif" alt="Manage Contact Mail" width="16" height="16"></a>
								<a href="#" onclick="location.href=(\'DeleteContact.php?gncontactid_PK='.$info['contactid_PK'].'\')"><img border="0" src="../images/delete.gif" alt="Delete Contact" width="16" height="16"></a>
 								<a href="#" onclick="Location.href=(\'mailthiscontact.php?gncontactid_PK = '.$info['contactid_PK'].'\')"><img border="0" src="../images/mail.gif" alt="EMail This Contact" width="16" height="16"></a>';

        
						$tpl->set_var("icons",$icons);                        

						
						$tpl->parse("ROWjon","BLOCK",true);   						     
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