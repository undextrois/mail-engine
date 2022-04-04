<?
#############################################################################
# Package: lame-crypt.inc.php
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

$_k0 = "ABCDEFGHIJKMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789";

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
?>
