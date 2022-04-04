<?
require_once ("../globals/config.inc.php");

include_once '_HW0NP12/general.inc.php';

$_redirect_url = "_ml_login.php";

$_ML_Session = new _ML_Session_Class();
_no_cache();
if ($_ML_Session->ML_session_auth()==false)
{
   $_ML_Session->ML_session_destroy();
   _safe_redirect($_redirect_url);
}

/* $userid_PK = $_ML_Session->ssn_idPK; */

$db = new _ALTO_DB;
$db->_use("_mailuser");
$userid = $_ML_Session->ssn_idPK;
$ThisScript = basename($_SERVER['PHP_SELF']);

$tpl = new template('.', 'keep');
$tpl->set_file(array('contacts_hndl'=>'email-contacts.html'));

$emails_js = '';
$contacts_js = '';
$all_emails_js = '';
$contact_emails_js = '';
$emails = '';

$emails_all = explode(';', strtr($_GET['emails'], array(','=>';',' '=>'')));
while(list($em_key, $em_val) = each($emails_all)){
  if(trim($em_val)){
    $emails .= ($emails ? "\n              " : '') . "<option value=\"{$em_val}\">{$em_val}</option>";
    $emails_js .= ($emails_js ? "\n    " : '') . "ActiveEmails[{$em_key}] = \"{$em_val}\";";
  }
}

$query = "
  SELECT
    contacts.contactid_PK,
    CONCAT_WS(', ', contacts.lastname, contacts.firstname) AS name,
    contactinfo.mail
  FROM
    contacts,
    contactinfo
  WHERE
    contacts.userid_FK='{$userid}'
    AND contactinfo.contactid_FK=contacts.contactid_PK
  ORDER BY
    contacts.lastname,
    contacts.firstname,
    contacts.middlename,
    contacts.contactid_PK,
    contactinfo.mail";

$cnts = $db->opendb($query);
$x = 0;
$c_id = 0;
$y = 0;
foreach($cnts AS $cnt){
  $all_emails_js .= ($all_emails_js ? "\n    " : '') . "Emails[{$x}] = \"{$cnt['mail']}\";";

  if($c_id != $cnt['contactid_PK']){
    $contacts_js .= ($contacts_js ? "\n    " : '') . "Contacts[{$y}] = new Array({$cnt['contactid_PK']}, \"".htmlentities($cnt['name'])."\");";

    if($c_id){
      $contact_emails_js .= ($contact_emails_js ? "\n    " : '') . "ContactEmails[{$c_id}] = new Array({$c_emails});";
    }

    $c_id = $cnt['contactid_PK'];
    $c_emails = '';
    $y++;
  }

  $c_emails .= ($c_emails ? "," : '') . "'{$x}'";

  $x++;
}

if(count($cnts))
  $contact_emails_js .= ($contact_emails_js ? "\n    " : '') . "ContactEmails[{$c_id}] = new Array({$c_emails});";

$tpl->set_var(
  array(
    'emails_js'           =>  $emails_js,
    'contacts_js'         =>  $contacts_js,
    'all_emails_js'       =>  $all_emails_js,
    'contact_emails_js'   =>  $contact_emails_js,
    'contacts'            =>  $contacts,
    'contact_emails'      =>  $contact_emails,
    'emails'              =>  $emails,
    'email_field'         =>  $_GET['field']
  )
);

$main_contents = $tpl->parse('contacts_hndl', array('contacts_hndl'));

$tpl->set_file(array('popup_main'=>'popup.html'));
$tpl->set_var(
  array(
    'main_contents'   =>  $main_contents,
    'page_title'      =>  'Contacts'
  )
);

$tpl->parse('popup_main',array('popup_main'));
$tpl->p('popup_main');
?>
