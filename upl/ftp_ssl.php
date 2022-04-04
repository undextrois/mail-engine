<?
if (ini_get("safe_mode"))
{
   print "Safe Mode ON";
}
/*
  $_con_id = ftp_ssl_connect("localhost",990);
  if (!$_con_id) die ("connection error!");
  $r = ftp_login($_con_id,"root","rootpass");
  if (!$r)
  {
     print "Unable to connect";
  }
  else
  {
     print "Success!";
  }
  ftp_close ($_con_id);
  */
?>
