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

if ($action == 'download') {
       # header("Content-Disposition: inline; filename=$filedir");
       header('Content-type: application/force-download');
       header('Content-Transfer-Encoding: Binary');
       header('Content-length: '.filesize($filedir));
       header('Content-disposition: attachment;filename='.basename($filedir));
}

$select = "select * from pop_folder where folderID_PK = $folderid";
$folder = $db->opendb($select);

if ($folder[0]['folder_name'] == 'Inbox') {
    $select = "select *, month(date_receive) as month, dayofmonth(date_receive) as day, year(date_receive) as year, hour(date_receive) as hr, minute(date_receive) as min from inbox where inboxID_PK = $id";
    $mailinfo = $db->opendb($select);
} else {
    $select = "select *, month(date_receive) as month, dayofmonth(date_receive) as day, year(date_receive) as year, hour(date_receive) as hr, minute(date_receive) as min from folder_msg where folderID_FK = $folderinfo[0][folderID_PK] and msgID_PK = $id";
    $mailinfo = $db->opendb($select);
}

foreach ($mailinfo as $line) {
        $attachment = $line['attachment'];
        if ($attachment) {
            $attachs = split("]", $attachment);
            foreach ($attachs as $at) {
                if ($at != " ") {
                    $at = preg_replace("/\[/", "", $at);
                    $at = preg_replace("/\]/", "", $at);
                    $at = preg_replace("/^ /", "", $at);

                    preg_match("/\/(.*)/", $at, $matches, PREG_OFFSET_CAPTURE);
                    preg_match("/\/(.*)/", $at, $matches2, PREG_OFFSET_CAPTURE);
                    $filesd = $matches2[0][0];

                    if (preg_match("/\.csv|\.xls/", $matches[0][0])) {
                            $icon = '<img border="0" src="images/xls.gif" width="17" height="17">';
                    } else if (preg_match("/\.gif|\.jpeg|\.jpg|\.bmp|\.tif/", $matches[0][0])) {
                            $icon = '<img border="0" src="images/jpg.gif" width="20" height="20">';
                    } else if (preg_match("/\.db/", $matches[0][0])) {
                            $icon = '<img border="0" src="images/database.gif" width="16" height="20">';
                    } else if (preg_match("/\.doc/", $matches[0][0])) {
                            $icon = '<img border="0" src="images/word.gif" width="17" height="17">';
                    } else if (preg_match("/\.txt/", $matches[0][0])) {
                            $icon = '<img border="0" src="images/page.gif" width="16" height="16">';
                    } else if (preg_match("/\.eml/", $matches[0][0])) {
                            $icon = '<img border="0" src="images/mail_icon.gif" width="15" height="14">';
                    } else {
                            $icon = '<img border="0" src="images/fileicon.gif" width="11" height="13">';
                    }
                    $dirfl = '/var/www/cgi-bin/attachs/'.$at;
                    $flsize = filesize($dirfl);
                    $flsize .= "kb";

                    $filesd = preg_replace("/\//", "", $filesd);
                    $attachfiles .= '<tr>
                            <td class="defafont_b" align="center">'.$icon.'</td>
                            <td class="defafont_b">'.$filesd.'</td>
                            <td class="defafont_b">'.$flsize.'</td>
                            <td class="defafont_b"><input type="button" value="Download" name="B1" class="ukbots" onclick="dwnfl(\''.$dirfl.'\');"></td>
                          </tr>';
                }
            }
        }
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="main.css">
<title>Download Attachments</title>
<script language="javascript">
<!--
  function dwnfl(val) {
      document.attach.action.value = 'download';
      document.attach.file.value = val;
      document.attach.submit();
  }
//-->
</script>
</head>

<body>
<form method="POST" name="attach">
<table border="1" bordercolorlight="#FFFFFF" cellspacing="0" cellpadding="3" bordercolordark="#000000" bordercolor="#000000">
  <tr>
    <td bgcolor="#CCCCCC" class="defafont_b" colspan="4"> <img src="images/download.jpg" width="16" height="16"> Download</td>
  </tr>
  <tr>
    <td width="34" height="34" align="center" class="defafont_b">&nbsp; </td>
    <td width="126" class="defafont_b">File Name:{filename}</td>
    <td width="87" class="defafont_b">Files Size:{size}</td>
    <td width="21" class="defafont_b">&nbsp;</td>
  </tr>
  <?echo $attachfiles?>
</table>
<input type="hidden" name="action" value="">
<input type="hidden" name="file" value="">
</form>
</body>
</html>
