<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<title>Today is 20th of October</title>
<link rel="stylesheet" type="text/css" href="../main.css">
<script language="javascript">
<!--
  function iconchg(val, id) {
      document.vweml.action.value = val;
      document.vweml.id.value = id;
      document.vweml.submit();
  }
  function dwnfl(val) {
      document.attach.action.value = 'download';
      document.attach.file.value = val;
      document.attach.submit();
  }
//-->
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>

<body topmargin="0" leftmargin="0">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" class="menubg"><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50%" class="defafont_w"><table border="0" cellspacing="0" cellpadding="4">
              <tr>
                <td class="defafont_w"><b><img border="0" src="../images/openfolder.gif" width="27" height="22"></b></td>
                <td class="defafont_w"><b>ALTOMAILER V1.0</b></td>
              </tr>
          </table></td>
          <td width="50%" class="defafont_w"><p align="right"><b>You Are Now Online: Donniel C. Collera<br>
              
			  Today is November 3, 2004 </b></td>
        </tr>
        <tr>
          <td width="100%" class="defaltbg" colspan="2"><table border="0" width="100%">
              <tr>
                <td width="8%" class="defafont_b" valign="top"><table height="356" border="0" cellpadding="10" bordercolor="#FFFFFF" bordercolorlight="#FFFFFF" bordercolordark="#FFFFFF">
                    <tr>
                      <td width="100%" align="center" class="menubox"><p align="center"><a href="../main.php"><img border="0" src="../images/home.gif" width="18" height="18"></a><br>
                              <a href="http://" class="menulink">Home</a></td>
                    </tr>
                    <tr>
                      <td width="100%" align="center" class="menubox"><p align="center"><a href="../compose.php"><img border="0" src="../images/mail.gif" width="16" height="16"></a><br>
                              <a href="http://" class="menulink">Mail</a></td>
                    </tr>
                    <tr>
                      <td width="100%" align="center" class="menubox"><p align="center"><a href="http://"><img border="0" src="../images/explorer.gif" width="20" height="20"></a><br>
                              <a href="http://" class="menulink">File</a></td>
                    </tr>
                    <tr>
                      <td width="100%" align="center" class="menubox"><a href="../options.php"><img border="0" src="../images/tools.gif" width="16" height="16"></a><br>
                      <a href="http://" class="menulink"> Options</a></td>
                    </tr>
                    <tr>
                      <td width="100%" height="56" align="center" class="menubox"><a href="../contactlist.php"><img src="../images/contacts.gif" width="15" height="12" border="0"></a><br>
                      <a href="http://" class="menulink"> </a>Contacts</td>
                    </tr>
                    <tr>
                      <td height="56" align="center" class="menubox"><p align="center"><a href="../_ml_logout.php"><img border="0" src="../images/security.gif" width="15" height="13"> </a><a href="http://" class="menulink">Logout</a></p>
                      </td>
                    </tr>
                </table></td>
                <td width="92%" class="defafont_b" valign="top"><table border="0" cellspacing="0" cellpadding="3" width="100%">
                    <tr>
                      <td width="100%"><table border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="16" class="defafont_b"><img src="../images/contacts.gif" width="15" height="12"></td>
                            <td width="123" class="defafont_b"><b><font size="5">Contacts </font></b></td>
                          </tr>
                      </table></td>
                      <form method="POST" name="vweml">
                        <td width="100%"><div align="right">
                            <table border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td width="100%"><input type="button" value="Return To INBOX" name="B1" class="ukbots" onClick="location.href('inbox.php')"></td>
                              </tr>
                            </table>
                        </div></td>
                        <input type="hidden" name="action" value="">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="pop" value="<?echo $popid?>">
                        <input type="hidden" name="folder" value="<?echo $folderid?>">
                      </form>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">
					  <!-----to save new contact->
					  <!------------------------->
					  <form name="form1" method="post" action="SaveNewContact.php">
                        <table width="100%"  border="0">
                          <tr>
                            <td colspan="2"><div align="right" class="uktext">
                              <div align="center">{error_validation}</div>
                            </div>
						    </td>
                          </tr>
                            <tr>
								<td width="29%"> <div align="right" class="uktext">LastName</div></td>
								<td width="80%"><input name="txtlastname" type="text" class="ccInputBox" id="txtlastname" size="40"></td>
							  </tr>
                            <tr>
                            <td><div align="right"><span class="uktext">FirstName</span></div></td>
                            <td><input name="txtfirstname" type="text" class="ccInputBox" id="txtfirstname" size="25"></td>
                          </tr>
                          <tr>
                            <td><div align="right" class="uktext">
                              <div align="right" class="uktext">MiddleName </div>
                              </div></td>
                            <td><input name="txtmiddlename" type="text" class="ccInputBox" id="txtmiddlename" size="10"></td>
                          </tr>
                          <tr>
                            <td><div align="right" class="uktext">Company</div></td>
                            <td><input name="txtcompany" type="text" class="ccInputBox" id="txtcompany" size="55"></td>
                          </tr>
                          <tr>
                            <td class="uktext"><div align="right">Telephone No</div></td>
                            <td><input name="txttelno" type="text" class="ccInputBox" id="txttelno" size="15"></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                            <td><input name="cmdSave" type="submit" class="ukbots" id="cmdSave" value="Save"></td>
                          </tr>
                        </table>
                      </form></td>
                    </tr>
                    <tr>
                      <td width="100%" height="42" colspan="2" class="defafont_b">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="100%" class="defafont_b" colspan="2">&nbsp;                      </td>
                    </tr>
                </table></td>
              </tr>
          </table>
          </td>
        </tr>
        <tr>
          <td width="100%" class="defaltbg" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td width="100%" class="defaltbg" colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="3">
              <tr>
                <td width="100%" class="defafont_b"><p align="center"><br>
                    All rights reserved 2004</td>
              </tr>
          </table></td>
        </tr>
    </table></td>
  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>

</body>

</html>