<?php

require_once('base.php'); 
mysql_select_db($database_base, $base);
if(!isset($_GET['serie'])){
	
$query_Recordset1 = "SELECT lien_pc FROM film where id='".$_GET['id']."'";
$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$liens=explode(";",$row_Recordset1['lien_pc']);
system("open /Applications/vlc.app \"".$liens[$_GET['lien']]."\"");
}
else{
	system("open /Applications/vlc.app \"".$_GET['serie']."\"");
}
?>
