<?
#############################################################################
# Download/Upload script v1.0
#
# Oct 27, 2004
# Altomeyer LLC
#############################################################################

# if (!defined('__GN_U_MODULE_TRAP__')
#   || (__GN_U_MODULE_TRAP__ != 'GN-F4DCC0DD0B42F6B4C1565E59C92E308F-DVT'))
# {
#    die ("don't make me kick your arse!");
# }

include_once '_HW0NP12/class.inc.php';
include_once '_HW0NP12/template.inc';

$_k0 = "ABCDEFGHIJKMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789";

$_mysql_host = "localhost";
$_mysql_db = "_mailuser";
$_mysql_user = "ukpauser";
$_mysql_pass = "ukpauser";

$_debug_opt = true;
$_accept = false;

$_redirect_url = "main.php";

function _safe_croak($ln,$ms)
{
  print "croak [$ln]: $ms\n";
}

function _safe_debug($ln,$ms)
{
  global $_debug_opt;
  if ($_debug_opt) print "dbg [$ln]: $ms<br>\n";
}

function _safe_redirect($_url)
{
  header("location: $_url");
exit;
}

$_upl_user = "admin";
$_private_node = "/var/www/_UKML/pub";
$_upload_max_sz = (2 * (100000));        # 2 mb only?
$_folder_depth = 4;
$_folder_prefix = "_UKM.";
$_folder_mask = 0700;

_force_download($_upl_user,"text.txt");

/*
$_folder = strtoupper(dechex(crc32($_upl_user)));
$_folder = "_UKM$_folder.";
$_folder.= date("mdy");
# mkdir ("$_private_node/$_folder",$_folder_mask);

$f = fopen("$_private_node/$_folder/text.txt","w+t");

fputs($f,"Testing");
fclose ($f);

print "Yeah!";
*/

# _safe_debug(__LINE__,_create_node($_upl_user));

