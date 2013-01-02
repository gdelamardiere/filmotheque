<?php

require_once('base.php'); 
mysql_select_db($database_base, $base);
require_once("include.php");
//$movie = new AlloMovie("pulp fiction");
//$lien="/Users/gdelamardiere/Movies/".$_GET['creer'];
//$film=str_replace(".avi","",htmlspecialchars($_GET['creer'],ENT_QUOTES));

//print_r($infos);
//


	
	
	$query_Recordset2 = "SELECT titre,id FROM film";
	$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	
while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
		$movie = new AlloMovie($row_Recordset2['titre']);
		$infos = $movie->get();
		print_r($infos);
		if(isset($infos['annee-production'])){
		$annee=str_replace("-","",$infos['annee-production']);
		$query_Recordset1 = "UPDATE film SET annee_production='".$annee."' WHERE id='".$row_Recordset2['id']."'";
		$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
		}
	}
?>




