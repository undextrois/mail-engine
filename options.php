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
$ThisScript = basename($_SERVER['PHP_SELF']);

if($_POST['Action'] == 'Set Default'){
  $default = $_POST['default'];
  $query = "UPDATE pop_account SET default_pop=NULL WHERE UserID_FK='{$userid}'";
  $db->savedb($query);

  $query = "UPDATE pop_account SET default_pop=1 WHERE UserID_FK='{$userid}' AND popID_PK='{$default}'";
  $db->savedb($query);

  Header("Location: {$ThisScript}");
  exit;
}

if($_GET['Action'] == 'Delete'){
  $pop_id = $_GET['pop_id'];

  $query = "DELETE FROM pop_account WHERE UserID_FK='{$userid}' AND popID_PK='{$pop_id}'";
  $db->savedb($query);

  Header("Location: {$ThisScript}");
  exit;
}

if($_POST['Action'] == 'Save Account'){
  $edit = $_POST['edit'];
  $server = addslashes_gpc($_POST['server']);
  $username = addslashes_gpc($_POST['username']);
  $password = addslashes_gpc($_POST['password']);
  $email = addslashes_gpc($_POST['email']);
  $default = $_POST['default'];
  $delete = $_POST['leave'] ? 'NULL' : 1;

  if($edit == 'Y'){
    $query = "
      UPDATE
        pop_account
      SET
        POP_Server='{$server}',
        POP_UserName='{$username}',
        POP_Password='{$password}',
        POP_EmailAddress='{$email}',
        clear_server={$delete}
      WHERE
        UserID_FK='{$userid}'
        AND popID_PK='{$pop_id}'";
  }
  else{
    $query = "
      INSERT INTO
        pop_account
      (
        UserID_FK,
        POP_Server,
        POP_UserName,
        POP_Password,
        POP_EmailAddress,
        clear_server
      )
      VALUES
      (
        '{$userid}',
        '{$server}',
        '{$username}',
        '{$password}',
        '{$email}',
        {$delete}
      )";
  }

  $pop_id = $db->savedb2($query);
  $pop_id = $edit == 'Y' ? $_POST['pop_id'] : $pop_id;

  if($default){
    $query = "UPDATE pop_account SET default_pop=NULL WHERE UserID_FK='{$userid}'";
    $db->savedb($query);
  }

  $query = "UPDATE pop_account SET default_pop=".($default ? 1 : 'NULL')." WHERE UserID_FK='{$userid}' AND popID_PK='{$pop_id}'";
  $db->savedb($query);

  Header("Location: {$ThisScript}");
  exit;
}

$tpl=new Template(".","keep"); //required for all template pages
$tpl->set_file(array('options_main'=>'options_main.html'));
$main_contents = $tpl->parse('options_main', array('options_main'));

if($_GET['edit'] != 'Y'){
  $tpl->set_file(array('options1'=>'options1.html'));
  $tpl->set_block('options1', 'POPACCTS', 'pophandle');
  $query = "
    SELECT
      popID_PK,
      POP_Server,
      POP_EmailAddress,
      default_pop
    FROM
      pop_account
    WHERE
      UserID_FK='{$userid}'";
  $pops = $db->opendb($query);

  foreach($pops AS $pop){
    $tpl->set_var(
      array(
        'pop_id'        =>  $pop['popID_PK'],
        'pop_server'    =>  $pop['POP_Server'],
        'pop_email'     =>  $pop['POP_EmailAddress'],
        'pop_checked'   =>  ($pop['default_pop'] ? ' checked' : '')
      )
    );

    $tpl->parse('pophandle', 'POPACCTS', true);
  }

  if(!count($pops)){
    $tpl->set_var('pophandle', '<tr><td colspan="4" align="center"><b><i>There are no POP3 accounts</i></b></td></tr>');
    $pop_options = '';
  }
  else{
    $pop_options = "
                    <tr>
                      <td class=\"defafont_b\" align=\"center\" colspan=\"4\">
                        <p align=\"left\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img border=\"0\" src=\"images/parent.gif\" width=\"20\" height=\"16\">
                        Group Options: <input type=\"submit\" value=\"Update Default\" class=\"ukbots\">
                    </tr>";
  }

  $tpl->set_var(
    array(
      'options_form_action'     =>  $ThisScript,
      'pop_options'             =>  $pop_options
    )
  );

  $main_contents .= $tpl->parse('options1', array('options1')).'<p>&nbsp;</p>';

  $server_value = '';
  $username_value = '';
  $password_value = '';
  $email_value = '';
  $default_value = '';
  $delete_value = '';
}
else{
  $query = "
    SELECT
      POP_Server,
      POP_UserName,
      POP_Password,
      POP_EmailAddress,
      default_pop,
      clear_server
    FROM
      pop_account
    WHERE
      UserID_FK='{$userid}'
      AND popID_PK='{$_GET['pop_id']}'";
  $pops = $db->opendb($query);

  $server_value = $pops[0]['POP_Server'];
  $username_value = $pops[0]['POP_UserName'];
  $password_value = $pops[0]['POP_Password'];
  $email_value = $pops[0]['POP_EmailAddress'];
  $default_value = $pops[0]['default_pop'];
  $delete_value = $pops[0]['clear_server'];
}

$tpl->set_file(array('pop_account'=>'pop-account-form.html'));
$tpl->set_var(
  array(
    'options_form_action'     =>  $ThisScript,
    'server_value'            =>  $server_value,
    'username_value'          =>  $username_value,
    'password_value'          =>  $password_value,
    'email_value'             =>  $email_value,
    'pop_id'                  =>  ($_GET['edit'] == 'Y' ? $_GET['pop_id'] : ''),
    'edit_pop'                =>  $_GET['edit'],
    'pop_edit_title'          =>  $_GET['edit'] == 'Y' ? 'Update' : 'New',
    'default_checked'         =>  ($default_value ? ' checked' : ''),
    'leave_checked'          =>  (!$delete_value ? ' checked' : '')
  )
);

$main_contents .= $tpl->parse('pop_account', array('pop_account'));

include 'main.php';
?>
