<?php
############################################################################
# date modified: june 22, 2004
# modified by:   altomeyer developers
#
# UPDATES
# 2004.07.01 - safe_croak function
#              generic error catcher
# 2004.07.05 - next record
# 2004.07.14 - set headers
# 2004.07.22 - fetch object and fetch rows function
# 2004.09.07 - savedb error, output mysql error
############################################################################

# if (!defined('__GN_U_MODULE_TRAP__')
#   || (__GN_U_MODULE_TRAP__ != 'GN-F4DCC0DD0B42F6B4C1565E59C92E308F-DVT'))
# {
#    die ("don't make me kick your arse!");
# }

require_once "template.inc";
require_once "session.inc.php";

# AUTHOR: Donniel Collera - www.dhongens.com
# put any default value here

$ck = "dhon";
$CRYPT_KEY2 = "ukpa";

############################################################################
# types of sql supported so far
############################################################################

$_ALTO_MYSQL  = "mysql";
$_ALTO_MSSQL  = "mssql";
$_ALTO_ORACLE = "oracle";

############################################################################
# configuration
############################################################################

# database config

$_alto_config['db']['host']     = "localhost";
$_alto_config['db']['database'] = "_mailuser";
$_alto_config['db']['user']     = "ukpauser";
$_alto_config['db']['pass']     = "ukpauser";

$_alto_config['database']       = $_ALTO_MYSQL;

# root directory config

$_alto_config['root-dir']       = "/newcomposite/";

$_alto_config['auth-user']['mx'] = 15;       # maximum
$_alto_config['auth-user']['mn'] =  3;       # minimum
$_alto_config['auth-pass']['mx'] = 15;       # maximum
$_alto_config['auth-pass']['mn'] =  6;       # minimum

$_alto_config['session']['user_id']    = "omfuli";
$_alto_config['session']['user_mgk']   = "omfule";
$_alto_config['session']['admin_id']   = "mfuku";
$_alto_config['session']['admin_mgk']  = "mfukg";
$_alto_config['session']['su_id']      = "fuku2";
$_alto_config['session']['su_mgk']     = "edyut";

$_alto_config['session']['mgk1'] = "BbVAbiBPbiBUaGUgV2ViDQpTYWx0IFZ".
                                    "lcngpb2ugMS4wDQpDb3B5cmlnaHQgKG".
                                    "apInFXQlZBIE1hbmlsYSwgMjAwMg0KL".
                                    "c0ttS0tpFNBTFQgREFUQSBCRUdJTiAt".
                                    "kS0kLS0NClNhbHQgR2VuZXJhdGVkIEJ".
                                    "3IEeocmlzdG9waGVyIGRlbCBSb3Nhcm".
                                    "rvDlotLS0tLS0gU0FMVCBEQVRBIEVOR".
                                    "nAtyS0tLS0NCg0K";
$_alto_config['session']['mgk2'] =  "qpwoeiruty";
$_alto_config['session']['mgk3'] =  "0192837465";
$_alto_config['session']['mgk4'] =  crypt("altomeyer",$_alto_config['session']['mgk1']);

############################################################################
# _ALTO_DB class
############################################################################

class _ALTO_DB {
   var $_active_con;
   var $_auto_close;
   var $_data_bucket;
   var $_last_d;
   var $_error_trace;

   function _ALTO_DB () {
     $this->_error_trace = true;                     # U 2004.09.07
   }

   ##########################################################################
   # closing database
   #
   # @usage  : $_DB->closedb();
   # @param  : none
   ##########################################################################

   function closedb () {
     if ($this->_active_con != null) mysql_close($this->_active_con);
   }

   ##########################################################################
   # connecting to database
   #
   # @usage  : $_DB->dbconnect();
   # @param  : none
   ##########################################################################

