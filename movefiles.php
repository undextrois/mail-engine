<?php
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

$id = ($_GET['id']) ? $_GET['id'] : $_POST['id'];
$folderid = ($_GET['folder']) ? $_GET['folder'] : $_POST['folder'];
$popid = ($_GET['pop']) ? $_GET['pop'] : $_POST['pop'];



$select = "select * from pop_folder where popID_FK = $popid";
$folder = $db->opendb($select);

if ($_POST['Submit']) {
    if ($folder[0]['folder_name'] == 'Inbox') {
        $select = "select * from inbox where inboxID_PK = $id";
        $mailinfo = $db->opendb($select);
    } else {
      $select = "select * from pop_folder where folderID_PK = $folderid";
      $folderinfo = $db->opendb($select);

      $select = "select * from folder_msg where folderID_FK = $folderinfo[0][folderID_PK] and msgID_PK = $id";
      $mailinfo = $db->opendb($select);
    }

    foreach ($mailinfo as $line) {
             $select = "insert into folder_msg
                         values(NULL, ".$_POST['newfolder'].", '$line[from]', '$line[to]', '$line[cc]', '$line[bcc]', '$line[subject]', '$line[body]', '$line[attachment]', '$line[priority]', '$line[date_receive]', '$line[filesize]', '$line[flag]')";
             echo $select."<br>";
             $newmsgid = $db->savedb2($select);
             if ($newmsgid) {
                $select = "delete from inbox where inboxID_PK = $id";
                $db->savedb($select);
             }
    }
    echo "<script language='javascript'>
<!--
        opener.location.href('http://192.168.0.201/mailer/inbox.php');
        self.close();
-->
</script>";
}


foreach ($folder as $fl) {
        $folderlist .= '<option value="'.$fl['folderID_PK'].'">'.$fl['folder_name'].'</option>';
}



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="main.css">
<title>Move Mail</title>
</head>

<body>
<form name="mvfl" method="post">
<table width="116" border="1" cellpadding="3" cellspacing="0" bordercolor="#000000" bordercolorlight="#FFFFFF" bordercolordark="#000000">
  <tr>
    <td width="106" bgcolor="#CCCCCC" class="defafont_b"><img src="images/move.gif" width="16" height="16"> Move to </td>
  </tr>
  <tr>
    <td align="center" class="defafont_b">
      <select name="newfolder">
        <?echo $folderlist?>
      </select>
    </td>
  </tr>
  <tr>
    <td height="27" align="center" class="defafont_b"><table width="100%"  border="0">
        <tr>
          <td width="39%">&nbsp;</td>
          <td width="22%"><input type="submit" name="Submit" value="Submit"></td>
          <td width="39%">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
</table>
<input type="hidden" name="action" value="">
<input type="hidden" name="id" value="<?echo $id?>">
<input type="hidden" name="pop" value="<?echo $popid?>">
<input type="hidden" name="folder" value="<?echo $folderid?>">
</form>
</body>
</html>
