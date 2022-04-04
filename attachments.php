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

if($_POST['Action'] == 'Save Attachment'){
  include 'lib/class.file-uploader.php';
  $message_id = $_POST['message_id'];

  $File = new FileUploader('attachment1');
  if($File->Uploaded && $File->Allowed){
    $filename = addslashes_gpc($File->FileName);
    $content_type = $File->FileType;
    $filesize = $File->FileSize;

    do{
      $attachment_id = md5(uniqid(rand(),1));
      $query = "SELECT attachment_id_PK FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
      $mXs = $db->opendb($query);
    }while(count($MXs));

    $File->MoveUploaded("attachments/{$attachment_id}");

    $query = "INSERT INTO attachments (attachment_id_PK, message_id_FK, filename, content_type, filesize, date) VALUES ('{$attachment_id}', '{$message_id}', '{$filename}', '{$content_type}', '{$filesize}', NOW())";
    $db->_alto_insert_d1($query);
  }

  $File = new FileUploader('attachment2');
  if($File->Uploaded && $File->Allowed){
    $filename = addslashes_gpc($File->FileName);
    $content_type = $File->FileType;
    $filesize = $File->FileSize;

    do{
      $attachment_id = md5(uniqid(rand(),1));
      $query = "SELECT attachment_id_PK FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
      $mXs = $db->opendb($query);
    }while(count($MXs));

    $File->MoveUploaded("attachments/{$attachment_id}");

    $query = "INSERT INTO attachments (attachment_id_PK, message_id_FK, filename, content_type, filesize, date) VALUES ('{$attachment_id}', '{$message_id}', '{$filename}', '{$content_type}', '{$filesize}', NOW())";
    $db->_alto_insert_d1($query);
  }

  $File = new FileUploader('attachment3');
  if($File->Uploaded && $File->Allowed){
    $filename = addslashes_gpc($File->FileName);
    $content_type = $File->FileType;
    $filesize = $File->FileSize;

    do{
      $attachment_id = md5(uniqid(rand(),1));
      $query = "SELECT attachment_id_PK FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
      $mXs = $db->opendb($query);
    }while(count($MXs));

    $File->MoveUploaded("attachments/{$attachment_id}");

    $query = "INSERT INTO attachments (attachment_id_PK, message_id_FK, filename, content_type, filesize, date) VALUES ('{$attachment_id}', '{$message_id}', '{$filename}', '{$content_type}', '{$filesize}', NOW())";
    $db->_alto_insert_d1($query);
  }

  $File = new FileUploader('attachment4');
  if($File->Uploaded && $File->Allowed){
    $filename = addslashes_gpc($File->FileName);
    $content_type = $File->FileType;
    $filesize = $File->FileSize;

    do{
      $attachment_id = md5(uniqid(rand(),1));
      $query = "SELECT attachment_id_PK FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
      $mXs = $db->opendb($query);
    }while(count($MXs));

    $File->MoveUploaded("attachments/{$attachment_id}");

    $query = "INSERT INTO attachments (attachment_id_PK, message_id_FK, filename, content_type, filesize, date) VALUES ('{$attachment_id}', '{$message_id}', '{$filename}', '{$content_type}', '{$filesize}', NOW())";
    $db->_alto_insert_d1($query);
  }

  $File = new FileUploader('attachment5');
  if($File->Uploaded && $File->Allowed){
    $filename = addslashes_gpc($File->FileName);
    $content_type = $File->FileType;
    $filesize = $File->FileSize;

    do{
      $attachment_id = md5(uniqid(rand(),1));
      $query = "SELECT attachment_id_PK FROM attachments WHERE attachment_id_PK='{$attachment_id}'";
      $mXs = $db->opendb($query);
    }while(count($MXs));

    $File->MoveUploaded("attachments/{$attachment_id}");

    $query = "INSERT INTO attachments (attachment_id_PK, message_id_FK, filename, content_type, filesize, date) VALUES ('{$attachment_id}', '{$message_id}', '{$filename}', '{$content_type}', '{$filesize}', NOW())";
    $db->_alto_insert_d1($query);
  }

  echo "
<script language=\"javascript\" type=\"text/javascript\">
  opener.document.EmailForm.Action.value = '';
  opener.document.EmailForm.submit();
  opener.focus();
  window.close();
</script>";

  exit;
}

$tpl=new Template(".","keep"); //required for all template pages

$tpl->set_file(array('attachments'=>'attachments.html'));
$tpl->set_var(
  array(
    'message_id'      =>  $_GET['message_id'],
    'form_action'     =>  basename($_SERVER['PHP_SELF'])
  )
);
$main_contents = $tpl->parse('attachments',array('attachments'));

$tpl->set_file(array('popup_main'=>'popup.html'));
$tpl->set_var(
  array(
    'main_contents'   =>  $main_contents,
    'page_title'      =>  'Email Attachment(s)'
  )
);

$tpl->parse('popup_main',array('popup_main'));
$tpl->p('popup_main');
?>