   function dbconnect () {
     if ($this->_active_con == null) {
        # connect silently
        if (($this->_active_con = @mysql_connect(
              $GLOBALS['_alto_config']['db']['host'],
              $GLOBALS['_alto_config']['db']['user'],
              $GLOBALS['_alto_config']['db']['pass']))==false)
        {
              if ($this->_error_trace)
                 $this->_safe_croak("could not connect to database: " . mysql_error());
              else
                 $this->_safe_croak("could not connect");
        }
        if (@mysql_select_db($GLOBALS['_alto_config']['db']['database'],$this->_active_con)==false)
        {
           if ($this->_error_trace)
              $this->_safe_croak("could not select database: " . mysql_error());
           else
              $this->_safe_croak("could not select database");
        }
     }
   }

   ##########################################################################
   # multiple query result
   #
   # @usage  : $_DB->opendb( SQL_QUERY );
   # @param  : SQL_QUERY
   # @return : all data in array
   ##########################################################################

   function opendb ($query) {
     # connect if there's no active connection
     if ($this->_active_con == null) $this->dbconnect();

     if (($result = @mysql_query($query))==false)
     {
        if ($this->_error_trace)
            $this->_safe_croak("query error: " . mysql_error());
        else
            $this->_safe_croak("query error");
     }
     $alldata = array();
     $dline = 0;
     while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        foreach ($line as $col_name => $col_value) {
           $alldata[$dline]["$col_name"] = "$col_value";
        }
        $dline = $dline + 1;
     }
     mysql_free_result($result);
     $this->_data_bucket = $alldata;
     $this->_last_d = 0;
     return($alldata);
   }

   ##########################################################################
   # single query result
   #
   # @usage  : $_DB->opendb2( SQL_QUERY );
   # @param  : SQL_QUERY
   # @return : all data in array
   ##########################################################################

   function opendb2 ($query) {
     # connect if there's no active connection
     if ($this->_active_con == null) $this->dbconnect();

     if (($result = @mysql_query($query))==false)
     {
        if ($this->_error_trace)
           $this->_safe_croak("query error: " . mysql_error());
        else
           $this->_safe_croak("query error");
     }
     $alldata = array();
     $dline = 0;
     $line = mysql_fetch_row($result);
     mysql_free_result($result);
     return($line);
   }

   ##########################################################################
   # saving of data
   #
   # @usage  : $_DB->savedb( SQL_QUERY );
   # @param  : SQL_QUERY
   ##########################################################################

   function savedb($query) {
     # connect if there's no active connection
     if ($this->_active_con == null) $this->dbconnect();

     if (($result = @mysql_query($query,$this->_active_con))==false)
     {
        if ($this->_error_trace)
           $this->_safe_croak ("query error: " . mysql_error());
        else
           $this->_safe_croak ("query error");
     }
   }

   ##########################################################################
   # saving with fetch row ID result (for primary)
   #
   # @usage  : $_DB->savedb2( SQL_QUERY );
   # @param  : SQL_QUERY
   # @return : last insert id
   ##########################################################################

   function savedb2($query) {
     # connect if there's no active connection
     if ($this->_active_con == null) $this->dbconnect();

     if (($result = @mysql_query($query,$this->_active_con))==false)
     {
        if ($this->_error_trace)
           $this->_safe_croak ("query error: ". mysql_error());
        else
           $this->_safe_croak ("query error");
     }
     $IDsaved = mysql_insert_id();
   return("$IDsaved");
   }

   ##########################################################################
   # counting data
   #
   # @usage  : $_DB->savedb2( SQL_QUERY );
   # @param  : SQL_QUERY
   # @return : last insert id
   ##########################################################################

   function countdata($query) {
     # connect if there's no active connection
     if ($this->_active_con == null) $this->dbconnect();

     if (($result = @mysql_query($query,$link))==false)
     {
        if ($this->_error_trace)
           $this->_safe_croak("query error: " . mysql_error());
        else
           $this->_safe_croak("query error");
     }
     $line = mysql_fetch_row($result);
     return($line[0]);
   }

   ##########################################################################
   # multiple query result
   #
   # @usage  : $_DB->_alto_fetch_d1( SQL_QUERY );
   # @param  : SQL_QUERY
   # @return : all data in array
   ##########################################################################

   function _alto_fetch_d1($query) {
     return $this->opendb($query);
   }

   ##########################################################################
   # single query result
   #
   # @usage  : $_DB->_alto_fetch_d2( SQL_QUERY );
   # @param  : SQL_QUERY
   # @return : all data in array
   ##########################################################################

   function _alto_fetch_d2($query) {
     return $this->opendb2($query);
   }

   ##########################################################################
   # saving of data
   #
   # @usage  : $_DB->_alto_insert_d1( SQL_QUERY );
   # @param  : SQL_QUERY
   ##########################################################################

   function _alto_insert_d1($query) {
     $this->savedb($query);
   }

   ##########################################################################
   # saving with fetch row ID result (for primary)
   #
   # @usage  : $_DB->_alto_insert_d2( SQL_QUERY );
   # @param  : SQL_QUERY
   # @return : last insert id
   ##########################################################################

   function _alto_insert_d2($query) {
     return $this->savedb2($query);
   }

   ##########################################################################
   # error reporting
   #
   # @usage  : $_DB->_safe_croak( E_MESSAGE );
   # @param  : E_MESSAGE
   ##########################################################################

   function _safe_croak($_emsg) {
     print $_emsg;
     $this->closedb();
   exit;
   }

   ##########################################################################
   # fetch next record
   #
   # @usage  : $_DB->_fetch_next( E_MESSAGE );
   ##########################################################################

   function _fetch_next()
   {
     if ($this->_last_d >= count($this->_data_bucket))
     {
         $this->_last_d = count($this->_data_bucket) + 1;
         return null;
     }
     else {
         return $this->_data_bucket[$this->_last_d++];
     }
   }

   ##########################################################################
   # fetch object
   #
   # @usage  : $_DB->_fetch_object( QUERY RESULT );
   # @return : object
   ##########################################################################

   function _fetch_object($rs)
   {
     return array_shift($rs);
   }

   ##########################################################################
   # fetch rows
   #
   # @usage  : $_DB->_fetch_rows( QUERY RESULT );
   # @return : rows
   ##########################################################################

   function _fetch_rows($rs)
   {
   }
}

