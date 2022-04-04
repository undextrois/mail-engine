<?
#############################################################################
# Module: folder.php
# Date: November 2004
#
# Altomeyer LLC
#
#############################################################################

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

/* $_ML_Session->ssn_idPK; */

$tpl=new Template(".","keep");
$tpl->set_file(array("folder_tmpl"=>"folder.html"));

$_php_action_script = $_SERVER['PHP_SELF'];

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

print "PUTA";

print "SELECT d.* FROM eDisk_Tbl AS d
                        WHERE d.downer_FK=$_ML_Session->ssn_idPK<br>";


if (($_rs0=mysql_query("SELECT d.* FROM eDisk_Tbl AS d
                        WHERE d.downer_FK=$_ML_Session->ssn_idPK"))==false)
{
   _safe_croak(__LINE__,mysql_error());
   mysql_close ($_mysql_con);
exit;   
}

$_dQuotaSZ = number_format(0);
$_dUsedSZ = number_format(0);
$_dUsedSZN = 0;
$_dQuotaSZN = 0;
$_dFreeSZ = number_format(0);

if (mysql_num_rows($_rs0))
{
   $_rw0 = mysql_fetch_object($_rs0);
   $_dQuotaSZ = number_format($_rw0->dQuotaSZ);
   $_dQuotaSZN = $_rw0->dQuotaSZ;
   mysql_free_result ($_rs0); 
}

print $_dQuotaSZ;

$main_contents = "";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']=='POST')
{
   if (isset($_POST['action']) && isset($_POST['id']))
   {
      $_fileid = intval($_POST['id']);
      if ($_POST['action'] == 1)
      {
           if (($_rs0=@mysql_query("SELECT MIN(fo.fileid_PK) AS fMin,MAX(fo.fileid_PK) AS fMax
                                    FROM eFile_Tbl AS fo, eFolder_Tbl AS fi
                                    WHERE fo.folderid_FK=fi.folderid_PK
                                    AND fi.fowner_FK = " . $_ML_Session->ssn_idPK))==false)
           {
             _safe_croak(__LINE__,mysql_error());
           exit;
           }

           if (!mysql_num_rows($_rs0))
           {
              @mysql_close ($_mysql_con);
              header("location: folder.php?e=FILE_NOT_FOUND");
           exit;
           }

           $_rw0 = @mysql_fetch_object($_rs0);
           if ($_fileid < $_rw0->fMin && $_fileid > $_rw0->fMax)
           {
              @mysql_close ($_mysql_con);
              header("location: folder.php?e=FILE_NOT_FOUND");
           exit;
           }
           mysql_free_result($_rs0);

           if (($_rs0=@mysql_query("SELECT fo.*,fi.folderName FROM eFile_Tbl AS fo, eFolder_Tbl AS fi
                                    WHERE fo.folderid_FK=fi.folderid_PK
                                    AND fo.fileid_PK = $_fileid
                                    AND fi.fowner_FK = " . $_ML_Session->ssn_idPK))==false)
           {
             _safe_croak(__LINE__,mysql_error());
           exit;
           }

           if (!mysql_num_rows($_rs0))
           {
              @mysql_close ($_mysql_con);
              header("location: folder.php?e=FILE_NOT_FOUND");
           exit;
           }

           if (mysql_num_rows($_rs0))
           {
              $_rw0 = mysql_fetch_object($_rs0);

              if (($f = @fopen($_ML_Config{'_private_node'}."/$_rw0->folderName/$_rw0->fNewName","r+b"))==false)
              {
                 _safe_croak(__LINE__,"download failed!");
                 mysql_close ($_mysql_con);
              exit;
              }

              header("Content-type: application/force-download");
              header("Content-Disposition: attachment; filename=\"$_rw0->fName\"");
              header("Content-Transfer-Encoding: Binary");
              header("Cache-Control: private");
              header("Content-length: $_rw0->fSize");

              while (!feof($f))
              {
                 $l = fgets($f,1024);
                 $c = base64_decode(str_rot13(strrev($l)));
                 $d = _decrypt($c,$_k0);
                 print $d;
              }

              mysql_free_result($_rs0);

              fclose ($f);
              @mysql_close ($_mysql_con);
              header("location: folder.php");
              exit;
           }
           else
           {           
              @mysql_close ($_mysql_con);
              header("location: folder.php?e=FILE_NOT_FOUND");
              exit;
           }
      }
      else if ($_POST['action'] == 2)
      {
           @mysql_close ($_mysql_con);
           header("location: folder.php");
      exit;
      }
   }
   else if (isset($_POST['action']) && isset($_POST['id']))
   {
      @mysql_close ($_mysql_con);
      header("location: folder.php");
   exit;
   }

}

if (($_rs0=@mysql_query("SELECT fo.* FROM eFile_Tbl AS fo, eFolder_Tbl AS fi
                        WHERE fo.folderid_FK=fi.folderid_PK
                        AND fi.fowner_FK = " . $_ML_Session->ssn_idPK))==false)
{
   _safe_croak(__LINE__,mysql_error());
exit;
}

if (mysql_num_rows($_rs0))
{
   while ($_rw0 = mysql_fetch_object($_rs0))
   {
       $_fName = stripslashes($_rw0->fName);
       $_fCreateDate = $_rw0->fCreateDate;

       $_fSize = number_format($_rw0->fSize);
       if ($_rw0->fSize < 1000) { $_fSize = number_format($_rw0->fSize)." B"; }
       else if ($_rw0->fSize < 1000000) { $_fSize = number_format(($_rw0->fSize) / 100) . " B"; }
       else if ($_rw0->fSize < 1000000000) { $_fSize = number_format(($_rw0->fSize) / 1000) . " KB"; }

       $_fType = "Application/Binary";

       if (preg_match("#\.(\w+)$#",$_fName,$_fMatch))
       {
          $_fSuffix = strtoupper($_fMatch[1]);

          if (($_rs1 = mysql_query("SELECT x.description FROM eFileTypes_Tbl AS x
                                    WHERE x.suffix = '".mysql_escape_string($_fSuffix)."' LIMIT 0,1"))==false)
          {
             _safe_croak(__LINE__,mysql_error());
             mysql_close ($_mysql_con);
          exit;
          }       

          if (mysql_num_rows($_rs1))
          {
             $_rw1 = mysql_fetch_object($_rs1);
             $_fType = $_rw1->description;
             mysql_free_result($_rs1);
          }
       }

$main_contents.= <<<MAIN_CONTENTS_EOF
                    <tr>
                      <td class="defafont_b" width="5" align="center"><input type="checkbox" name="flst[]" value="$_rw0->fileid_PK"></td>
                      <td class="defafont_b" width="40%"><b>$_fName</b></td>
                      <td class="defafont_b" width="10%" align="center"><b>$_fType</b></td>
                      <td class="defafont_b" width="20%" align="right"><b>$_fSize</b></td>
                      <td class="defafont_b" width="17%" align="center"><b>$_fCreateDate</b></td>
                      <td class="defafont_b" width="19%" align="center">
                      <a href="#" onclick="iconchg(1,$_rw0->fileid_PK)"><img border="0" src="images/download.jpg" alt="Download" width="16" height="16"></a>
                      <a href="#" onclick="iconchg(2,$_rw0->fileid_PK)"><img border="0" src="images/delete.gif" alt="Delete" width="16" height="16"></a>
                      </td>
                    </tr>
MAIN_CONTENTS_EOF;
   $_dUsedSZN += $_rw0->fSize;
   }
   mysql_free_result($_rs0);
}

@mysql_close ($_mysql_con);

if ($_dUsedSZN > $_dQuotaSZN) $_dFreeSZ = number_format(0);
else
{
   $_dFreeSZN = ($_dQuotaSZN - $_dUsedSZN);
   $_dFreeSZ = number_format($_dFreeSZN);
   if ($_dFreeSZN < 1000) { $_dFreeSZ = number_format($_dFreeSZN)." B"; }
   else if ($_dFreeSZN < 1000000) { $_dFreeSZ = number_format(($_dFreeSZN) / 100) . " B"; }
   else if ($_dFreeSZN < 1000000000) { $_dFreeSZ = number_format(($_dFreeSZN) / 1000) . " KB"; }
}

$_dUsedSZ = number_format($dUsedSZN);
if ($_dUsedSZN < 1000) { $_dUsedSZ = number_format($_dUsedSZN)." B"; }
else if ($_dUsedSZN < 1000000) { $_dUsedSZ = number_format(($_dUsedSZN) / 100) . " B"; }
else if ($_dUsedSZN < 1000000000) { $_dUsedSZ = number_format(($_dUsedSZN) / 1000) . " KB"; }

$_dQuotaSZ = number_format($dQuotaSZN);
if ($_dQuotaSZN < 1000) { $_dQuotaSZ = number_format($_dQuotaSZN)." B"; }
else if ($_dQuotaSZN < 1000000) { $_dQuotaSZ = number_format(($_dQuotaSZN) / 100) . " B"; }
else if ($_dQuotaSZN < 1000000000) { $_dQuotaSZ = number_format(($_dQuotaSZN) / 1000) . " KB"; }

$_top_main_contents = <<<MAIN_CONTENTS_EOF
                  <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                     <td class="defafont_b"><img border="0" src="images/openfolder.gif" width="38" height="39"></td>
                     <td class="defafont_b"><b><font size="5">My Folder</font></b></td>
                  </tr>
                  <tr>
                     <td class="defafont_b" colspan="2"><br></td>
                  </tr>
                  <tr>
                     <td class="defafont_b" colspan="2">Disk quota size: $_dQuotaSZ</td>
                  </tr>
                  <tr>
                     <td class="defafont_b" colspan="2">Used space:  $_dUsedSZ</td>
                  </tr>
                  <tr>
                     <td class="defafont_b" colspan="2">Available space:  $_dFreeSZ</td>
                  </tr>
                  </table>
                  <form action="$_php_action_script" method="POST" name="fminbx3">
                  <table border="1" width="100%" cellspacing="0" cellpadding="3" bordercolor="#000000" bordercolorlight="#FFFFFF" bordercolordark="#000000">
                  <tr>
                     <td class="defafont_b" width="17" align="center" bgcolor="#CCCCCC">&nbsp;</td>
                     <td class="defafont_b" align="center" bgcolor="#CCCCCC"><b><img border="0" src="images/asc_order.gif" width="7" height="7">&nbsp; Filename</b></td>
                     <td class="defafont_b" align="center" bgcolor="#CCCCCC"><b><img border="0" src="images/asc_order.gif" width="7" height="7">&nbsp; Type</b></td>
                     <td class="defafont_b" align="center" bgcolor="#CCCCCC"><b><img border="0" src="images/asc_order.gif" width="7" height="7">&nbsp; Size</b></td>
                     <td class="defafont_b" align="center" bgcolor="#CCCCCC"><b><img border="0" src="images/asc_order.gif" width="7" height="7">&nbsp; Date Modified</b></td>
                     <td class="defafont_b" align="center" bgcolor="#CCCCCC"><b>Cmd</b></td>
                  </tr>
MAIN_CONTENTS_EOF;

$main_contents.= <<<MAIN_CONTENTS_EOF
                  <tr>
                     <td class="defafont_b" align="center" colspan="6">
                         <p align="left">&nbsp;<img border="0" src="images/parent.gif" width="20" height="16">Group Options: <input type="button" value="Delete" name="delete" class="ukbots" onclick="iconchg(2)">
                           <input type="button" value="Move" name="mvfolder" class="ukbots">
                           <input type="button" value="Rename" name="markread" class="ukbots" onclick="iconchg(6, 1)">
                     </td>
                  </tr>
                  </table>
                  <input type="hidden" name="action" value="">
                  <input type="hidden" name="id" value="">
                  </form>
MAIN_CONTENTS_EOF;

$tpl->set_var(
  array(
    'page_title'          =>  'Today is '.date('jS of F'),
    'date_today'          =>  'Today is '.date('jS of F, Y'),
    'main_contents'       =>  $_top_main_contents.$main_contents
  )
);
$tpl->parse('folder_tmpl', array('folder_tmpl'));
$tpl->p("folder_tmpl");
?>
