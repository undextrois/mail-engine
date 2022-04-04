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


$db = new _ALTO_DB;
$db->_use("_mailuser");

$userid = $_ML_Session->ssn_idPK;

$FilesizeSuffixes = array('b', 'k', 'm', 'g', 't');

if($_GET['Action'] == 'Delete'){
  $emails = is_array($_GET['email']) ? "'".implode("','", $_GET['email'])."'" : "'{$_GET['email']}'";
  $pop_account = $_GET['pop_account'];

  if($emails && $emails != "''"){
    $query = "DELETE FROM sent_items WHERE message_id_PK IN ({$emails})";
    $db->savedb($query);

    $query = "SELECT attachment_id_PK FROM attachments WHERE message_id_FK IN ({$emails})";
    $atts = $db->opendb($query);

    foreach($atts AS $att){
      @unlink("attachments/{$att['attachment_id_PK']}");
    }

    $query = "DELETE FROM attachments WHERE message_id_FK IN ({$emails})";
    $db->savedb($query);
  }

  Header("Location: sent-items.php?pop_account={$pop_account}");
  exit;
}


$tpl=new Template(".","keep"); //required for all template pages

$tpl->set_file(array('sent_items'=>'sent-items.html'));

$query = "SELECT popID_PK, POP_EmailAddress, default_pop FROM pop_account WHERE UserID_FK='{$userid}'";
$pop_accts = $db->opendb($query);
$tpl->set_block('sent_items', 'POPACCOUNTS', 'pophandle');

foreach($pop_accts AS $pop_acct){
  $tpl->set_var(
    array(
      'pop_id'          =>  $pop_acct['popID_PK'],
      'pop_display'     =>  $pop_acct['POP_EmailAddress'],
      'selected'        =>  (($pop_acct['default_pop'] && !$_GET['pop_account']) || $_GET['pop_account'] == $pop_acct['popID_PK'] ? ' selected' : '')
    )
  );

  $pop_account = ($pop_acct['default_pop'] && !$_GET['pop_account']) || $_GET['pop_account'] == $pop_acct['popID_PK'] ? $pop_acct['popID_PK'] : $pop_account;

  $tpl->parse('pophandle', 'POPACCOUNTS', true);
}

$query = "
  SELECT
    message_id_PK,
    `from`,
    `to`,
    subject,
    UNIX_TIMESTAMP(date_sent) AS date,
    sent
  FROM
    sent_items
  WHERE
    popID_FK='{$pop_account}'
    AND UserID_PK='{$userid}'";

$Msgs = $db->opendb($query);

$tpl->set_block('sent_items', 'EMAILSBLOCK', 'emails_handle');

foreach($Msgs AS $Msg){
  $query = "SELECT attachment_id_PK FROM attachments WHERE message_id_FK='{$Msg['message_id_PK']}'";
  $MAtts = $db->opendb($query);

  $tpl->set_var(
    array(
      'email_message_id'    =>  $Msg['message_id_PK'],
      'email_date_sent'     =>  date('l j F Y, g:i a', $Msg['date']),
      'email_subject'       =>  $Msg['subject'],
      'email_to'            =>  $Msg['to'],
      'email_sent'          =>  $Msg['sent'],
      'email_attachment'    =>  (count($MAtts) ? "<img border=\"0\" src=\"images/attachment.gif\" width=\"10\" height=\"13\">" : '&nbsp;')
    )
  );

  $tpl->parse('emails_handle', 'EMAILSBLOCK', true);
}

if(!count($Msgs)){
  $tpl->set_var('emails_handle', '<tr><td class="defafont_b" align="center" colspan="9"><b><i>There are no emails</i></b></td></tr>');
  $email_options = '';
}
else{
  $email_options = "
                    <tr>
                      <td class=\"defafont_b\" align=\"center\" colspan=\"9\">
                        <p align=\"left\">&nbsp;<img border=\"0\" src=\"images/parent.gif\" width=\"20\" height=\"16\">
                        Group Options: <input type=\"submit\" value=\"Delete\" class=\"ukbots\">
                    </tr>";
}

$tpl->set_var(
  array(
    'total_sent_emails'     =>  count($Msgs) . ' message(s) sent',
    'pop_account'           =>  $pop_account,
    'email_form_action'     =>  basename(__FILE__),
    'email_options'         =>  $email_options
  )
);

$main_contents = $tpl->parse('sent_items', array('sent_items'));

include 'main.php';
?>
