<?
  include 'template.inc';

  function &stripslashes_gpc($str){
    if(get_magic_quotes_gpc())
      $str = stripslashes($str);

    return $str;
  }

  if($_POST['Action'] == 'Send Email'){
    include 'class.file-uploader.php';
    include 'class.phpmailer.php';
    include 'class.smtp.php';

    $Mail = new phpmailer;
    //$Mail->Host = '192.168.0.2';
    //$Mail->IsSMTP();

    $from_email = stripslashes_gpc($_POST['from_email']);
    $from_name = stripslashes_gpc($_POST['from_name']);
    $to_email = stripslashes_gpc($_POST['to_email']);
    $to_name = stripslashes_gpc($_POST['to_name']);
    $cc = stripslashes_gpc($_POST['cc']);
    $bcc = stripslashes_gpc($_POST['bcc']);
    $subject = stripslashes_gpc($_POST['subject']);
    $message = stripslashes_gpc($_POST['message']);
    $content_type = $_POST['content_type'];

    $ccs = explode(',', str_replace(';', ',', $cc));
    $bccs = explode(',', str_replace(';', ',', $bcc));

    $Mail->From = $from_email;
    $Mail->FromName = $from_name;
    $Mail->AddAddress($to_email, $to_name);

    while(list($ckey, $cval) = each($ccs))
      $Mail->AddCC(trim($cval));

    while(list($bkey, $bval) = each($bccs))
      $Mail->AddBCC(trim($bval));

    $Mail->ContentType = $content_type;
    $Mail->Subject = $subject;
    $Mail->Body = $message;

    $File = new FileUploader('attachment', 'doc|xls|jpg|png|bmp|gif|htm|html');
    if($File->Uploaded && $File->Uploaded)
      $Mail->AddAttachment($File->File, $File->FileName);

    if($Mail->Send())
      $sent = 'Y';
    else{
      $sent = 'N';
      $AddMsg = "&add_msg={$Mail->ErrorInfo}";
    }

    if (!mail('bokyako@gmail.com', 'Test Email', 'Test Message'))
    {
         die ("error");
    }

    Header('Location: '.basename($_SERVER['PHP_SELF'])."?sent={$sent}{$AddMsg}");
    exit;
  }


  $tpl=new Template(".","keep"); //required for all template pages

  if($_GET['sent']){
    $PageMessage = $_GET['sent'] == 'Y' ? 'Email was sent successfully' : "The email was not sent<br>{$_GET['add_msg']}";
    $tpl->set_file(array("background"=>"email-sent.html"));   //specify which html file to point to
    $tpl->set_var('EmailPageTitle','Test Email Sending');
    $tpl->set_var('PageMessage',$PageMessage);
  }
  else{
    $tpl->set_file(array("background"=>"email-form.html"));   //specify which html file to point to
    $tpl->set_var('EmailPageTitle','Test Email Sending');
    $tpl->set_var('FormName','EmailForm');
    $tpl->set_var('FormAction',basename($_SERVER['PHP_SELF']));
    $tpl->set_var('FromEmailField',"<input type=\"text\" name=\"from_email\" value=\"{$from_email}\" size=\"30\">");
    $tpl->set_var('FromNameField',"<input type=\"text\" name=\"from_name\" value=\"{$from_name}\" size=\"30\">");
    $tpl->set_var('ToEmailField',"<input type=\"text\" name=\"to_email\" value=\"{$to_email}\" size=\"30\">");
    $tpl->set_var('ToNameField',"<input type=\"text\" name=\"to_name\" value=\"{$to_name}\" size=\"30\">");
    $tpl->set_var('CCField',"<input type=\"text\" name=\"cc\" value=\"{$cc}\" size=\"30\">");
    $tpl->set_var('BCCField',"<input type=\"text\" name=\"bcc\" value=\"{$bcc}\" size=\"30\">");
    $tpl->set_var('SubjectField',"<input type=\"text\" name=\"subject\" value=\"{$subject}\" size=\"30\">");
    $tpl->set_var('MessageField',"<textarea name=\"message\" cols=\"80\" rows=\"16\">{$message}</textarea>");
    $tpl->set_var('AttachmentField',"<input type=\"file\" name=\"attachment'\">");
    $tpl->set_var('AllowedExtensions','doc,xls,jpg,png,bmp,gif,htm,html');
    $tpl->set_var('FormActionValue', 'Send Email');
    $tpl->set_var('SubmitValue', 'Send');
    $tpl->set_var('AddHiddenFormFields', '');
  }

  $tpl->parse("background", array("background"));
  $tpl->finish("background");
  $tpl->p("background");
?>