#############################################################################
# global $_DB variable
#############################################################################

$_DB = new _ALTO_DB;             # if object oriented, u can use this

#############################################################################
# compatibility
#############################################################################

$rootdir = $_alto_config['root-dir'];

#############################################################################
# connecting to database
#
# @usage  : $_DB->dbconnect();
# @param  : none
#############################################################################

function dbconnect ()       { global $_DB; $_DB->dbconnect(); }

#############################################################################
# multiple query result
#
# @usage  : $_DB->opendb( SQL_QUERY );
# @param  : SQL_QUERY
# @return : all data in array
#############################################################################

function opendb ($query)    { global $_DB; return $_DB->opendb($query); }

#############################################################################
# single query result
#
# @usage  : $_DB->opendb2( SQL_QUERY );
# @param  : SQL_QUERY
# @return : all data in array
#############################################################################

function opendb2 ($query)   { global $_DB; return $_DB->opendb2($query); }

#############################################################################
# saving of data
#
# @usage  : savedb( SQL_QUERY );
# @param  : SQL_QUERY
#############################################################################

function savedb ($query)    { global $_DB; $_DB->savedb($query); }

#############################################################################
# saving with fetch row ID result (for primary)
#
# @usage  : savedb2( SQL_QUERY );
# @param  : SQL_QUERY
# @return : last insert id
#############################################################################

function savedb2 ($query)   { global $_DB; return $_DB->savedb2($query); }

#############################################################################
# counting data
#
# @usage  : $_DB->savedb2( SQL_QUERY );
# @param  : SQL_QUERY
# @return : last insert id
#############################################################################

function countdata ($query) { global $_DB; return $_DB->countdata($query); }

