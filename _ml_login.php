<?
#############################################################################
# Login script v1.0
#
# Oct 27, 2004
# Altomeyer LLC
#############################################################################
#############################################################################
# TODO
# auth
#
#############################################################################

# if (!defined('__GN_U_MODULE_TRAP__')
#   || (__GN_U_MODULE_TRAP__ != 'GN-F4DCC0DD0B42F6B4C1565E59C92E308F-DVT'))
# {
#    die ("don't make me kick your arse!");
# }

include_once '_HW0NP12/general.inc.php';

define ('_DEF_PASSWD_MAX_LN',16);
define ('_DEF_USERID_MAX_LN',16);
define ('_DEF_PASSWD_MIN_LN',6);
define ('_DEF_USERID_MIN_LN',3);

# what the hell is this?
# T_PAAMAYIM_NEKUDOTAYIM 
# $this->main = 1;

$_debug_opt = false;
$_accept = false;

$_redirect_url = "main.php";                    # change this

#############################################################################
# session variables has been auto-generated
#
#############################################################################

class _ML_Log_Class
{

}

_ML_Session_Class::ML_session_start();
_no_cache();

if (($_mysql_con = @mysql_connect($GLOBALS['_ML_Config']['host'],
                                  $GLOBALS['_ML_Config']['user'],
                                  $GLOBALS['_ML_Config']['pass']))==false)
{
   _safe_croak(__LINE__,"unable to connect to server!");
exit;
}
if (($_msyql_link = @mysql_select_db($GLOBALS['_ML_Config']['db']))==false)
{
   _safe_croak(__LINE__,"unable to connect to server!");
exit;
}

#############################################################################
# check if remembered
#
# each time they log in, we must check their cookie first
# if they do exist and if it's valid, then we must create new session
# so that way, no one can poison our cookie
# we have to check our cookie cache first before we do that
#
# NOTE: cookie session is different from php session (double auth scheme)
#############################################################################

if (isset($HTTP_COOKIE_VARS))
{
   if (isset($HTTP_COOKIE_VARS['user']))
   {
      $_cache_user = trim ($HTTP_COOKIE_VARS['user']);
      if (!empty($_cache_user))
      {

      }
   }
}

#############################################################################
# continue what we are doing
#
#############################################################################

$_pxKYhash = array();

if (($_rs1=mysql_query('SELECT prefixKY,prefixid_PK FROM eAuthPrefix_Tbl'))==false)
{
   _safe_croak(__LINE__,mysql_error());
   mysql_close ($_mysql_con);
exit;
}

if (mysql_num_rows($_rs1))
{
   while ($_rw1 = mysql_fetch_object($_rs1))
   {
      $_pxKYhash[] = array('KY' => $_rw1->prefixKY,'NO' => $_rw1->prefixid_PK);
   }
   mysql_free_result($_rs1);
}

_safe_debug(__LINE__,count ($_pxKYhash));

srand( (double)microtime()*1000000 );

$_RnM = ( count($_pxKYhash) - 1 );
$_nCnf = $_RnM;        # this won't take that much time, i guess

while ($_nCnf--)
{
  $_RnT = rand(0,$_RnM); 
  if ($_RnT < 0) $_RnT = 0; else if ($_RnT > $_RnM) $_RnT = $_RnM;
  _safe_debug(__LINE__,"$_RnT | " . $_pxKYhash[$_RnT]['KY']);
}

_safe_debug(__LINE__,"_RnT: $_RnT");

$_pxKY = $_pxKYhash[$_RnT]['KY'];
$_pxKN = $_pxKYhash[$_RnT]['NO'];

$_tStmP = time();
$_amGk = rand(399,0xfff);
$_aCoD = ($_tStmP & 0x0fff);
$_aCoD = (($_aCoD ^ $_RnT) + $_amGk);
$_aCoD ^= ord($_pxKY{1});
$_aCoD ^= ord($_pxKY{0});
$_aCoD ^= ord($_pxKY{2});

$_CgCoD = md5("$_tStmP$_aCoD$_pxKY$_amGk");

_safe_debug(__LINE__,"CgCoD: $_CgCoD");

 $_aCod2 = "$_pxKY$_amGk";

$_RxN = 3;
$_LoP = strlen($_aCod2);

