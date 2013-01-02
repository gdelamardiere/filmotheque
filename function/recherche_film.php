<?php

require_once("include.php");
require_once('base.php'); 
mysql_select_db($database_base, $base);






function recherche_film($creer,$base){
		
	//recuperation des infos du film sur allocine
	$film=str_replace(".avi","",htmlspecialchars($creer,ENT_QUOTES));
	$film=str_replace(".mkv","",$film);
	$film=str_replace(".AVI","",$film);
	$film=str_replace(".MKV","",$film);
	$movie = new AlloMovie($film);
	$infos = $movie->get();if(empty($infos)){
		echo "pas de film";
		exit;
	}
	
	//affichage du film
	$id="";
	if(isset($infos['id'])){$id=$infos['id'];}
	$synopsis="";
	if(isset($infos['synopsis'])){$synopsis=htmlspecialchars($infos['synopsis'],ENT_QUOTES);}
	$poster="";
	if(isset($infos['poster'])){$poster=htmlspecialchars($infos['poster'],ENT_QUOTES);}
	
	//recherche si le film existe deja
	$query_Recordset = "SELECT lien_pc,id_allocine,id FROM film WHERE id_allocine = '".$id."'";
	$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
	$row_Recordset = mysql_fetch_assoc($Recordset);
	if(isset($row_Recordset['id_allocine']) && $row_Recordset['id_allocine']==$id){
		echo "ce film existe déjà ".$row_Recordset['lien_pc'];
	}
	
		echo utf8_decode("<br/>synopsis=".$synopsis."___id=".$id."___poster=".$poster);
	
	
}



recherche_film($_GET['name'],$base);

?>