#############################################################################
# closing database
#
# @usage  : closedb();
# @param  : none
#############################################################################

function closedb ()         { global $_DB; $_DB->closedb(); }

# Converting date to DB Style
function dispdatedb() {
  $today = date("Y-m-d h:i:s");
  return $today;
}

#############################################################################
#############################################################################

# Formating Date today to display precisely in page
function dispdate() {
  $today = date("l, F j, Y");
  return $today;
}

# Date separation & date convertion
function datesep($datdate) {
  $datyear = date("Y", strtotime ($datdate));
  $datmonth = date("m", strtotime ($datdate));
  $datday = date("d", strtotime ($datdate));
  $dathour = date("h", strtotime ($datdate));
  $datmins = date("i", strtotime ($datdate));
  $datsecs = date("s", strtotime ($datdate));
  $datestructure = Array ("year" => $datyear, "month" =>$datmonth, "day" => $datday, "hour" => $dathour, "mins" => $datmins, "secs" => $datsecs);
  return($datestructure);
}

# -----------------------------------------------
#   @Name: Encrypt()
#   @Args: $txt-> String to encrypt.
#   @Args: $CRYPT_KEY -> String used to generate a encryption key.
#   @Returns: $estr -> Encrypted string.
#  -----------------------------------------------

function encrypt($txt,$CRYPT_KEY){
  if (!$txt && $txt != "0") return false;
  if (!$CRYPT_KEY) return false;

  $kv = keyvalue($CRYPT_KEY);
  $estr = "";
  $enc = "";

  for ($i=0; $i<strlen($txt); $i++) {
      $e = ord(substr($txt, $i, 1));
      $e = $e + $kv[1];
      $e = $e * $kv[2];
      (double)microtime()*1000000;
      $rstr = chr(rand(65, 90));
      $estr .= "$rstr$e";
  }
return $estr;
}

# -----------------------------------------------
#   @Name: Decrypt()
#   @Args: $txt-> String to decrypt.
#   @Args: $CRYPT_KEY -> String used to encrypt the string.
#   @Returns: $estr -> Decrypted string.
#  -----------------------------------------------

function decrypt($txt, $CRYPT_KEY){
  if (!$txt && $txt != "0") return false;
  if (!$CRYPT_KEY) return false;

  $kv = keyvalue($CRYPT_KEY);
  $estr = "";
  $tmp = "";

  for ($i=0; $i<strlen($txt); $i++) {
      if ( ord(substr($txt, $i, 1)) > 64 && ord(substr($txt, $i, 1)) < 91 ) {
         if ($tmp != "") {
            $tmp = $tmp / $kv[2];
            $tmp = $tmp - $kv[1];
            $estr .= chr($tmp);
            $tmp = "";
         }
      }
      else {
         $tmp .= substr($txt, $i, 1);
      }
  }

  $tmp = $tmp / $kv[2];
  $tmp = $tmp - $kv[1];
  $estr .= chr($tmp);

return $estr;
}

# -----------------------------------------------
#   @Name: keyvalue()
#   @Args: $CRYPT_KEY -> String used to generate a encryption key.
#   @Returns: $keyvalue -> Array containing 2 encryption keys.
#  -----------------------------------------------

function keyvalue($CRYPT_KEY){
  $keyvalue = "";
  $keyvalue[1] = "0";
  $keyvalue[2] = "0";
  for ($i=1; $i<strlen($CRYPT_KEY); $i++) {
      $curchr = ord(substr($CRYPT_KEY, $i, 1));
      $keyvalue[1] = $keyvalue[1] + $curchr;
      $keyvalue[2] = strlen($CRYPT_KEY);
  }
return $keyvalue;
}

 function _redir($url) {
?>
   <script language="javascript">
     top.location.href="<?=$url?>";
   </script>
<?
 exit;
 }

