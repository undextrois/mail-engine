<?
include_once '_HW0NP12/general.inc.php';

$_redirect_url = "_ml_login.php";

$_ML_Session = new _ML_Session_Class();
_no_cache();
if ($_ML_Session->ML_session_auth()==true)
{
   print "ssn_idPK: $_ML_Session->ssn_idPK<br>\n";
   print "ssn_user: $_ML_Session->ssn_user<br>\n";
   print "ssn_ipaddr: $_ML_Session->ssn_ipaddr<br>\n";
   print "ssn_key1: $_ML_Session->ssn_key1<br>\n";
   print "ssn_key2: $_ML_Session->ssn_key2<br>\n";
   print "ssn_digest: $_ML_Session->ssn_digest<br>\n";
   print "ssn_login: $_ML_Session->ssn_login<br>\n";
   print "ssn_time: $_ML_Session->ssn_time<br>\n";

   print "<br><br>\n";
   print "<a href=\"_ml_logout.php\">LOGOUT</a>\n";
}
else
{   
   $_ML_Session->ML_session_destroy();
   _safe_redirect($_redirect_url);
}
?>
