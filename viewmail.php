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
$action = ($_POST['action']) ? $_POST['action'] : "";
$filedir = ($_POST['file']) ? $_POST['file'] : "";

$select = "select * from pop_folder where folderID_PK = $folderid";
$folder = $db->opendb($select);

if ($action == 2) {
   $select = "select * from pop_folder where folderID_PK = $folderid";
   $folder = $db->opendb($select);
   if ($folder[0]['folder_name'] == 'Inbox') {
       $select = "select * from inbox where inboxID_PK = $id";
       $mailinfo = $db->opendb($select);
   } else {
       $select = "select * from pop_folder where folderID_PK = $folderid";
       $folderinfo = $db->opendb($select);

       $select = "select * from folder_msg where folderID_FK = $folderinfo[0][folderID_PK]";
       $mailinfo = $db->opendb($select);
   }
   $select = "select * from pop_folder where popID_FK = $popid and folder_name = 'Trash'";
   $trashinfo = $db->opendb($select);

   $newfolderid = $trashinfo[0][folderID_PK];

   foreach ($mailinfo as $line) {
      $select = "insert into folder_msg
                         values(NULL, ".$newfolderid.", '$line[from]', '$line[to]', '$line[cc]', '$line[bcc]', '$line[subject]', '$line[body]', '$line[attachment]', '$line[priority]', '$line[date_receive]', '$line[filesize]', '$line[flag]')";
      $newmsgid = $db->savedb2($select);
      if ($newmsgid) {
          $select = "delete from inbox where inboxID_PK = $id";
          $db->savedb($select);
      }
   }
   header("Location:inbox.php");

} else if ($action == 'download') {
       # header("Content-Disposition: inline; filename=$filedir");
       header('Content-type: application/force-download');
       header('Content-Transfer-Encoding: Binary');
       header('Content-length: '.filesize($filedir));
       header('Content-disposition: attachment;filename='.basename($filedir));
}

if ($folder[0]['folder_name'] == 'Inbox') {
    $select = "select *, month(date_receive) as month, dayofmonth(date_receive) as day, year(date_receive) as year, hour(date_receive) as hr, minute(date_receive) as min from inbox where inboxID_PK = $id";
    $mailinfo = $db->opendb($select);
} else {
    $select = "select *, month(date_receive) as month, dayofmonth(date_receive) as day, year(date_receive) as year, hour(date_receive) as hr, minute(date_receive) as min from folder_msg where folderID_FK = $folderinfo[0][folderID_PK] and msgID_PK = $id";
    $mailinfo = $db->opendb($select);
}