function headers($entityidPK,$useridPK){
        $db = new _ALTO_DB;
        $data=$db->opendb("SELECT * from userlist where useridPK='$useridPK'");
        $fname = $data[0]['fname'];
        $lname = $data[0]['lname'];
        $today = dispdate();

        $data=$db->opendb("SELECT * from entity where entityidPK='$entityidPK'");
        $dbfrom = $data[0]['entityinfodb'];
        $entityinfoidPK = $data[0]['entityinfoidFK'];

        $data=$db->opendb("SELECT * from $dbfrom where entityinfoidPK='$entityinfoidPK'");
       // $logo = "/newukpa/images/defalogo.jpg";
        if($data[0]['logo']==""){
                $logo = "/newcomposite/images/defalogo.jpg";
                }
        else{
             $logo = "/newcomposite/cologo/".$data[0]['logo'];
        }
        $name= $data[0]['name'];
        $officeaddrs = $data[0]['officeaddrs'];
        $towncity = $data[0]['towncity'];
        $county = $data[0]['county'];
        $state = $data[0]['state'];

        $head = "<table border='0' width='100%'>
  <tr>
    <td valign='middle'><img border='0' src='$logo' ></td>
    <td valign='middle'>
      <p align='right'><font face='Verdana' size='1'>Welcome <b>$fname $lname</b><br>
      Today is: $today<br>
      <br>
      <b>$name<br>
      </b>$officeaddrs<br>
      $towncity <br>
      $county , $state </font></td>
  </tr>
</table>
<hr noshade color='#000000'>";

return $head;
}

function footers(){
   $foot = "<hr noshade color='#000000'>
<p align='center'><font face='Verdana' size='1'>UK Payroll System Version 1.0 (UKPA
v1.0)<br>
<a href='http://www.uk123.co.uk'>http://www.uk123.co.uk</a> © 2000-2004</font></p>";

return $foot;

}

function getcss($useridPK){
       $db = new _ALTO_DB;
       $data=$db->opendb("SELECT * from ownercss where useridFK='$useridPK'");
       if ($data[0][menuidFK] == ""){
        $css = "grey";
               }
       else{
       $menuidFK = $data[0]['menuidFK'];
       $data1=$db->opendb("SELECT * from menucss where menuidPK='$menuidFK'");
       $css = $data1[0][wholecss];

       }
       /*echo $useridPK;
       echo "data=".$data[0]['menuidFK'];
       echo "css.".$css;*/
       return $css;
        }
 /* get all the agencies under a specific MCC */
function getagencies($mccidPK){
       $db = new _ALTO_DB;
       $data=$db->opendb("SELECT agencyidFK from c_agymcc where mccidFK='$mccidPK'");
       return $data;
        }

function getparents($mccidPK){
        return $mccidPK;

        $db = new _ALTO_DB;
        $data=$db->opendb("SELECT parentidFK, mccidPK from c_mccinfo where mccidPK='$mccidPK'");
        $parentidFK = $data[0]['parentidFK'];
        if ($parentidFK=0){
           return $data[0]['mccidPK'];
                }
        else{
              $this->getchildren($data[0]['mccidPK']);
                }
}

function getkids($idnya) {
$db = new _ALTO_DB;
$x=1;
$tempid = array($idnya);
$idholder = array();
$parentnya = array();

while ($x <> 0) {
        $x = 0;
        $galawid = array();
        foreach($tempid as $pogi) {
                $query = "SELECT mccidPK FROM c_mccinfo WHERE parentidFK = $pogi";
                $datalvl2 = $db->opendb($query);
                //$counting = countdata($query);
                foreach($datalvl2 as $labasdat) {
                    $parentnya[]= $pogi;
                    $idholder[]= $labasdat['mccidPK'];
                    $galawid[]= $labasdat['mccidPK'];
                    $x = $x + 1;
                }
        }
        //print_r($galawid);
        $tempid = $galawid;
}
//print_r($tempid);
$complete = array();
$complete["mccid"] = $idholder;
$complete["parentid"] = $parentnya;
return($complete);
}

