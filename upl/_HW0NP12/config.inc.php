<?
#############################################################################
# Package: config.inc.php
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

$_ML_Config{'host'} = 'localhost';
$_ML_Config{'user'} = 'ukpauser';
$_ML_Config{'pass'} = 'ukpauser';
$_ML_Config{'db'}   = '_mailuser';

$_ML_Config{'_private_node'}  = "/var/www/_UKML/pub";
$_ML_Config{'_upload_max_sz'} = (2 * (100000));        # 2 mb only?
$_ML_Config{'_folder_depth'}  = 4;
$_ML_Config{'_folder_prefix'} = "_UKM.";
$_ML_Config{'_folder_mask'}   = 0700;

$_ML_Config{'_debug_opt'}     = false;
?>