foreach ($mailinfo as $line) {
        $from = $line['from'];
        $to = $line['to'];
        $cc = $line['cc'];
        $bcc = $line['bcc'];
        $subject = $line['subject'];
        $body = $line['body'];
        $body = preg_replace("/\n/", "<br>", $body);
        $attachment = $line['attachment'];
        $priority = $line['priority'];
        $displayDate = date("l F d, Y ", mktime(0, 0, 0, $line['month'], $line['day'], $line['year']));
        if ($line['hr'] > 12) {
                $hr = $line['hr'] - 12;
                $amp = "PM";
        } else {
                $hr = $line['hr'];
                $amp = "AM";
        }
        $hr = sprintf('%02d',$hr);
        $mint = sprintf('%02d',$line['min']);

        $displayDate .= "&nbsp;$hr:$mint $amp";

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


<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<title>Today is 20th of October</title>
<link rel="stylesheet" type="text/css" href="main.css">
<script language="javascript">
<!--
  function iconchg(val, id) {
      document.vweml.action.value = val;
      document.vweml.id.value = id;
      document.vweml.submit();
  }
  function dwnfl(val) {
      document.attach.action.value = 'download';
      document.attach.file.value = val;
      document.attach.submit();
  }
//-->
</script>
</head>

<body topmargin="0" leftmargin="0">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" class="menubg">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50%" class="defafont_w">
            <table border="0" cellspacing="0" cellpadding="4">
              <tr>
                <td class="defafont_w"><b><img border="0" src="images/openfolder.gif" width="27" height="22"></b></td>
                <td class="defafont_w"><b>ALTOMAILER V1.0</b></td>
              </tr>
            </table>
          </td>
          <td width="50%" class="defafont_w">
            <p align="right"><b>You Are Now Online: Donniel C. Collera<br>
            Today is 20th of October, 2004</b></td>
        </tr>
        <tr>
          <td width="100%" class="defaltbg" colspan="2">
            <table border="0" width="100%">
              <tr>
                <td width="8%" class="defafont_b" valign="top">
                  <table border="0" cellpadding="10" bordercolor="#FFFFFF" bordercolorlight="#FFFFFF" bordercolordark="#FFFFFF">
                    <tr>
                      <td width="100%" align="center" class="menubox">
                        <p align="center"><a href="http://"><img border="0" src="images/home.gif" width="18" height="18"></a><br>
                        <a href="http://" class="menulink">Home</a></td>
                    </tr>
                    <tr>
                      <td width="100%" align="center" class="menubox">
                        <p align="center"><a href="http://"><img border="0" src="images/mail.gif" width="16" height="16"></a><br>
                        <a href="http://" class="menulink">Mail</a></td>
                    </tr>
                    <tr>
                      <td width="100%" align="center" class="menubox">
                        <p align="center"><a href="http://"><img border="0" src="images/explorer.gif" width="20" height="20"></a><br>
                        <a href="http://" class="menulink">File</a></td>
                    </tr>
                    <tr>
                      <td width="100%" align="center" class="menubox"><a href="http://"><img border="0" src="images/tools.gif" width="16" height="16"></a><br>
                        <a href="http://" class="menulink">
                        Options</a></td>
                    </tr>
                    <tr>
                      <td width="100%" align="center" class="menubox"><a href="http://"><img border="0" src="images/security.gif" width="15" height="16"></a><br>
                        <a href="http://" class="menulink">
                        Logout</a></td>
                    </tr>
                  </table>
                </td>
                <td width="92%" class="defafont_b" valign="top">
                  <table border="0" cellspacing="0" cellpadding="3" width="100%">
                    <tr>
                      <td width="50%">
                        <table border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td class="defafont_b"><img border="0" src="images/inboxicon.gif" width="38" height="39"></td>
                            <td class="defafont_b"><b><font size="5">Received Email</font></b></td>
                          </tr>
                        </table>
                      </td>
                    <form method="POST" name="vweml">
                      <td width="50%">
                          <div align="right">
                            <table border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td width="100%">
                                <input type="button" value="Reply This Email" name="B1" class="ukbots" onclick="location.href('reply.php?id=<?echo $id?>&folder=<?echo $folder?>&pop=<?echo $pop?>')">
                                <input type="button" value="Delete This Email" name="B1" class="ukbots" onclick="iconchg(2, <?echo $id?>)">
                                <input type="button" value="Return To INBOX" name="B1" class="ukbots" onclick="location.href('inbox.php')"></td>
                              </tr>
                            </table>
                          </div>
                      </td>
                      <input type="hidden" name="action" value="">
                      <input type="hidden" name="id" value="">
                      <input type="hidden" name="pop" value="<?echo $popid?>">
                      <input type="hidden" name="folder" value="<?echo $folderid?>">
                    </form>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">
                        <table border="0" cellspacing="0" cellpadding="3" width="903">
                          <tr>
                            <td class="defafont_b" width="91" valign="top"><b>Received From:&nbsp;</b></td>
                            <td class="defafont_b" width="796" valign="top"><?echo $from?></td>

                          </tr>
                          <tr>
                            <td class="defafont_b" width="91" valign="top"><b>Received To:</b></td>
                            <td class="defafont_b" width="796" valign="top"><?echo $to?></td>
                          </tr>
                          <tr>
                            <td class="defafont_b" width="91" valign="top"><b>CC:</b></td>
                            <td class="defafont_b" width="796" valign="top"><?echo $cc?></td>
                          </tr>
                          <tr>
                            <td class="defafont_b" width="91" valign="top"><b>Date Received:&nbsp;</b></td>
                            <td class="defafont_b" width="796" valign="top"><b><?echo $displayDate?></b></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">
                        <hr color="#000000">
                      </td>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">
                        <table border="1" width="100%" bordercolor="#000000" cellspacing="0" bordercolordark="#000000" bordercolorlight="#FFFFFF" cellpadding="3">
                          <tr>
                            <td width="100%" bgcolor="#CCCCCC" class="defafont_b"><b>SUBJECT:&nbsp;
                              <?echo $subject?></b></td>
                          </tr>
                          <tr>
                            <td width="100%" class="defafont_b"><?echo $body?></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">
                        <hr color="#000000">
                      </td>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">
                      <form method="POST" name="attach">
                        <table border="1" bordercolorlight="#FFFFFF" cellspacing="0" cellpadding="3" bordercolordark="#000000" bordercolor="#000000">
                          <tr>
                            <td bgcolor="#CCCCCC" class="defafont_b" colspan="4">Attachments:</td>
                          </tr>
                          <?echo $attachfiles?>
                        </table>
                        <input type="hidden" name="action" value="">
                        <input type="hidden" name="file" value="">
                      </form>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td width="100%" class="defaltbg" colspan="2">
            &nbsp;
          </td>
        </tr>
        <tr>
          <td width="100%" class="defaltbg" colspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="3">
              <tr>
                <td width="100%" class="defafont_b">
                  <p align="center">Registered to: Alto-meyer LLC<br>
                  All rights reserved 2004</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>

</body>

</html>
