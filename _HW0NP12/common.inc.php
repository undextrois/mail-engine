<?
#############################################################################
# Package: common.inc.php
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

function _safe_croak($ln,$ms)
{
  print "croak [$ln]: $ms\n";
}

function _safe_debug($ln,$ms)
{
  if (isset($GLOBALS['_ML_Config']['_debug_opt'])
      && $GLOBALS['_ML_Config']['_debug_opt'])
      print "dbg [$ln]: $ms<br>\n";
}

function _safe_redirect($_url)
{
  header("location: $_url");
exit;
}

function _no_cache()
{
   $now = gmdate('D, d M Y H:i:s') . ' GMT';
   header('Expires: ' . $now); // rfc2616 - Section 14.21
   header('Last-Modified: ' . $now);
   header('Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
   header('Pragma: no-cache'); // HTTP/1.0
}

function _get_remote_ip()
{
  global $HTTP_CLIENT_IP, $HTTP_VIA, $HTTP_FROM;
  global $CLIENT_IP, $REMOTE_ADDR, $REMOTE_HOST;
  global $HTTP_REFERER, $HTTP_REMOTE_PORT, $HTTP_USER_AGENT;
  global $HTTP_REQUEST_URI, $HTTP_REQUEST_METHOD;
  global $HTTP_CACHE_CONTROL;

  /*

   transparent
   anonymous
   high-anonimity


   TRANSPARENT

   if ($HTTP_X_FORWARDED_FOR)
   {

      get proxy information
      $REMOTE_ADDR
      $HTTP_X_FORWARDED_FOR is my real IP address
   }

   other TRANSPARENT

   if ($HTTP_X_FORWARDED_FOR)
   {

      if ($HTTP_VIA)
      {
         get proxy information
         $REMOTE_ADDR is another proxy's IP
         $HTTP_X_FORWARDED_FOR is my real IP address and other proxy's ip
         $HTTP_X_FORWARDED_FOR = 202.73.163.110,210.212.140.25
         or could be just my IP only
      }
   }

   ANONYMOUS

   if ($HTTP_X_FORWARDED_FOR)
   {
      if ($HTTP_VIA)
      {
         save $HTTP_VIA
         save proxy IP $HTTP_X_FORWARDED_FOR
         HTTP_X_FORWARDED_FOR is where i am really connected
         $REMOTE_ADDR is proxy's proxy
      }
   }

   other ANONYMOUS

   if ($HTTP_X_FORWARDED_FOR)
   {
      if ($HTTP_VIA)
      {
         save $HTTP_VIA
         $HTTP_X_FORWARDED_FOR is empty         
         $REMOTE_ADDR is proxy's proxy
      }
   }

   HIGH ANONIMITY

   $REMOTE_ADDR = proxy connected to


   */

  $_real_ip = "";
  if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
     $_real_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else if (getenv('HTTP_X_FORWARDED_FOR'))
  {
     $_real_ip = getenv('HTTP_X_FORWARDED_FOR');
  }
  else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
     $_real_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
     $_real_ip = $_SERVER['REMOTE_ADDR'];
  }

return $_real_ip;
}
?>
