<?
# if (!defined('__GN_U_MODULE_TRAP__')
#   || (__GN_U_MODULE_TRAP__ != 'GN-F4DCC0DD0B42F6B4C1565E59C92E308F-DVT'))
# {
#    die ("don't make me kick your arse!");
# }

$_redirect_url = "_ml_login.php";

include_once '_HW0NP12/general.inc.php';

$_ML_Session = new _ML_Session_Class();
_no_cache();
$_ML_Session->ML_session_destroy();

_safe_redirect($_redirect_url);
?>
