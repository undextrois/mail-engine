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

include 'mime-types.php';
$db = new _ALTO_DB;
$db->_use("_mailuser");
$userid = $_ML_Session->ssn_idPK;

$FilesizeSuffixes = array('b', 'k', 'm', 'g', 't');

if($_POST['Action'] == 'Send Email'){
  include_once 'send-email.php';

  $message_id = $_POST['message_id'];
  $from_account = $_POST['from_account'];
  $to = stripslashes_gpc(str_replace(',', ';', $_POST['to']));
  $cc = stripslashes_gpc(str_replace(',', ';', $_POST['cc']));
  $bcc = stripslashes_gpc(str_replace(',', ';', $_POST['bcc']));
  $subject = stripslashes_gpc($_POST['subject']);
  $message = stripslashes_gpc($_POST['message']);
  $content_type = $_POST['content_type'];

  $query = "SELECT POP_EmailAddress FROM pop_account WHERE popID_PK='{$from_account}'";
  $popacct = $db->opendb($query);

  $from = $popacct[0]['POP_EmailAddress'];

  $query = "DELETE FROM sent_items WHERE message_id_PK='{$message_id}'";
  $db->savedb($query);

  $query = "
    INSERT INTO
      sent_items
    (
      popID_FK,
      UserID_PK,
      message_id_PK,
      `from`,
      `to`,
      cc,
      bcc,
      subject,
      body,
      content_type,
      sent,
      date_sent
    )
    VALUES
    (
      '{$from_account}',
      '{$userid}',
      '{$message_id}',
      '".addslashes($from)."',
      '".addslashes($to)."',
      '".addslashes($cc)."',
      '".addslashes($bcc)."',
      '".addslashes($subject)."',
      '".addslashes($message)."',
      '{$content_type}',
      'N',
      NOW()
    )";

  $db->savedb($query);

  if(send_email($message_id, $to, $subject, $message, $content_type, $from, $cc, $bcc)){
    $sent = 'Y';
    $query = "UPDATE sent_items SET sent='Y' WHERE message_id_PK='{$message_id}'";
    $db->savedb($query);
  }
  else{
    $sent = 'N';
    $AddMsg = "&add_msg={$Mail->ErrorInfo}";
  }

  Header('Location: '.basename($_SERVER['PHP_SELF'])."?sent={$sent}{$AddMsg}");
  exit;
}

if($_POST['Action'] == 'Delete'){
  $attachment_id = $_POST['attachment_id'];
  $query = "DELETE FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
  $db->savedb($query);
  @unlink("attachments/{$attachment_id}");
}


$tpl=new Template(".","keep"); //required for all template pages

if($_GET['sent']){
  $PageMessage = $_GET['sent'] == 'Y' ? 'Email was sent successfully' : "The email was not sent<br>{$_GET['add_msg']}";
  $tpl->set_file(array("compose"=>"email-sent.html"));   //specify which html file to point to
  $tpl->set_var('PageMessage',$PageMessage);
}
else{
  $tpl->set_file(array("compose"=>"email-form.html"));   //specify which html file to point to

  $DelTime = mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'));
  $query = "
    SELECT
      attachments.attachment_id_PK
    FROM
      attachments
    LEFT JOIN
      sent_items
      ON
        sent_items.message_id_PK=attachments.message_id_FK
    WHERE
      sent_items.message_id_PK IS NULL
      AND UNIX_TIMESTAMP(attachments.date) <= '{$DelTime}'";

  $del_atts = $db->opendb($query);

  $del_atts_ids = '';
  foreach($del_atts AS $del_att){
    @unlink("attachments/{$del_att['attachment_id_PK']}");
    $del_atts_ids .= ($del_atts_ids ? ',' : '') . "'{$del_att['attachment_id_PK']}'";
  }

  if($del_atts_ids){
    $query = "DELETE FROM attachments WHERE attachment_id_PK IN ({$del_atts_ids})";    
    $db->savedb($query);
  }

  $query = "SELECT popID_PK, POP_EmailAddress, default_pop FROM pop_account WHERE UserID_FK='{$userid}'";
  $pop_accts = $db->opendb($query);
  $tpl->set_block('compose', 'POPACCOUNTS', 'pophandle');

  if($_POST['message_id'])
    $message_id = $_POST['message_id'];
  else
    do{
      $message_id = md5(uniqid(rand(),1));
      $query = "SELECT message_id_PK FROM sent_items WHERE message_id_PK='{$message_id}'";
      $mXs = $db->opendb($query);
    }while(count($MXs));

  foreach($pop_accts AS $pop_acct){
    $tpl->set_var(
      array(
        'pop_id'          =>  $pop_acct['popID_PK'],
        'pop_display'     =>  $pop_acct['POP_EmailAddress'],
        'selected'        =>  (($pop_acct['default_pop'] && !$_POST['from_account']) || $_POST['from_account'] == $pop_acct['popID_PK'] ? ' selected' : '')
      )
    );

    $tpl->parse('pophandle', 'POPACCOUNTS', true);
  }

  $query = "SELECT attachment_id_PK, filename, content_type, filesize FROM attachments WHERE message_id_FK='{$message_id}'";
  $atts = $db->opendb($query);
  $tpl->set_block('compose','ATTACHMENTS', 'atthandle');

  foreach($atts AS $att){
    $fsize = $att['filesize'];
    $x = 0;
    while($fsize > 1024){
      $fsize /= 1024;
      $x++;
    }

    $fsize = round($fsize, 1);
    $fsize = $fsize % 1 ? round($fsize) : $fsize;
    $icon_file = $MimeIcons[$att['content_type']];
    $icon_file = file_exists("images/{$icon_file}") ? $icon_file : 'fileicon.gif';

    $tpl->set_var(
      array(
        'attachment_id'         =>  $att['attachment_id_PK'],
        'attachment_icon'       =>  $icon_file,
        'attachment_filename'   =>  $att['filename'],
        'attachment_filename2'  =>  str_replace("'", "\\'", $att['filename']),
        'attachment_size'       =>  $fsize.' '.$FilesizeSuffixes[$x]
      )
    );

    $tpl->parse('atthandle','ATTACHMENTS',true);
  }

  if(!count($atts))
    $tpl->set_var('atthandle', '<tr><td class="defafont_b" colspan="4">No attachments</td></tr>');

  $tpl->set_var(
    array(
      'FormName'            =>  'EmailForm',
      'FormAction'          =>  basename($_SERVER['PHP_SELF']),
      'FormActionValue'     =>  'Send Email',
      'SubmitValue'         =>  'Send',
      'to_value'            =>  htmlentities($_POST['to'] ? stripslashes_gpc($_POST['to']) : ''),
      'cc_value'            =>  htmlentities($_POST['cc'] ? stripslashes_gpc($_POST['cc']) : ''),
      'bcc_value'           =>  htmlentities($_POST['bcc'] ? stripslashes_gpc($_POST['bcc']) : ''),
      'subject_value'       =>  htmlentities($_POST['subject'] ? stripslashes_gpc($_POST['subject']) : ''),
      'message_value'       =>  htmlentities($_POST['message'] ? stripslashes_gpc($_POST['message']) : ''),
      'checked_plain'       =>  htmlentities($_POST['content_type'] == 'text/plain' || !$_POST['content_type'] ? ' checked' : ''),
      'checked_html'        =>  htmlentities($_POST['content_type'] == 'text/html' ? ' checked' : ''),
      'message_id'          =>  $message_id
    )
  );
}

$main_contents = $tpl->parse("compose", array("compose"));
include 'main.php';
?>
