<?
  function InputText($FieldName, $FieldIniVal='', $MaxLength=0, $FieldSize=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla");
    $browser = substr("$type",8,3);

    $Size = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $FieldSize : 15 + ceil($FieldSize / 2);
    $Maxi = ($MaxLength) ? " maxlength=\"{$MaxLength}\"" : '';
    $Value = ($FieldIniVal) ? ' value="'.htmlentities($FieldIniVal).'"' : '';
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    echo  "<input type=\"text\" name=\"{$FieldName}\"{$Value} size=\"{$Size}\"{$Maxi}{$AddAtts}>";
  }

  function InputPassword($FieldName, $FieldIniVal='', $MaxLength=0, $FieldSize=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla");
    $browser = substr("$type",8,3);

    $Size = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $FieldSize : 15 + ceil($FieldSize / 2);
    $Maxi = ($MaxLength) ? " maxlength=\"{$MaxLength}\"" : '';
    $Value = ($FieldIniVal) ? ' value="'.htmlentities($FieldIniVal).'"' : '';
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    echo  "<input type=\"password\" name=\"{$FieldName}\"{$Value} size=\"{$Size}\"{$Maxi}{$AddAtts}>";
  }

  function InputFile($FieldName, $FieldIniVal='', $MaxLength=0, $FieldSize=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla");
    $browser = substr("$type",8,3);

    $Size = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $FieldSize : 15 + ceil($FieldSize / 2);
    $Maxi = ($MaxLength) ? " maxlength=\"{$MaxLength}\"" : '';
    $Value = ($FieldIniVal) ? ' value="'.htmlentities($FieldIniVal).'"' : '';
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    echo  "<input type=\"file\" name=\"{$FieldName}\"{$Value} size=\"{$Size}\"{$Maxi}{$AddAtts}>";
  }

  function TextArea($FieldName, $FieldIniVal='', $Cols=0, $Rows=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'],"Mozilla");
    $browser = substr("$type",8,3);

    $Cols = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $Cols : 15 + ceil($Cols / 2);
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    echo "<textarea wrap=\"VIRTUAL\" name=\"{$FieldName}\" cols=\"{$Cols}\" rows=\"{$Rows}\"{$AddAtts}>".htmlentities($FieldIniVal).'</textarea>';
  }

  function &InputTextRaw($FieldName, $FieldIniVal='', $MaxLength=0, $FieldSize=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla");
    $browser = substr("$type",8,3);

    $Size = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $FieldSize : 15 + ceil($FieldSize / 2);
    $Maxi = ($MaxLength) ? " maxlength=\"{$MaxLength}\"" : '';
    $Value = ($FieldIniVal) ? ' value="'.htmlentities($FieldIniVal).'"' : '';
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    return  "<input type=\"text\" name=\"{$FieldName}\"{$Value} size=\"{$Size}\"{$Maxi}{$AddAtts}>";
  }

  function &InputPasswordRaw($FieldName, $FieldIniVal='', $MaxLength=0, $FieldSize=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla");
    $browser = substr("$type",8,3);

    $Size = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $FieldSize : 15 + ceil($FieldSize / 2);
    $Maxi = ($MaxLength) ? " maxlength=\"{$MaxLength}\"" : '';
    $Value = ($FieldIniVal) ? ' value="'.htmlentities($FieldIniVal).'"' : '';
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    return "<input type=\"password\" name=\"{$FieldName}\"{$Value} size=\"{$Size}\"{$Maxi}{$AddAtts}>";
  }

  function &InputFileRaw($FieldName, $FieldIniVal='', $MaxLength=0, $FieldSize=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], "Mozilla");
    $browser = substr("$type",8,3);

    $Size = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $FieldSize : 15 + ceil($FieldSize / 2);
    $Maxi = ($MaxLength) ? " maxlength=\"{$MaxLength}\"" : '';
    $Value = ($FieldIniVal) ? ' value="'.htmlentities($FieldIniVal).'"' : '';
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    return "<input type=\"file\" name=\"{$FieldName}\"{$Value} size=\"{$Size}\"{$Maxi}{$AddAtts}>";
  }

  function &TextAreaRaw($FieldName, $FieldIniVal='', $Cols=0, $Rows=0){
    global $HTTP_SERVER_VARS;

    $type = strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'],"Mozilla");
    $browser = substr("$type",8,3);

    $Cols = (($browser == '5.0') or ($browser == '4.0')) ? 30 + $Cols : 15 + ceil($Cols / 2);
    $AddAtts = '';

    if(func_num_args() > 4){
      $CA = 4;
      while($CA < func_num_args()){
        $AddAtts .= ' '.func_get_arg($CA).'="'.func_get_arg($CA+1).'"';

        $CA += 2;
      }
    }

    return "<textarea wrap=\"VIRTUAL\" name=\"{$FieldName}\" cols=\"{$Cols}\" rows=\"{$Rows}\"{$AddAtts}>".htmlentities($FieldIniVal).'</textarea>';
  }

  function sendemail($to, $subject, $body, $from)
  {
  	$msg_body = stripslashes($body);

  	$mailheaders  = "From: $from\n";
  	$mailheaders .= "Reply-To: $from\n";
  	$mailheaders .= "MIME-version: 1.0\n";
  	$mailheaders .= "Content-type: multipart/mixed; ";

  	mail($to, stripslashes($subject), $msg_body, $mailheaders);
  }

  /* this function removes the given directory and all files and subdirectories in it */
  function remove_dir($path){
    if($Dir = dir($path)){
      $Path = $Dir->path;
      //echo "Entering Directory: $Path ...<br>";//
      while ($File = $Dir->read()){
        if ($File != "." && $File != ".."){
          if(is_dir($Path."/".$File))
            $removed = remove_dir($Path."/".$File);
          else{
            if(@unlink($Path."/".$File))
              $removed = true;
            else
              $removed = false;
          }

          if(!$removed)
            return false;
        }
      }

      $Dir->close();

      if(rmdir($path))
        return true;
      else
        return false;
    }
    else
      return false;
  }

  function &putslashes($str){
    if(!get_magic_quotes_gpc())
      $str = addslashes($str);

    return $str;
  }

  function &stripslashes_gpc($str){
    if(get_magic_quotes_gpc())
      $str = stripslashes($str);

    return $str;
  }

  function PutEscapeCharToStr($Str, $EscChr = '\\'){
    $NumArgs = func_num_args();
    $Args = func_get_args();

    for($X=2;$X<$NumArgs;$X++)
      $Str = str_replace($Args[$X], "{$EscChr}{$Args[$X]}", $Str);

    return $Str;
  }

  function CenterStr($Str, $Length=0){
    if(!$Length)
      return $Str;

    $LeftMargin = floor(($Length - strlen($Str)) / 2);
    $RightMargin = $LeftMargin + (($Length - strlen($Str)) % $LeftMargin);

    return eval("return sprintf('%{$LeftMargin}s%s%{$RightMargin}s', ' ', '{$Str}', ' ');");
  }

  function &MySQLTimestamp($MysqlTime){
    $TimeArray = array();

    $TimeArray['year'] = substr($MysqlTime, 0, 4);
    $TimeArray['month'] = intval(substr($MysqlTime, 4, 2));
    $TimeArray['day'] = intval(substr($MysqlTime, 6, 2));
    $TimeArray['hour'] = intval(substr($MysqlTime, 8, 2));
    $TimeArray['minute'] = intval(substr($MysqlTime, 10, 2));
    $TimeArray['second'] = intval(substr($MysqlTime, 12, 2));

    return $TimeArray;
  }

  function NotEmpty($Var){
    return !empty($Var);
  }

  function SetKeywords($Str, $VarArr, $Indexes){
    $NewArr = array();
    $IndexArr = explode(',', $Indexes);

    while(list($IKey, $IVal) = each($IndexArr))
      $NewArr['{'.$IVal.'}'] = $VarArr[$IVal];

    return strtr($Str, $NewArr);
  }
?>