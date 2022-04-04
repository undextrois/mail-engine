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


$userid = $_ML_Session->ssn_idPK;
#$pop_id = 0;
$displayDate = date("l F d, Y");


if ($_POST['folders']) { $def_folders = $_POST['folders'];
} else if ($_GET['folders']) { $def_folders = $_GET['folders'];
} else { $def_folders = 'Inbox'; }

if ($_POST['pop_acnts']) { $pop_id = $_POST['pop_acnts'];
} else if ($_GET['pop_acnts']) { $pop_id = $_GET['pop_acnts'];
} else { $pop_id = 0; }

$tpl=new Template(".","keep");
$tpl->set_file(array("tmpHandle"=>"inbox.html"));
$db = new _ALTO_DB;
$db->_use("_mailuser");

$tpl->set_var("display_date",$displayDate);


$select = " select * from pop_account where UserID_FK = $userid";
$pop_acnt = $db->opendb($select);

$tpl->set_block("tmpHandle","BLOCK","ROWcat");
foreach ($pop_acnt as $line) {
    $tpl->set_var("pop_values",$line["popID_PK"]);

    if ($line["popID_PK"] == $pop_id) {
       $tpl->set_var("selected","selected");
       $display =  $line["POP_EmailAddress"]." (Default)";
       $clear = $line["clear_server"];
    } else if (($pop_id == 0) && ($line["default_pop"] == 1)) {
       $tpl->set_var("selected","selected");
       $display =  $line["POP_EmailAddress"]." (Default)";
       $pop_id = $line["popID_PK"];
       $clear = $line["clear_server"];
    } else {
       $tpl->set_var("selected","");
       $display =  $line["POP_EmailAddress"];
    }
    $tpl->set_var("pop_display",$display);
    $tpl->parse("ROWcat","BLOCK",true);
}

$select = " select * from pop_folder where popID_FK = $pop_id";
$pop_folders = $db->opendb($select);

$tpl->set_var("parm","?clr=".$clear."&id=".$pop_id);

$tpl->set_block("tmpHandle","BLOCK2","ROWcat2");

foreach ($pop_folders as $fld_line) {
         $tpl->set_var("folder_values",$fld_line["folder_name"]);
         if ($def_folders == $fld_line["folder_name"]) {
                 $tpl->set_var("selected","selected");
                 $folder_id = $fld_line["folderID_PK"];
         } else {
                 $tpl->set_var("selected","");
         }
         $tpl->set_var("folder_display",$fld_line["folder_name"]);
         $tpl->parse("ROWcat2","BLOCK2",true);
}

if ($def_folders == 'Inbox') {
        $select = "select *, month(date_receive) as month, dayofmonth(date_receive) as day, year(date_receive) as year, hour(date_receive) as hr, minute(date_receive) as min from inbox where popID_FK = $pop_id order by date_receive desc";
} else {
        $select = "select *, month(date_receive) as month, dayofmonth(date_receive) as day, year(date_receive) as year, hour(date_receive) as hr, minute(date_receive) as min from folder_msg where folderID_FK = $folder_id order by date_receive desc";
}

$pop_inbox = $db->opendb($select);

