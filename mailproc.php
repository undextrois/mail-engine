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

$action = $_POST['action'];
$popid = $_POST['popid'];
$folderid = $_POST['folderid'];
$id = ($_POST['id']) ? $_POST['id'] : "";
$chkids = ($_POST['chkids']) ? $_POST['chkids'] : "";

if ($action == 2) {
   $select = "select * from pop_folder where folderID_PK = $folderid";
   $folder = $db->opendb($select);
   $folders = $folder[0]['folder_name'];

   if ($folder[0]['folder_name'] == 'Trash') {
      #echo "delete na!!!<br>";
      $select = "delete from folder_msg where folderID_FK = $folderid and msgID_PK = $id";
      #echo $select."<br>";
      $db->savedb($select);
   } else {
     if ($chkids) {
         $ids = split(',', $chkids);
         foreach ($ids as $r) {
           if ($r) {
              if ($folder[0]['folder_name'] == 'Inbox') {
                $select = "select * from inbox where inboxID_PK = $r";
                $mailinfo = $db->opendb($select);
              } else {
                $select = "select * from folder_msg where folderID_FK = $folderid and msgID_PK = $r";
                $mailinfo = $db->opendb($select);
              }
              $select = "select * from pop_folder where popID_FK = $popid and folder_name = 'Trash'";
              $trashinfo = $db->opendb($select);
              $newfolderid = $trashinfo[0][folderID_PK];

              foreach ($mailinfo as $line) {
                 $select = "insert into folder_msg values(NULL, ".$newfolderid.", '$line[from]', '$line[to]', '$line[cc]', '$line[bcc]', '$line[subject]', '$line[body]', '$line[attachment]', '$line[priority]', '$line[date_receive]', '$line[filesize]', '$line[flag]')";
                 $newmsgid = $db->savedb2($select);
                 if ($newmsgid) {
                    if ($folder[0]['folder_name'] == 'Inbox') {
                        $select = "delete from inbox where inboxID_PK = $r";
                        $db->savedb($select);
                    } else {
                        $select = "delete from folder_msg where msgID_PK = $r";
                        $db->savedb($select);
                    }
                 }
              }
           }
         }
     } else {
         if ($folder[0]['folder_name'] == 'Inbox') {
           $select = "select * from inbox where inboxID_PK = $id";
           $mailinfo = $db->opendb($select);
         } else {
           $select = "select * from folder_msg where folderID_FK = $folderid";
           $mailinfo = $db->opendb($select);
         }
         $select = "select * from pop_folder where popID_FK = $popid and folder_name = 'Trash'";
         $trashinfo = $db->opendb($select);

         $newfolderid = $trashinfo[0][folderID_PK];

         foreach ($mailinfo as $line) {
           $select = "insert into folder_msg values(NULL, ".$newfolderid.", '$line[from]', '$line[to]', '$line[cc]', '$line[bcc]', '$line[subject]', '$line[body]', '$line[attachment]', '$line[priority]', '$line[date_receive]', '$line[filesize]', '$line[flag]')";
           $newmsgid = $db->savedb2($select);
           if ($newmsgid) {
               if ($folder[0]['folder_name'] == 'Inbox') {
                  $select = "delete from inbox where inboxID_PK = $id";
                  $db->savedb($select);
               } else {
                  $select = "delete from folder_msg where msgID_PK = $id";
                  $db->savedb($select);
               }
           }
         }
     }
  }
} else if ($action == 6) {
   $select = "select * from pop_folder where folderID_PK = $folderid";
   $folder = $db->opendb($select);
   $folders = $folder[0]['folder_name'];

   $ids = split(',', $chkids);
   foreach ($ids as $r) {
       if ($r) {
           if ($folder[0]['folder_name'] == 'Inbox') {
              $select = "update inbox set flag = '$id' where inboxID_PK = $r";
              $db->savedb($select);
           } else {
              $select = "update folder_msg set flag = '$id' where msgID_PK = $r";
              $db->savedb($select);
           }
       }
   }

}

$db->closedb();

header("Location:inbox.php?pop_acnts=$popid&folders=$folders");

?>