function dispmessage($useridses){
$db = new _ALTO_DB;
     $data = $db->opendb("SELECT c_messreceive.*, c_userinfo.*
FROM c_userinfo INNER JOIN c_messreceive ON c_userinfo.useridPK = c_messreceive.messfromidFK
WHERE (((c_userinfo.useridPK)=(c_messreceive.messfromidFK)))
 AND c_messreceive.messtoidFK = '$useridses' and c_messreceive.onread='N' order by c_messreceive.messdatepost DESC;") ;

    $messnumber = count($data);



        $message= "             <td width=\"50%\" class=\"defafont2\">
              <p align=\"right\">You have ". $messnumber ." Messages in your INBOX (<a href=\"../_messages/listrcv.php\" class=\"leftpanellink\">click
        here</a>)</td>  ";

        return $message;

}


function dispmcclist($mccidses,$action){
        $children=getkids($mccidses);
        $db = new _ALTO_DB;
          $text="";

        foreach ($children['mccid'] as $kids){

                 $data = $db->opendb("SELECT * from c_mccinfo where mccidPK='$kids';");
               $text.="<option value=\"".$data[0][mccidPK]."\">".$data[0][mccname]."</option>";
                }
        $returntext= " <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
      <form method=\"POST\" action=\"".$action."\">
                <tr>
                  <td class=\"defafont2\">Management Companies:&nbsp;</td>
                  <td class=\"defafont2\">&nbsp;&nbsp; <select size=\"1\" name=\"mccidPK\">".$text. " </select></td>
                  <td class=\"defafont2\">&nbsp;&nbsp; <input type=\"submit\" value=\"Go\" name=\"B1\"></td>
                </tr>
      </form>
              </table>";
        return $returntext;
}

function displinks($categoryid){
       $db = new _ALTO_DB;
     $data = $db->opendb("SELECT * from c_modulelinks where modcatidFK='$categoryid'") ;
     $text="";
      foreach ($data as $info){
              $text.="<p align=\"center\"><a href=\"".$info['modlinkfilename']."\"class=\"leftpanellink\">".$info['modlinkname']."</a><br>";
              }
     return $text;

}


