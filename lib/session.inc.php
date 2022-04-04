<?
############################################################################
# date modified: 2004.01.07
# modified by:   altomeyer developers
#
# UPDATES
# 2004.01.07 - added srand
# 2004.12.07 - no multi-users allowed
# 2004.22.07 - hacked rights, _alto_session_rget function
# 2004.13.09 - new session variables
############################################################################

define ('__ALTO_SESSION_USER__',1);
define ('__ALTO_SESSION_SUPER__',2);
define ('__ALTO_SESSION_ADMIN__',3);

define ('MULTI_SESSION_ENABLE',1);

#############################################################################
#
# ukpa_session
#
# ssn_idPK int
# ssn_user char 16
# ssn_ipaddr int
# ssn_key char 32
# ssn_login timestamp
# ssn_time int
#
#############################################################################

class _UKPA_SessionClass
{
  var $ssn_idPK;
  var $ssn_user;
  var $ssn_ipaddr;
  var $ssn_key;
  var $ssn_login;
  var $ssn_time;

  var $logidPK;
  var $useridFK;
  var $entityidFK;

  var $isadmin;

  # new session variables for new composite
  #
  # godmode = (Y/N) pre
  # logtype = (A/M) pre
  #
  # IF godmode = y AND logtype = m THEN mcc god
  # IF godmode = n AND logtype = m THEN admin mcc
  # IF godmode = y AND logtype = a THEN agency god
  # IF godmode = n AND logtype = a THEN agency mcc

  var $useridses;
  var $mccidses;
  var $privsetidses;
  var $godmodeses;
  var $logtypeses;
  var $agencyidses;

}

class _ALTO_Session extends _UKPA_SessionClass
{

  function _ALTO_Session($m_stype = NULL)
  {
    session_start();
  }

  function _alto_session_cleanup()
  {

    if (session_is_registered($GLOBALS['_alto_config']['session']['user_id']))
        session_unregister($GLOBALS['_alto_config']['session']['user_id']);

    if (session_is_registered($GLOBALS['_alto_config']['session']['user_mgk']))
        session_unregister($GLOBALS['_alto_config']['session']['user_mgk']);

    session_destroy();
  }

