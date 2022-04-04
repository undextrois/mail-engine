<?
#############################################################################
# Package: class.inc.php
# Date: November 2004
#
# Altomeyer LLC
#
#############################################################################

if (!defined('__GN_U_MODULE_TRAP__')
  || (__GN_U_MODULE_TRAP__ != 'GN-F4DCC0DD0B42F6B4C1565E59C92E308F-DVT'))
{
   die ("don't make me kick your arse!");
}

class _UserQuota_Class extends _UserCookie_Class
{
  var $diskid_PK;
  var $downer_FK;
  var $dQuotaSZ;
};

class _UserFolder_Class
{
  var $folderid_PK;
  var $fowner_FK;
  var $folderName;
  var $fModifyDate;
  var $fCreateDate;
};

class _UserFile_Class extends _UserFolder_Class
{
  var $fileid_PK;
  var $fowner_FK;
  var $folderid_FK;
  var $fHashMD5;
  var $fPrefixKey;
  var $fMagicKey;
  var $fModifyDate;
  var $fCreateDate;
};

class _UserCookie_Class
{
  var $cCacheid_PK;
  var $cCacheOwner;
  var $cCacheKeyN;
  var $cCacheKeyV;
  var $cCacheExpr;
};

class _UserLoginInfo_Class extends _UserCookie_Class
{
  var $userid_PK;
  var $loginID;
  var $passwd;
};
?>