$cnt = 0;
$tpl->set_block("tmpHandle","BLOCK3","ROWcat3");
if (($pop_inbox[0]['inboxID_PK'] == false) && ($pop_inbox[0]['msgID_PK'] == false)) {
        $tpl->set_var("checkbox","");
        $tpl->set_var("mail_icon","");
        $tpl->set_var("attach_icon","");
        $tpl->set_var("priority_icon","");
        $tpl->set_var("date","");
        $tpl->set_var("subject","");
        $tpl->set_var("from","");
        $tpl->set_var("option_icons","");
        $tpl->parse("ROWcat3","BLOCK3",true);
} else {
  foreach ($pop_inbox as $inbox_line) {
        $inboxid = ($inbox_line['inboxID_PK']) ? $inbox_line['inboxID_PK']: $inbox_line['msgID_PK'];
        $subject = ($inbox_line['subject']) ? $inbox_line['subject'] : "NONE" ;
        $from = ($inbox_line['from']) ? $inbox_line['from'] : "NONE";
        $from = preg_replace('/\</', '&lt;', $from);
        $from = preg_replace('/\>/', '&gt;', $from);
#        $from = $f;

        $tpl->set_var("checkbox",'<input type="checkbox" name="chk_'.$inboxid.'" value="'.$inboxid.'">');
        if ($inbox_line['flag'] == 1) { $flag = '<img border="0" src="images/CAI5CBOB.jpg" width="15" height="14">';
        } else { $flag = '<img border="0" src="images/mail_icon.gif" width="15" height="14">';  }

        $icons = '<a href="#" onclick="location.href=(\'viewmail.php?id='.$inboxid.'&folder='.$folder_id.'&pop='.$pop_id.'\')"><img border="0" src="images/edit1.gif" alt="View This Email" width="16" height="16"></a>&nbsp;
                        <a href="#" onclick="iconchg(2, '.$inboxid.')"><img border="0" src="images/delete.gif" alt="Delete This Email" width="16" height="16"></a>&nbsp;
                        <a href="#" onclick="window.open(\'movefiles.php?id='.$inboxid.'&folder='.$folder_id.'&pop='.$pop_id.'\',\'pickup\',\'scrollbars=yes,resizable=yes,screenX=50,screenY=50,left=50,top=50,width=250,height=250\');return false;"><img border="0" src="images/move.gif" alt="Move to Folder" width="16" height="16"></a>&nbsp;';
        if ($def_folders != 'Trash') {
             $icons .= '<a href="reply.php?id='.$inboxid.'&folder='.$folder_id.'&pop='.$pop_id.'"><img border="0" src="images/touch.gif" alt="Reply This Email" width="16" height="18"></a>&nbsp;';
        }
        # else {
        #     $icons .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        #}
        if ($inbox_line['attachment']) {
            $attach = '<img border="0" src="images/attachment.gif" width="10" height="13">';
            $icons .= '<a href="#" onclick="window.open(\'downloadfiles.php?id='.$inboxid.'&folder='.$folder_id.'\',\'pickup\',\'scrollbars=yes,resizable=yes,screenX=50,screenY=50,left=50,top=50,width=450,height=350\');return false;"><img border="0" src="images/download.jpg" alt="Download Attachments" width="16" height="16"></a>&nbsp;';
        } else {
            $attach = '&nbsp;';
            $icons .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        if ($inbox_line['priority'] == 'Highes') { $priority = '<img border="0" src="images/priority_post_icon2a.gif" width="17" height="17">';
        } else { $priority = '&nbsp;'; }
        #$priority = '<img border="0" src="images/priority_post_icon2.gif" width="17" height="17">';
        $displayDate = date("l F d, Y ", mktime(0, 0, 0, $inbox_line['month'], $inbox_line['day'], $inbox_line['year']));
        if ($inbox_line['hr'] > 12) {
                $hr = $inbox_line['hr'] - 12;
                $amp = "PM";
        } else {
                $hr = $inbox_line['hr'];
                $amp = "AM";
        }
        $hr = sprintf('%02d',$hr);
        $mint = sprintf('%02d',$line['min']);
        $displayDate .= "&nbsp;$hr:$mint $amp";

        $tpl->set_var("mail_icon",$flag);
        $tpl->set_var("attach_icon",$attach);
        $tpl->set_var("priority_icon",$priority);
        $tpl->set_var("date",$displayDate);
        $tpl->set_var("subject",$subject);
        $tpl->set_var("from",$from);
        $tpl->set_var("option_icons",$icons);
        $tpl->parse("ROWcat3","BLOCK3",true);
        $cnt++;
   }
}

if ($cnt > 0) {
    $buttons = '<tr>
                      <td class="defafont_b" align="center" colspan="8">
                        <p align="left">&nbsp;<img border="0" src="images/parent.gif" width="20" height="16">
                        Group Options: <input type="button" value="Delete" name="delete" class="ukbots" onclick="iconchg(2)">
                        <input type="button" value="Move to Folder" name="mvfolder" class="ukbots">
                        <input type="button" value="Mark As Read" name="markread" class="ukbots" onclick="iconchg(6, 1)">
                        <input type="button" value="Mark As Unread" name="markunread" class="ukbots"  onclick="iconchg(6, 0)"></td>
                    </tr>';
    if ($def_folders == 'Trash') {
      $tpl->set_var("msg","You have ".$cnt." in your Trash folder");
      $tpl->set_var("buttons",$buttons);
    } else if ($def_folders == 'Inbox') {
      $tpl->set_var("msg","You have ".$cnt." New Email in your inbox");
      $tpl->set_var("buttons",$buttons);
    } else {
      $tpl->set_var("msg","You have ".$cnt." in your ".$def_folders." folder");
      $tpl->set_var("buttons",$buttons);
    }
} else {
    if ($def_folders == 'Trash') {
       $tpl->set_var("msg","Trash folder is Empty");
      $tpl->set_var("buttons","");
    } else if ($def_folders == 'Inbox') {
      $tpl->set_var("msg","You Have no new Email At this time");
      $tpl->set_var("buttons","");
    } else {
      $tpl->set_var("msg",$def_folders." folder is Empty");
      $tpl->set_var("buttons","");
    }
}
//{main_contents}
$tpl->set_var("pop_id",$pop_id);
$tpl->set_var("folder_id",$folder_id);

$main_contents = $tpl->parse("tmpHandle", array("tmpHandle"));

$tpl->set_file(array("main"=>"main.html"));
$tpl->set_var(
  array(
    'page_title'      =>  'Today is '.date('jS of F'),
    'date_today'      =>  'Today is '.date('jS of F, Y'),
    'main_contents'   =>  $main_contents
  )
);

//  function parse($target, $handle, $append = false) {
$tpl->parse('main', array('main'));
$tpl->p("main");

#$tpl->finish("tmpHandle");
#$tpl->p("tmpHandle");

$db->closedb();
?>
