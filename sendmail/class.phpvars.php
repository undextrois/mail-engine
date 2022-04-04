<?
  class PHPVars {
    var $Post;
    var $Get;
    var $Cookie;
    var $Server;
    var $Env;
    var $Session;
    var $Location;
    var $Sess;
    var $Loc;

    function ParseLocationVars($VarStr, $VarDelimiter='/'){
      $Script = basename($this->Server['SCRIPT_FILENAME']);
      $URI = substr($this->Server['PHP_SELF'], strpos($Script, $this->Server['PHP_SELF']) + strlen($Script) + 1);
      $Qstr = explode($VarDelimiter, $URI);
      $QstrVar = explode('|', $VarStr);

      for($i=0;$i<count($$QstrVar);$i++){
        $this->Location[$QstrVar[$i]] = str_replace('|', '/', $Qstr[$i + 1]);
      }
    }

    function PHPVars(){
      global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS, $HTTP_SESSION_VARS;

      $this->Post = &$HTTP_POST_VARS;
      $this->Get = &$HTTP_GET_VARS;
      $this->Cookie = &$HTTP_COOKIE_VARS;
      $this->Server = &$HTTP_SERVER_VARS;
      $this->Env = &$HTTP_ENV_VARS;
      $this->Session = &$HTTP_SESSION_VARS;
      $this->Location = array();
      $this->Sess = &$HTTP_SESSION_VARS;
      $this->Loc = &$this->Location;
    }
  }
?>