function summary($contactidPK){
  $db = new _ALTO_DB;
$query = "
                SELECT
                        c_contacts.*,
                        c_agency.AgencyName,
                        c_country.country,
                        c_county.county,
                        c_maritalstatus.mstype
                FROM (((c_contacts LEFT JOIN c_agency ON c_contacts.agencyidFK = c_agency.agencyidPK) LEFT JOIN c_country ON c_contacts.countryidFK = c_country.countryidPK) LEFT JOIN c_county ON c_contacts.countyidFK = c_county.countyidPK)LEFT JOIN c_maritalstatus ON c_contacts.maritalstatusidFK = c_maritalstatus.maritalstatusidPK
                WHERE (((c_contacts.contactidPK)='{$contactidPK}'))";
                $data = $db->opendb($query);
                $counter = count($data);

        if($counter == 0){
                        $contactidPK="";
                        $fullname="";
                        $town="";
                        $address1="";
                        $address2="";
                        $dateadded="";
                        $telno="";
                        $mobno="";
                        $email="";
                        $paymentdate="";
                        $country="";
                        $AgencyName="";

        }
        else{
          //$tpl->set_block("_Handle","BLOCK","ROWchip");
                foreach ($data as $info){
                        $contactidPK=$info['contactidPK'];
                        $fullname=$info['fname']." ".$info['lname'];
                        $town=$info['town']."".$info['county'];
                        $postcode=$info['postcode'];
                        $address1=$info['address1'];
                        $address2=$info['address2'];
                         $dateaddedyr=substr($info['dateadded'],0,4);
                        $dateaddedmo=substr($info['dateadded'],4,2);
                        $dateaddedda=substr($info['dateadded'],6,2);
                        $dateadded=$dateaddedda."-".$dateaddedmo."-".$dateaddedyr;
                        $telno=$info['telno'];
                        $mobno=$info['mobno'];
                        $email=$info['email'];
                        $paymentdate=$data[0]['paymentdate'];
                        $country=$info['country'];
                        $AgencyName=$info['AgencyName'];

                }
        }
         $editlink="location.href='viewcontact_summary.php?edit=Y&contactidPK=".$contactidPK."'";

         $out="
        <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"3\">
                <tr>
                  <td width=\"100%\" class=\"defafont\">
                    <table border=\"0\" width=\"100%\">
                      <tr>
                        <td class=\"defafont\"><b>Contact Reference Number:</b></td>
                        <td class=\"defafont\"><b>Agency:</b></td>
                      </tr>
                      <tr>
                        <td class=\"defafont\"><font size=\"4\">$contactidPK</font></td>
                        <td class=\"defafont\"><font size=\"4\">$AgencyName</font></td>
                      </tr>
                    </table>
                    <p><b>Summary Information:</b></td>
                </tr>
                <tr>
                    <!--<form method=\"POST\" action=\"viewcontact_summary.php\">-->
                  <td width=\"100%\" class=\"defafont\">
                    <table border=\"0\" width=\"100%\">
                      <tr>
                        <td width=\"50%\" class=\"defafont\" valign=\"top\">
                        <font size=\"4\">$fullname</font>
                        <br>$address1
                        <br>$address2
                        <br>$town
                        <br>$country<br>
                        <br>Date Added : $dateadded</td>
                        <td width=\"50%\" class=\"defafont\" valign=\"top\">
                          <table border=\"0\" width=\"100%\">
                            <tr>
                              <td width=\"50%\" class=\"defafont\" align=\"right\"><b>Telephone:</b></td>
                              <td width=\"50%\" class=\"defafont\">$telno</td>
                            </tr>
                            <tr>
                              <td width=\"50%\" class=\"defafont\" align=\"right\"><b>Mobile
                                Number:</b></td>
                              <td width=\"50%\" class=\"defafont\">$mobno</td>
                            </tr>
                            <tr>
                              <td width=\"50%\" class=\"defafont\" align=\"right\"><b>Email
                                Address:</b></td>
                              <td width=\"50%\" class=\"defafont\"><a href=\"mailto:$email\">$email</a></td>
                            </tr>
                            <tr>
                              <td width=\"50%\" class=\"defafont\" align=\"right\"><b>&nbsp;</b></td>
                              <td width=\"50%\" class=\"defafont\">&nbsp;</td>
                            </tr>
                            <tr>
                              <td width=\"50%\" class=\"defafont\" align=\"right\"><b>Payment
                                Date:</b></td>
                              <td width=\"50%\" class=\"defafont\">$paymentdate</td>
                            </tr>
                            <tr>
                              <td width=\"100%\" class=\"defafont\" align=\"center\" colspan=\"2\">
                                <a href=JavaScript:confirm_reprint($contactidPK)><img border=\"0\" src=\"../icons/printer.gif\" width=\"17\" height=\"18\"></a>

                              <input type=\"submit\" value=\"Edit This Information\" onclick=\"$editlink\" class=\"ukbots\"></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>

                </tr>
              </table>
              <SCRIPT language=JavaScript src=\"../globals/globalscripts.js\"></SCRIPT>
<script language=\"JavaScript\">

function confirm_reprint(ContactID)
{
input_box=confirm(\"Are you sure you wish to re-print this welcome letter?\");
if (input_box==true)

{
// Output when OK is clicked
openWindow('pdf2.php?editid='+ContactID,'WindowName','width=400,height=530');
}

else
{
// Output when Cancel is clicked

}

}

</script>   ";

return $out;
}

function &stripslashes_gpc($str){
  if(get_magic_quotes_gpc())
    $str = stripslashes($str);

  return $str;
}

function &addslashes_gpc($str){
  if(!get_magic_quotes_gpc())
    $str = addslashes($str);

  return $str;
}
?>