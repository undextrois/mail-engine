<?
  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
  {
      if (isset($_POST['list']))
      {
         foreach ($_POST['list'] as $list)
         {
            print "$list<br>\n";
         }
      }
  }
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<input type="text" name="list[]"><br>
<input type="text" name="list[]"><br>
<input type="text" name="list[]"><br>
<input type="text" name="list[]"><br>
<input type="text" name="list[]"><br>
<input type="submit" value="Submit">
</form>
