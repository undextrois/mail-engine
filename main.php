<?
require_once ("../globals/config.inc.php");

include_once '_HW0NP12/general.inc.php';

$_redirect_url = "../index.php";

$_ML_Session = new _ML_Session_Class();
_no_cache();
if ($_ML_Session->ML_session_auth()==false)
{
   $_ML_Session->ML_session_destroy();
   _safe_redirect($_redirect_url);
}

$user_name = stripslashes($_ML_Session->ssn_user);

$tpl=new Template(".","keep");
$tpl->set_file(array("main"=>"main.html"));

$tpl->set_var(
  array(
    'page_title'          =>  'Today is '.date('jS of F'),
    'date_today'          =>  'Today is '.date('jS of F, Y'),
    'user_online'         =>  $user_name,
    'main_contents'       =>  $main_contents
  )

);
$tpl->parse('main', array('main'));
$tpl->p("main");
?>
