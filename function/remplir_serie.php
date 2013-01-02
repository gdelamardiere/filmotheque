<?php


require_once('base.php'); 
mysql_select_db($database_base, $base);

if(isset($_GET['dossier'])){
	$name=$_GET['dossier'].$_GET['input0'];
	$episode=intval($_GET['episode']);
	$saison=intval($_GET['saison']);
	$id_serie=$_GET['id_serie'];
	
	
	$query_Recordset = "SELECT id,lien FROM episode WHERE id_serie = '".$id_serie."' AND id_saison = '".$saison."' AND num_episode = '".$episode."'";
	$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
	$row_Recordset = mysql_fetch_assoc($Recordset);
	
		$query_Recordset1 = "UPDATE episode SET lien='".$name."' WHERE id = '".$row_Recordset['id']."'";
		$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());
		
	
	
	

}







?>