  function _alto_session_create($m_id,$m_type)
  {

    session_register($GLOBALS['_alto_config']['session']['user_id']);
    session_register($GLOBALS['_alto_config']['session']['user_mgk']);

    $_loDB = new _ALTO_DB;
    $_loDB->dbconnect();

    if (($_loRS=mysql_query("SELECT * FROM c_sessions
                             WHERE ssn_user=\"".mysql_escape_string($m_id)."\""))==false)
    {
       print __LINE__ . ": ".mysql_error();
       mysql_close ($_acon);
    exit;
    }
    if (mysql_num_rows($_loRS))
    {
   /*dinagdag namin to wag kang umangal*/
    $tangna = mysql_fetch_row($_loRS);
    $id2=$tangna[1];
    //}
    //$id1=$id1[1];
   /********/
       @mysql_query("DELETE FROM c_sessions
                     WHERE ssn_user=\"".mysql_escape_string($m_id)."\"");
       mysql_free_result($_loRS);
    }

    srand((double)microtime()*1000000);
    $_mkey = uniqid(rand(),1);
    $_mkey = md5($m_id . $_mkey);

    if (mysql_query("INSERT INTO c_sessions SET
                     ssn_user = \"".mysql_escape_string($m_id)."\",
                     ssn_key  = \"".mysql_escape_string($_mkey)."\",
                     ssn_ipaddr  = ". ip2long($_SERVER['REMOTE_ADDR']). ",
                     ssn_time = NOW()")==false)
    {
        print __LINE__ . ": ".mysql_error();
        mysql_close($_acon);
    exit;
    }

    $_loDB->closedb();

    $_SESSION[$GLOBALS['_alto_config']['session']['user_id']] = "$m_id";
    $_SESSION[$GLOBALS['_alto_config']['session']['user_mgk']] = $_mkey;
    $_SESSION[$GLOBALS['_alto_config']['session']['chip']]  =$id2;

  }

  function _alto_session_auth()
  {
    if (session_is_registered($GLOBALS['_alto_config']['session']['user_id']) &&
       session_is_registered($GLOBALS['_alto_config']['session']['user_mgk']))
    {

       $this->ssn_key = $_SESSION[$GLOBALS['_alto_config']['session']['user_mgk']];
       $this->ssn_user = $_SESSION[$GLOBALS['_alto_config']['session']['user_id']];

       $_loDB = new _ALTO_DB;
       $_loDB->dbconnect();

       if (($_loRS=mysql_query("SELECT * FROM c_sessions
                                WHERE ssn_user=\"".mysql_escape_string($this->ssn_user)."\"
                                AND ssn_key=\"".mysql_escape_string($this->ssn_key)."\""))==false)
       {
          print __LINE__ . ": ".mysql_error();
          $_loDB->closedb();
       exit;
       }

       if (!mysql_num_rows($_loRS)) {

          @mysql_query("DELETE FROM c_sessions
                        WHERE ssn_user=\"".mysql_escape_string($this->ssn_user)."\"");
          mysql_free_result($_loRS);
          $this->_alto_session_cleanup();
          $_loDB->closedb();

       return false;
       }

       if (mysql_query("UPDATE c_sessions SET ssn_time=NOW() WHERE
                        ssn_user=\"".mysql_escape_string($this->ssn_user)."\"
                        AND ssn_ipaddr=".ip2long($_SERVER['REMOTE_ADDR']))==false)
       {
          print __LINE__. ": ".mysql_error();
          mysql_close ($_acon);
       exit;
       }

       if (($_loRS2=mysql_query("SELECT * FROM c_loginfo WHERE username=\"$this->ssn_user\""))==false)
       {
          print __LINE__. ": ".mysql_error();
          mysql_close ($_acon);
       exit;
       }

       $_patch_table = "c_agentinfo";

       $_loRW2 = mysql_fetch_object($_loRS2);

       if ($_loRW2->logtype=="M")
       {
           $_patch_table = "c_cminfo";
       }

       mysql_free_result($_loRS2);

       if (($_loRS=mysql_query("SELECT i.logidPK,i.useridFK,i.privsetidFK,i.mccidFK,i.privsetidFK,i.godmode,i.logtype,i.agencyidFK FROM c_loginfo AS i, $_patch_table AS u WHERE i.username=\"$this->ssn_user\" AND u.useridPK=i.useridFK"))==false)
       {
          print __LINE__. ": ".mysql_error();
          mysql_close ($_acon);
       exit;
       }

       if ($_loRW = mysql_fetch_array($_loRS))
       {
          $this->logidPK = $_loRW['logidPK'];
          $this->useridFK = $_loRW['useridFK'];

          $this->useridses = $_loRW['useridFK'];
          $this->mccidses = $_loRW['mccidFK'];
          $this->privsetidses = $_loRW['privsetidFK'];
          $this->godmodeses = $_loRW['godmode'];
          $this->logtypeses = $_loRW['logtype'];
          $this->agencyidses = $_loRW['agencyidFK'];

       mysql_free_result($_loRS);
       }
       else
       {
          $this->_alto_session_cleanup();
          return false;
       }

       $_loDB->closedb();

       $_SESSION[$GLOBALS['_alto_config']['session']['user_id']] = $this->ssn_user;
       $_SESSION[$GLOBALS['_alto_config']['session']['user_mgk']] = $this->ssn_key;
       //$_SESSION[$GLOBALS['_alto_config']['session']['chip']] = $id1;
       return true;
    }

  /*
  var $useridses;
  var $mccidses;
  var $privsetidses;
  var $godmodeses;
  var $logtypeses;
  var $agencyidses;
  */

  return false;
  }

  function _alto_session_uget()
  {
    return $this->ssn_user;
  }

  function _alto_session_lget()
  {
    return $this->logidPK;
  }

  function _alto_session_iget()
  {
    return $this->useridFK;

  }
  /*kami rin gumawa nito tangna ka*/
  function getuserid(){
   $username= $this->ssn_user;
   $_loRS2=mysql_query("SELECT * FROM c_loginfo
                             WHERE username='$username'");
    $tangna = mysql_fetch_row($_loRS2);
    $id3=$tangna[1];
    return $id3;
  }

  function getlogtype(){
   $username= $this->ssn_user;
   $_loRS2=mysql_query("SELECT * FROM c_loginfo
                             WHERE username='$username'");
    $tangna = mysql_fetch_row($_loRS2);
    $id3=$tangna[4];
    return $id3;
  }
   function getmccid(){
   $username= $this->ssn_user;
   $_loRS2=mysql_query("SELECT * FROM c_loginfo
                             WHERE username='$username'");
    $tangna = mysql_fetch_row($_loRS2);
    $id3=$tangna[7];
    return $id3;
  }
   function getgodmode(){
   $username= $this->ssn_user;
   $_loRS2=mysql_query("SELECT * FROM c_loginfo
                             WHERE username='$username'");
    $tangna = mysql_fetch_row($_loRS2);
    $id3=$tangna[8];
    return $id3;
  }
   function getprivsetid(){
   $username= $_SESSION[$GLOBALS['_alto_config']['session']['chip']];
   $_loRS2=mysql_query("SELECT * FROM c_loginfo
                             WHERE username='$username'");
    $tangna = mysql_fetch_row($_loRS2);
    $id3=$tangna[5];
    return $id3;
  }

   function getagencyid(){
   $userid= $this->getuserid();
   $_loRS3=mysql_query("SELECT agencyidFK FROM c_agentinfo
                             WHERE useridPK='$userid'");
    $tangna = mysql_fetch_row($_loRS3);
    $id3=$tangna[0];
    return $id3;
  }
  /**************************/
  function _alto_session_eget()
  {
    return $this->entityidFK;
  }

  function _alto_session_rget()
  {
    return $this->isadmin;
  }
}

class _ALTO_Loginfo {
  var $logidPK;
  var $useridPK;
  var $username;
  var $password;

  function _alto_is_user($m_user) {
    $m_lng = strlen($m_user);

    if (($m_lng <= $GLOBALS['_alto_config']['auth-user']['mx'])
       && ($m_lng >= $GLOBALS['_alto_config']['auth-user']['mn'])) {
       if (preg_match("#^\w(\w|\_|\.|\-)+$#si",$m_user,$m_m)==false) return false;
    return true;
    }
  return false;
  }

  function _alto_is_passwd($m_pass) {
    $m_lng = strlen($m_pass);
    if (($m_lng <= $GLOBALS['_alto_config']['auth-pass']['mx'])
       && ($m_lng >= $GLOBALS['_alto_config']['auth-pass']['mn'])) {
    return true;
    }
  return false;
  }

  function _alto_auth($m_user,$m_pass) {
    global $_DB;
  }
}
?>