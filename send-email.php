<?
  function send_email($message_id, $to, $subject, $message, $content_type='text/plain', $from='', $cc='', $bcc=''){
    global $db;
    require 'lib/class.phpmailer.php';

    $Mail = new phpmailer;
    $Mail->ContentType = $content_type;
    $Mail->Subject = $subject;
    $Mail->Body = $message;
    $Mail->From = $from;
    $Mail->FromName = '';
    $Mail->AddReplyTo($from);

    $Mail->ClearAddresses();
    $tos = explode(';', $to);
    while(list($k, $v) = each($tos))
      $Mail->AddAddress(trim($v));

    $Mail->ClearCCs();
    $ccs = explode(';', $cc);
    while(list($k, $v) = each($ccs))
      $Mail->AddCC(trim($v));

    $Mail->ClearBCCs();
    $bccs = explode(';', $bcc);
    while(list($k, $v) = each($bccs))
      $Mail->AddBCC(trim($v));

    $Mail->ClearAttachments();
    $query = "SELECT attachment_id_PK, filename FROM attachments WHERE message_id_FK='{$message_id}'";
    $atts = $db->opendb($query);

    foreach($atts AS $att)
      $Mail->AddAttachment("attachments/{$att['attachment_id_PK']}", $att['filename']);

    return $Mail->Send();
  }
?>