while ($_RxN < $_LoP)
{
  $_bM2 = $_CgCoD{7 + $_RxN};
  $_Ma0 = $_aCod2{$_RxN};
  $_CgCoD{7 + $_RxN} = $_Ma0;
  $_aCod2{$_RxN} = $_bM2;
  $_RxN++; 
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
{
   if (isset($_POST['_u1']) && isset($_POST['_p1']))
   {
      $_accept = true;

      $_Un01 = trim($_POST['_u1']);
      $_PxN0 = strlen($_Un01);
      $_UN01 = trim($_POST['_p1']);
      $_PXN0 = strlen($_UN01);

      if ($_PxN0 < _DEF_USERID_MIN_LN || $_PxN0 > _DEF_USERID_MAX_LN) $_accept = false;
      if ($_PXN0 < _DEF_PASSWD_MIN_LN || $_PXN0 > _DEF_PASSWD_MAX_LN) $_accept = false;
      if (preg_match("#^\w(\w|\_|\.|\-)+$#si",$_Un01,$m)==false) $_accept = false;

      if ($_accept)
      {
         if (($_rs1=@mysql_query("SELECT eInf.* FROM eLoginInfo_Tbl AS eInf WHERE eInf.user='$_Un01'"))==false)
         {
            _safe_croak(__LINE__,mysql_error());
            mysql_close($_mysql_con);
         exit;
         }
         if (mysql_num_rows($_rs1))
         {
            $_rw1 = mysql_fetch_object($_rs1);
            if ($_rw1->passwd != $_UN01)
            {
               $_accept = false;
            }
            mysql_free_result($_rs1);
         }
         else
         {
           $_accept = false;
         }

         if (isset($_POST['_challenge']))
            $_CgCoD = trim($_POST['_challenge']);
         else $_accept = false;
         if (isset($_POST['_authcode2']))
            $_aCod2 = trim($_POST['_authcode2']);
         else $_accept = false;
         if (isset($_POST['_authcode1']))
            $_aCoD = trim($_POST['_authcode1']);
         else $_accept = false;
         if (isset($_POST['_datestamp']))
            $_tStmP = trim($_POST['_datestamp']);
         else $_accept = false;

         $_cngeLN = strlen ($_CgCoD);
         if ($_cngeLN < 32 || $_cngeLN > 32) $_accept = false;

         $_LoP = strlen($_aCod2);
         $_RxN = 3;

         while ($_RxN < $_LoP)
         {
           $_bM2 = $_CgCoD{7 + $_RxN};
           $_Ma0 = $_aCod2{$_RxN};
           $_CgCoD{7 + $_RxN} = $_Ma0;
           $_aCod2{$_RxN} = $_bM2;
           $_RxN++; 
         }

         $_amGk = intval(substr($_aCod2,-(strlen($_aCod2) - 3)));
         $_pxKY = substr($_aCod2,0,3);

         $_aCoD ^= ord($_pxKY{2});
         $_aCoD ^= ord($_pxKY{0});
         $_aCoD ^= ord($_pxKY{1});

         if (($_rs0=@mysql_query("SELECT prefixKY,prefixid_PK FROM eAuthPrefix_Tbl WHERE prefixKY='$_pxKY'"))==false)
         {
            _safe_croak(__LINE__,mysql_error());
            mysql_close ($_mysql_con);
         exit;
         }
         if (mysql_num_rows($_rs0))
         {
            $_rw0 = mysql_fetch_object($_rs0);
            $_RnT = ($_rw0->prefixid_PK) - 1;
            _safe_debug(__LINE__,"_RnT: $_RnT");
         }
         else
         {
            $_accept = false;
         }

         $_aCoD = (($_aCoD ^ $_RnT) - $_amGk);
         # $_aCoD = ($_tStmP | 0x0fff);

         _safe_debug(__LINE__,"aCoD: $_aCoD");
         _safe_debug(__LINE__,"aCod2: $_aCod2");
         _safe_debug(__LINE__,"amGk: $_amGk");
         _safe_debug(__LINE__,"tStmP: $_tStmP");
         _safe_debug(__LINE__,"pxKY: $_pxKY");
         _safe_debug(__LINE__,"CgCoD: " . md5("$_tStmP$_aCoD$_pxKY$_amGk"));

         _safe_debug(__LINE__,"CgCoD: $_CgCoD");

         if ($_accept)
         {
            @mysql_close ($_mysql_con);
            _ML_Session_Class::ML_session_create($_Un01);
            _safe_redirect($_redirect_url);
         }
      }
   }
}
else
{
  
}

@mysql_close ($_mysql_con);

$_gn_tpl = new Template(".","keep");
$_gn_tpl->set_file(array("_gn_tpl"=>"login.htm"));

$_meta_tags = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">";
$_meta_tags.= "<meta http-equiv=\"Pragma\" content=\"no-cache\">";
$_meta_tags.= "<meta http-equiv=\"Expires\" content=\"0\">";
$_page_title = "Mailer::Login";

$_gn_tpl->set_var(
   array(
       'action-script' => $_SERVER['PHP_SELF'],
       'page-title'    => $_page_title,
       'meta-cache'    => $_meta_tags,
       '_challenge'    => $_CgCoD,
       '_datestamp'    => $_tStmP,
       '_authcode1'    => $_aCoD,
       '_authcode2'    => $_aCod2
   )
);

$_gn_tpl->parse("_gn_tpl",array("_gn_tpl"));
$_gn_tpl->finish("_gn_tpl");$_gn_tpl->p("_gn_tpl");
?>