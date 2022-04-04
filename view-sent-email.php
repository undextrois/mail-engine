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
$ThisScript = basename($_SERVER['PHP_SELF']);
$FilesizeSuffixes = array('b', 'k', 'm', 'g', 't');

#
# Download attachment
#

if($_GET['Action'] == 'Download'){
  $attachment_id = $_GET['attachment_id'];
  $message_id = $_GET['message_id'];
  $pop_account = $_GET['account_id'];

  $query = "SELECT filename, content_type, filesize FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
  $att = $db->opendb($query);
  $Error = '';

  if(count($att)){
    $filename = $att[0]['filename'];
    $content_type = $att[0]['content_type'];
    $filesize = $att[0]['filesize'];

    if(file_exists("attachments/{$attachment_id}")){
      if($fp = fopen("attachments/{$attachment_id}", 'r')){
        $fcontents = fread($fp, $filesize);
        fclose($fp);
      }
      else
        $Error .= 'Failed to open file.\n';
    }
    else
      $Error .= 'The file you are trying to download is missing.\n';
  }
  else
    $Error .= 'The file you are trying to download does not exists.\n';

  if($Error){
    echo "
<script language=\"javascript\" type=\"text/javascript\">
  alert(\"{$Error}\");
  location.href = '{$ThisScript}?message_id={$message_id}&pop_account={$pop_account}';
</script>";
  }
  else{
    Header("Content-Type: {$content_type}");
    Header("Content-Length: {$filesize}");
    Header("Content-Disposition: inline; filename={$filename}");
    echo $fcontents;
  }

  exit;
}

## End Download Attachment ##


#
# Resent Email
#
if($_GET['Action'] == 'Resend'){
  $pop_account = $_GET['pop_account'];

  $query = "
    SELECT
      `from`,
      `to`,
      cc,
      bcc,
      subject,
      body,
      content_type,
      sent,
      popID_FK
    FROM
      sent_items
    WHERE
      message_id='{$_GET['message_id']}'";

  $msg = $db->opendb($query);

  if($msg[0]['sent'] == 'Y'){
    do{
      $message_id = md5(uniqid(rand(),1));
      $query = "SELECT message_id_PK FROM sent_items WHERE message_id_PK='{$message_id}'";
      $mXs = $db->opendb($query);
    }while(count($MXs));

    $query = "
      SELECT
        attachment_id_PK,
        filename,
        content_type,
        filesize
      FROM
        attachments
      WHERE
        message_id_FK='{$_GET['message_id']}'";

    $atts = $db->opendb($query);

    foreach($atts AS $att){
      do{
        $attachment_id = md5(uniqid(rand(),1));
        $query = "SELECT attachment_id_PK FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
        $mXs = $db->opendb($query);
      }while(count($MXs));

      @copy("attachments/{$att['attachment_id_PK']}", "attachments/{$attachment_id}");

      $filename = addslashes($att['filename']);
      $content_type = addslashes($att['content_type']);
      $filesize = $att['filesize'];

      $query = "
        INSERT INTO
          attachments
        (
          attachment_id_PK,
          message_id_FK,
          filename,
          content_type,
          filesize,
          date
        )
        VALUES
        (
          '{$attachment_id}',
          '{$message_id}',
          '{$filename}',
          '{$content_type}',
          '{$filesize}',
          NOW()
        )";

      $db->savedb($query);
    }
  }
  else
    $message_id = $_GET['message_id'];

  echo "
<html>
<body onload=\"document.Reply.submit()\">
  <form method=\"post\" name=\"Reply\" action=\"compose.php\">
    <input type=\"hidden\" name=\"from_account\" value=\"{$msg[0]['popID_FK']}\">
    <input type=\"hidden\" name=\"subject\" value=\"Re:".htmlentities($msg[0]['subject'])."\">
    <input type=\"hidden\" name=\"to\" value=\"".htmlentities($msg[0]['to'])."\">
    <input type=\"hidden\" name=\"cc\" value=\"".htmlentities($msg[0]['cc'])."\">
    <input type=\"hidden\" name=\"bcc\" value=\"".htmlentities($msg[0]['bcc'])."\">
    <input type=\"hidden\" name=\"message\" value=\"".htmlentities($msg[0]['body'])."\">
    <input type=\"hidden\" name=\"message_id\" value=\"{$message_id}\">
  </form>
</body>
</html>";

  exit;
}

## End Resend Email ##



$tpl=new Template(".","keep"); //required for all template pages

$query = "
  SELECT
    `from`,
    `to`,
    cc,
    bcc,
    subject,
    body,
    content_type,
    sent,
    UNIX_TIMESTAMP(date_sent) AS date,
    popID_FK
  FROM
    sent_items
  WHERE
    message_id_PK='{$_GET['message_id']}'";

$msg = $db->opendb($query);

$tpl->set_file(array('sent_email'=>'view-email.html'));
$tpl->set_block('sent_email','ATTACHMENTS','attshandle');

$query = "SELECT attachment_id_PK, filename, content_type, filesize FROM attachments WHERE message_id_FK='{$message_id}'";
$atts = $db->opendb($query);

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

  $tpl->parse('attshandle','ATTACHMENTS',true);
}

if(!count($atts))
  $tpl->set_var('attshandle', '<tr><td class="defafont_b" colspan="4">No attachments</td></tr>');


$tpl->set_var(
  array(
    'email_from'      =>  $msg[0]['from'],
    'email_to'        =>  $msg[0]['to'],
    'email_cc'        =>  $msg[0]['cc'],
    'email_bcc'       =>  $msg[0]['bcc'],
    'email_date'      =>  date('l j F Y, g:i a', $msg[0]['date']),
    'email_subject'   =>  $msg[0]['subject'],
    'email_message'   =>  ($msg[0]['content_type'] == 'text/plain' ? nl2br($msg[0]['body']) : $msg[0]['body']),
    'pop_account'     =>  $_GET['pop_account'],
    'message_id'      =>  $_GET['message_id'],
    'action_script'   =>  $ThisScript
  )
);

$main_contents = $tpl->parse('sent_email', array('sent_email'));

include 'main.php';
?>
