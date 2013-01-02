<?php 
if(isset($_GET['verif'])&&$_GET['verif']=='true'){
	require_once('base.php');
	require_once('base.php'); 
	mysql_select_db($database_base, $base);
	
	$query_Recordset1 = "SELECT lien_pc FROM film where id='".$_GET['id']."'";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	$lien=$row_Recordset1 ["lien_pc"];
	
	$query="delete from film WHERE id='".$_GET["id"]."'";
		$Recordset = mysql_query($query, $base) or die(mysql_error());
	
	
	
	/*if ($lien!=""){
		system("rm \"".$lien."\" " );
	}*/
}
?>
