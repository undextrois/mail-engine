<?
  if (($f = fopen("_HW0NP12/suffix.txt","r+b"))==false)
  {
     die ("error");
  }
  $f2 = fopen("suffix.h","w+t");
  $i = 0;
  while (!feof($f))
  {  $i++;
     $l = explode("|",trim(fgets($f,1024)));
     fputs($f2,"$i => array('$l[0]','".trim(mysql_escape_string($l[1]))."'),\n\r");
  }
  fclose ($f2);
  fclose ($f);
?>
