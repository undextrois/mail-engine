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
$query = "SELECT folder_name FROM pop_folder WHERE folderID_PK='{$_GET['folder']}'";
$fldr = $db->opendb($query);

if($fldr[0]['folder_name'] == 'Inbox'){
  $table = 'inbox';
  $table_id = 'inboxID_PK';
}
else{
  $table = 'folder_msg';
  $table_id = 'msgID_PK';
}

$query = "
  SELECT
    `from`,
    `to`,
    subject,
    body,
    UNIX_TIMESTAMP(date_receive) AS date
  FROM
    {$table}
  WHERE
    {$table_id}='{$_GET['id']}'";

$reply_msg = $db->opendb($query);

$to = $reply_msg[0]['from'];

if(($pos = strpos($to, '<')) !== false){
  $pos2 = strpos($to, '>');

  $to = substr($to, $pos + 1, $pos2 - $pos - 1);
}

$reply_header = "


-------Original Message-------

From: {$reply_msg[0]['from']}
Date: ".date('n/j/Y G:i:s', $reply_msg[0]['date'])."
To: {$reply_msg[0]['to']}
Subject: {$reply_msg[0]['subject']}

";


echo "
<html>
<body onload=\"document.Reply.submit()\">
  <form method=\"post\" name=\"Reply\" action=\"compose.php\">
    <input type=\"hidden\" name=\"from_account\" value=\"{$_GET['pop']}\">
    <input type=\"hidden\" name=\"subject\" value=\"Re:".htmlentities($reply_msg[0]['subject'])."\">
    <input type=\"hidden\" name=\"to\" value=\"".htmlentities($to)."\">
    <input type=\"hidden\" name=\"message\" value=\"".htmlentities($reply_header.$reply_msg[0]['body'])."\">
  </form>
</body>
</html>";
?>
