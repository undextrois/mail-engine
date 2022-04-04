<?
#############################################################################
# Package: session.inc.php
# Date: November 2004
#
# Altomeyer LLC
#
#############################################################################

if (!defined('__GN_U_MODULE_TRAP__')
  || (__GN_U_MODULE_TRAP__ != 'GN-F4DCC0DD0B42F6B4C1565E59C92E308F-DVT'))
{
   die ("don't make me kick your arse!");
}

if (defined('__GN_U_MODULE_PATCH_FOR_NEWUKPA__')
  && (__GN_U_MODULE_PATCH_FOR_NEWUKPA__ == 'GN-F4DCC0DD0B42F6B4C1565E59C92E308F-DVT'))
{
   if (!class_exists('_UKPA_SessionClass'))
      include_once '../globals/session.inc.php';

   class _ML_Session_Class extends _ALTO_Session
   {
     function _ML_Session_Class()
     {
       $this->ML_session_start(); 
     }

     function ML_session_start()
     {
       session_start(); 
     }

     function ML_session_destroy()
     {
       $this->_alto_session_cleanup();
     }

     function ML_session_create($_u)
     {
       $this->_alto_session_create($_u,1);
     }
 
     function ML_session_auth()
     {
       return $this->_alto_session_auth();
     }
   }
}
else
{
   $_session_vars{'_timeStamp'} = "G7quR";
   $_session_vars{'_key2'}      = "Aqt1y";
   $_session_vars{'_digest'}    = "osFO6";
   $_session_vars{'_user'}      = "Ppmmj";
   $_session_vars{'_key1'}      = "ryyHR";

   class _ML_SessionEx_Class
   {
     var $ssn_idPK;
     var $ssn_user;
     var $ssn_ipaddr;
     var $ssn_key1;
     var $ssn_key2;
     var $ssn_digest;
     var $ssn_login;
     var $ssn_time;
   }
 
   class _ML_Session_Class extends _ML_SessionEx_Class
   {
     function _ML_Session_Class()
     {
       $this->ML_session_start(); 
     }

     function ML_session_start()
     {
        session_start();
  
        $_session_vars{'_timeStamp'} = "G7quR";
        $_session_vars{'_key2'}      = "Aqt1y";
        $_session_vars{'_digest'}    = "osFO6";
        $_session_vars{'_user'}      = "Ppmmj";
        $_session_vars{'_key1'}      = "ryyHR";
     }

     function ML_session_destroy()
     {

       if ((session_is_registered($GLOBALS['_session_vars']{'_timeStamp'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_key2'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_digest'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_user'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_key1'})))
       {
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
  
          if (mysql_query("DELETE FROM eSessions_Tbl
                           WHERE eSessions_Tbl.ssn_user = '".mysql_escape_string($_u)."'")==false)
          {
             _safe_croak(__LINE__,mysql_error());
             mysql_close ($_mysql_con);
          exit;
          }

          mysql_close ($_mysql_con); 
       }

       if (session_is_registered($GLOBALS['_session_vars']{'_timeStamp'}))
           session_unregister($GLOBALS['_session_vars']{'_timeStamp'});    # timestamp
       if (session_is_registered($GLOBALS['_session_vars']{'_key2'}))
           session_unregister($GLOBALS['_session_vars']{'_key2'});         # key 2
       if (session_is_registered($GLOBALS['_session_vars']{'_digest'}))
           session_unregister($GLOBALS['_session_vars']{'_digest'});       # digest
       if (session_is_registered($GLOBALS['_session_vars']{'_user'}))
           session_unregister($GLOBALS['_session_vars']{'_user'});         # session user
       if (session_is_registered($GLOBALS['_session_vars']{'_key1'}))
           session_unregister($GLOBALS['_session_vars']{'_key1'});         # key 1
 
       session_destroy();
     }

     function ML_session_create($_u)
     {
       if (session_is_registered($GLOBALS['_session_vars']{'_timeStamp'}))
           session_unregister($GLOBALS['_session_vars']{'_timeStamp'});    # timestamp
       if (session_is_registered($GLOBALS['_session_vars']{'_key2'}))
           session_unregister($GLOBALS['_session_vars']{'_key2'});         # key 2
       if (session_is_registered($GLOBALS['_session_vars']{'_digest'}))
           session_unregister($GLOBALS['_session_vars']{'_digest'});       # digest
       if (session_is_registered($GLOBALS['_session_vars']{'_user'}))
           session_unregister($GLOBALS['_session_vars']{'_user'});         # session user
       if (session_is_registered($GLOBALS['_session_vars']{'_key1'}))
           session_unregister($GLOBALS['_session_vars']{'_key1'});         # key 1
   
       session_register($GLOBALS['_session_vars']{'_timeStamp'});    # timestamp
       session_register($GLOBALS['_session_vars']{'_key2'});         # key 2
       session_register($GLOBALS['_session_vars']{'_digest'});       # digest
       session_register($GLOBALS['_session_vars']{'_user'});         # session user
       session_register($GLOBALS['_session_vars']{'_key1'});         # key 1
 
       $_timeStamp = time();
 
       srand((double)microtime()*1000000);

       $_key2 = md5(uniqid(rand(),true));
       $_key1 = md5(uniqid(rand(),true));
       $_digest = md5("$_u-$_timeStamp");

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
 
       if (($_rs0=mysql_query("SELECT s.* FROM eSessions_Tbl AS s
                               WHERE s.ssn_user = '".mysql_escape_string($_u)."'"))==false)
       {
          _safe_croak(__LINE__,mysql_error());
          mysql_close ($_mysql_con);
       exit;
       }
       if (mysql_num_rows($_rs0))
       {
          mysql_free_result($_rs0);
          if (mysql_query("DELETE FROM eSessions_Tbl
                           WHERE eSessions_Tbl.ssn_user = '".mysql_escape_string($_u)."'")==false)
          {
             _safe_croak(__LINE__,mysql_error());
             mysql_close ($_mysql_con);
          exit;
          }
       }
       if (mysql_query("INSERT INTO eSessions_Tbl SET
                        ssn_user = '".mysql_escape_string($_u)."',
                        ssn_key1 = '".mysql_escape_string($_key1)."',
                        ssn_key2 = '".mysql_escape_string($_key2)."',
                        ssn_digest = '".mysql_escape_string($_digest)."',
                        ssn_ipaddr = ".ip2long($_SERVER['REMOTE_ADDR']).",
                        ssn_login = '$_timeStamp',
                        ssn_time = NOW()")==false)
       {
          _safe_croak(__LINE__,mysql_error());
          mysql_close ($_mysql_con);
       exit;
       }

       mysql_close ($_mysql_con);

       $_SESSION[$GLOBALS['_session_vars']{'_timeStamp'}] = $_timeStamp;
       $_SESSION[$GLOBALS['_session_vars']{'_key2'}] = $_key2;
       $_SESSION[$GLOBALS['_session_vars']{'_digest'}] = $_digest;
       $_SESSION[$GLOBALS['_session_vars']{'_user'}] = $_u;
       $_SESSION[$GLOBALS['_session_vars']{'_key1'}] = $_key1;
  
     }
 
     function ML_session_auth()
     {
       if ((session_is_registered($GLOBALS['_session_vars']{'_timeStamp'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_key2'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_digest'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_user'}))
          && (session_is_registered($GLOBALS['_session_vars']{'_key1'})))
       {
         # $this->ssn_idPK;

         $this->ssn_user = $_SESSION[$GLOBALS['_session_vars']{'_user'}];
         $this->ssn_key1 = $_SESSION[$GLOBALS['_session_vars']{'_key1'}];
         $this->ssn_key2 = $_SESSION[$GLOBALS['_session_vars']{'_key2'}];
         $this->ssn_digest = $_SESSION[$GLOBALS['_session_vars']{'_digest'}];
         $this->ssn_login = $_SESSION[$GLOBALS['_session_vars']{'_timeStamp'}];
 
         # $this->ssn_time;

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

         if (($_rs0=mysql_query("SELECT s.*,i.* FROM eSessions_Tbl AS s, eLoginInfo_Tbl AS i
                                 WHERE s.ssn_user = '".mysql_escape_string($this->ssn_user)."'
                                 AND s.ssn_key1 = '".mysql_escape_string($this->ssn_key1)."'
                                 AND s.ssn_key2 = '".mysql_escape_string($this->ssn_key2)."'
                                 AND i.user = s.ssn_user"))==false)
         {
            _safe_croak(__LINE__,mysql_error());
            mysql_close ($_mysql_con);
         exit;
         }
         if (!mysql_num_rows($_rs0))
         {
            mysql_close ($_mysql_con);
            $this->ML_session_destroy();
            return false;
         }
         else
         {
           $_rw0 = mysql_fetch_object($_rs0);
           $this->ssn_idPK = $_rw0->userid_PK;
           $this->ssn_ipaddr = long2ip($_rw0->ssn_ipaddr);
           mysql_free_result($_rs0);
         }
 
         if (mysql_query("UPDATE eSessions_Tbl SET eSessions_Tbl.ssn_time = NOW()
                          WHERE eSessions_Tbl.ssn_user = '".mysql_escape_string($this->ssn_user)."'")==false)
         {
            _safe_croak(__LINE__,mysql_error());
            mysql_close ($_mysql_con);
         exit;
         }
   
         $_SESSION[$GLOBALS['_session_vars']{'_timeStamp'}] = $this->ssn_login;
         $_SESSION[$GLOBALS['_session_vars']{'_key2'}] = $this->ssn_key2;
         $_SESSION[$GLOBALS['_session_vars']{'_digest'}] = $this->ssn_digest;
         $_SESSION[$GLOBALS['_session_vars']{'_user'}] = $this->ssn_user;
         $_SESSION[$GLOBALS['_session_vars']{'_key1'}] = $this->ssn_key1;

         mysql_close ($_mysql_con);
 
         return true;
       }
       else
       {
          return false;
       }
     }
   }
}
?>