function _upload_file($_prm_user,$_prm_file)
{
  global $_private_node;

  if (($_mysql_con = @mysql_connect("localhost","ukpauser","ukpauser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }
  if (($_msyql_link = @mysql_select_db("_mailuser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }

  if (($_rs0 = mysql_query("SELECT f.folderName FROM eFolder_Tbl AS f,
                            eLoginInfo_Tbl AS i
                            WHERE f.fowner_FK = i.userid_PK
                            AND i.user = '".mysql_escape_string($_prm_user)."'"))==false)
  {
     _safe_croak(__LINE__,mysql_error());
     mysql_close ($_mysql_con);
  exit;
  }
  if (mysql_num_rows($_rs0))
  {
     $_rw0 = mysql_fetch_object($_rs0);

     if (($f = fopen("$_private_node/$_rw0->folderName/$_prm_file","w+b"))==false)
     {
        _safe_croak(__LINE__,"unable to open file!");
        mysql_close ($_mysql_con);
     exit;
     }

     $l = fread($f,24);
     $d = _encrypt($l,$_k0);
     $c = strrev(str_rot13(base64_encode($d)));
     fputs($f,"$c\n");

     mysql_free_result($_rs0);

     fclose ($f);
  }
  mysql_close ($_mysql_con);
exit;

}

function _force_download($_prm_user,$_prm_file)
{
  global $_private_node;

  if (($_mysql_con = @mysql_connect("localhost","ukpauser","ukpauser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }
  if (($_msyql_link = @mysql_select_db("_mailuser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }

  if (($_rs0 = mysql_query("SELECT f.folderName FROM eFolder_Tbl AS f,
                            eLoginInfo_Tbl AS i
                            WHERE f.fowner_FK = i.userid_PK
                            AND i.user = '".mysql_escape_string($_prm_user)."'"))==false)
  {
     _safe_croak(__LINE__,mysql_error());
     mysql_close ($_mysql_con);
  exit;
  }

  if (mysql_num_rows($_rs0))
  {
     $_rw0 = mysql_fetch_object($_rs0);

     if (($f = fopen("$_private_node/$_rw0->folderName/$_prm_file","r+b"))==false)
     {
        _safe_croak(__LINE__,"unable to open file!");
        mysql_close ($_mysql_con);
     exit;
     }

     header("Content-type: application/force-download");
     header("Content-Disposition: attachment; filename=\"$_prm_file\"");
     header("Content-Transfer-Encoding: Binary");
     header("Cache-Control: private");
     header("Content-length: ".filesize("$_private_node/$_rw0->folderName/$_prm_file"));

     while (!feof($f))
     {
       $l = fgets($f,1024);
       $c = base64_decode(str_rot13(strrev($l)));
       $d = _decrypt($c,$_k0);
       print $d;
     }

     mysql_free_result($_rs0);

     fclose ($f);
  }
  mysql_close ($_mysql_con);
exit;
}

function _create_node($_prm_user)
{
  global $_private_node;

  if (($_mysql_con = @mysql_connect("localhost","ukpauser","ukpauser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }
  if (($_msyql_link = @mysql_select_db("_mailuser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }

  $_folder = strtoupper(dechex(crc32($_prm_user)));
  $_folder = "_UKM$_folder.";
  $_folder.= date("mdy");

  if (mkdir ("$_private_node/$_folder",0700)==false)
  {

  }

  /*
  $_ftp_con = ftp_ssl_connect("192.168.0.201");
  $_ftp_res = ftp_login($_ftp_con,"root","rootpass");
  if (ftp_mkdir($_ftp_con,"$_private_node/$_folder"))
  {
     # success
  }
  ftp_close ($_ftp_con);
  */

  if (($_rs0=mysql_query("SELECT f.folderName FROM eFolder_Tbl AS f,
                          eLoginInfo_Tbl as i 
                          WHERE f.fowner_FK = i.userid_PK
                          AND i.user = '".mysql_escape_string($_prm_user)."'
                          AND f.folderName = '".mysql_escape_string($_folder)."'"))==false)
  {
     _safe_croak(__LINE__,mysql_error());
     mysql_close ($_mysql_con);
  exit;
  }
  if (mysql_num_rows($_rs0))
  {
     _safe_debug(__LINE__,"Exists: $_rw0->folderName");
     $_rw0 = mysql_fetch_object($_rs0);
     $_folder = $_rw0->folderName;
     mysql_free_result ($_rs0);
  }
  else
  {
     _safe_debug(__LINE__,"Checking ID....");
     if (($_rs0=mysql_query("SELECT * FROM eLoginInfo_Tbl
                             WHERE user = '".mysql_escape_string($_prm_user)."'"))==false)
     {
        _safe_croak(__LINE__,mysql_error());
        mysql_close ($_mysql_con);
     exit;
     }
     if (mysql_num_rows($_rs0))
     {
        _safe_debug(__LINE__,"Creating Folder.....");
        $_rw0 = mysql_fetch_object($_rs0);
        $_upl_userid = $_rw0->userid_PK;
        mysql_free_result ($_rs0);
        if (($_rs0=mysql_query("INSERT INTO eFolder_Tbl
                                SET fowner_FK = $_upl_userid,
                                folderName = '".mysql_escape_string($_folder)."',
                                fCreateDate = NOW(),
                                fModifyDate = NOW()"))==false)
        {
           _safe_croak(__LINE__,mysql_error());
           mysql_close ($_mysql_con);
        exit;
        }
     }
     else
     {
       # something's wrong....
     }
  }
  mysql_close ($_mysql_con);
return $_folder;
}

function _create_folder($_prm_user,$_prm_folder)
{
  global $_folder_prefix;
  global $_folder_depth;

  if (($_mysql_con = @mysql_connect("localhost","ukpauser","ukpauser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }
  if (($_msyql_link = @mysql_select_db("_mailuser"))==false)
  {
     _safe_croak(__LINE__,"unable to connect to server!");
  exit;
  }
  if (($_rs0=mysql_query("SELECT f.* FROM eFolderEx_Tbl AS f,
                          eLoginInfo_Tbl AS i
                          WHERE f.fowner_FK = i.userid_PK
                          AND i.user = '".mysql_escape_string($_prm_user)."'
                          AND f.folderName = '$_folder_prefix".mysql_escape_string($_prm_folder)."'"))==false)
  {
     _safe_croak(__LINE__,mysql_error());
     mysql_close ($_mysql_con);
  exit;
  }
  if (mysql_num_rows($_rs0))
  {
     mysql_free_result($_rs0);
  }
  else   
  {
     if (($_rs0=mysql_query("SELECT i.* FROM eLoginInfo_Tbl AS i
                             WHERE i.user='".mysql_escape_string($_prm_user)."'"))==false)
     {
        _safe_croak(__LINE__,mysql_error());
        mysql_close ($_mysql_con);
     exit;
     }
     if (mysql_num_rows($_rs0))
     {
        $_rw0 = mysql_fetch_object($_rs0);
        $_upl_userid = $_rw0->userid_PK;
        mysql_free_result($_rs0);
        $_folderEx = strtoupper(dechex(crc32($_prm_folder)));
        $_folderEx = "$_folder_prefix$_folder";

        if (mysql_query("INSERT INTO eFolderEx_Tbl
                         SET folderName = '".mysql_escape_string($_folderEx)."',
                         fowner_FK = $_upl_userid,
                         fdepth = 1")==false)
        {
           _safe_croak(__LINE__,mysql_error());
           mysql_close ($_mysql_con);
        exit;
        }
     }
  }

  mysql_close ($_mysql_con);
}

function _encrypt($_s0,$_k0)
{
  $_k0l = strlen($_k0);
  $_s0l = strlen($_s0);

  $_k1 = "ABCDEFGHIJKMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  $_k1l = strlen($_k1);

  $_m0 = ($_k0l & 0xff);

  $_s1 = "";

  for ($_s0n = 0; $_s0n < $_s0l; $_s0n++)
  {
     $_c0 = 0;
     for ($_k0n = 0; $_k0n < $_k0l; $_k0n++)
     {
         $_c0 ^= ord($_s0{$_s0n}) ^ ord($_k0{$_k0n});
         $_c0 ^= $_m0 ^ 64;
         $_c0 ^= $_k0n ^ 3;
     }
     $_s1.= chr($_c0);
  }

  $_s1 = str_rot13($_s1);

  $_tmpS = "";

  for ($_n0; $_n0 < $_s0l; $_n0++)
  {
    $_tmpS.= sprintf("%02X",ord($_s1{$_n0}));
  }

return $_tmpS;
}

function _decrypt($_s0,$_k0)
{
  $_k0l = strlen($_k0);
  $_s0l = strlen($_s0);

  $_m0 = ($_k0l & 0xff);

  $_s1 = $_s0;
  $_tmpS = "";

  for ($_n0; $_n0 < $_s0l; $_n0+=2)
  {
    if ($_s1{$_n0} >= 'A' && $_s1{$_n0} <= 'F')
       $_d0 = (ord($_s1{$_n0}) - ord('A')) + 10;
    else $_d0 = (ord($_s1{$_n0}) - ord('0'));

    if ($_s1{$_n0+1} >= 'A' && $_s1{$_n0+1} <= 'F')
       $_d1 = ($_d0 << 4) | (ord($_s1{$_n0+1}) - ord('A')) + 10;
    else $_d1 = ($_d0 << 4) | (ord($_s1{$_n0+1}) - ord('0'));

    $_tmpS.= chr($_d1);
  }

  $_s1 = str_rot13($_tmpS);

  $_s0l = $_s0l / 2;

  for ($_s0n = 0; $_s0n < $_s0l; $_s0n++)
  {
     $_c0 = 0;
     for ($_k0n = 0; $_k0n < $_k0l; $_k0n++)
     {
         $_c0 ^= $_k0n ^ 3;
         $_c0 ^= $_m0 ^ 64;
         $_c0 ^= ord($_s1{$_s0n}) ^ ord($_k0{$_k0n});
     }
     $_s2.= chr($_c0);
     # print chr($_c0);
  }
return  $_s2;
}

function _decode_url_file($_prm_file)
{
return $_prm_file;
}

function _encode_url_file($_prm_file)
{
return $_prm_file;
}
?>
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.

$uploaddir = '/var/www/uploads/';
$uploadfile = $uploaddir . $_FILES['userfile']['name'];

print "<pre>";
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    print "File is valid, and was successfully uploaded. ";
    print "Here's some more debugging info:\n";
    print_r($_FILES);
} else {
    print "Possible file upload attack!  Here's some debugging info:\n";
    print_r($_FILES);
}
print "</pre>";
