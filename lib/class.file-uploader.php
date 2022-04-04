<?
  class FileUploader {
    var $File               = '';
    var $FileName           = '';
    var $FileType           = '';
    var $FileSize           = 0;
    var $FileExt            = '';
    var $AllowedExts        = 'none';
    var $NotAllowedExts        = 'none';
    var $Allowed            = true;
    var $Uploaded           = false;
    var $FilesVar           = '_FILES';

    function FileUploader($FieldName = 'file', $Exts = '', $NExts=''){
      global ${$this->FilesVar};

      if(is_uploaded_file(${$this->FilesVar}[$FieldName]['tmp_name'])){
        if($Exts){
          $this->AllowedExts = explode("|", strtolower($Exts));
          $this->AllowedExts = array_unique($this->AllowedExts);
        }

        if(!empty($NExts)){
          $this->NotAllowedExts = explode('|', strtolower($NExts));
          $this->NotAllowedExts = array_unique($this->NotAllowedExts);
        }

        $this->File = ${$this->FilesVar}[$FieldName]['tmp_name'];
        $this->FileName = ${$this->FilesVar}[$FieldName]['name'];
        $this->FileType = ${$this->FilesVar}[$FieldName]['type'];
        $this->FileSize = ${$this->FilesVar}[$FieldName]['size'];
        $this->FileExt = strtolower(substr($this->FileName, strrpos($this->FileName, ".") + 1));

        if($this->NotAllowedExts != 'none'){
          $this->Allowed = in_array($this->FileExt, $this->NotAllowedExts) ? false : true;
        }

        $this->Allowed = $this->Allowed ? ($this->AllowedExts != 'none' ? ((in_array($this->FileExt, $this->AllowedExts)) ? true : false) : true) : false;

        $this->Uploaded = true;
      }
    }

    function MoveUploaded($Dest){
      if($this->Allowed && $this->Uploaded)
        if(file_exists($this->File)){
          copy($this->File, $Dest);
          @unlink($this->File);
          return true;
        }

      return false;
    }

    function RemoveTemp(){
      if($this->File){
        @unlink($this->File);
      }
    }

    function MoveTemp($Dest){
      if($this->Allowed && $this->Uploaded)
        if(file_exists($this->File)){
          copy($this->File, $Dest);
          @unlink($this->File);
          return true;
        }

      return false;
    }
  }
?>