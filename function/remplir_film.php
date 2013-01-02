<?php

require_once("include.php");
require_once('base.php'); 
mysql_select_db($database_base, $base);






function remplir_film($creer,$dossier,$base){
	//recuperation des genres de ma base
	if(strlen($dossier)-1!="/"){$dossier=$dossier."/";}
	$dossier=str_replace("//","/",$dossier);
	$tab= array();
	$query_Recordset2 = "SELECT nom,id FROM genre";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
		
	while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
		$tab[$row_Recordset2['nom']]=$row_Recordset2['id'];
	}
	
	
	//recuperation des infos du film sur allocine
	$lien=$dossier.$creer;
	$film=str_replace(".avi","",htmlspecialchars($creer,ENT_QUOTES));
	$film=str_replace(".mkv","",$film);
	$film=str_replace(".AVI","",$film);
	$film=str_replace(".MKV","",$film);
	$movie = new AlloMovie($film);
	$infos = $movie->get();if(empty($infos)){
		echo "pas de film"; 
		system("mv \"".$dossier.$creer."\" \"".$dossier."pas_trouve/".$creer."\"");
		exit;
	}
	
	//remplissage du film
	$id="";
	if(isset($infos['id'])){$id=$infos['id'];}
	$titre_original="";
	if(isset($infos['titre-original'])){$titre_original=htmlspecialchars($infos['titre-original'],ENT_QUOTES);}
	$titre="";
	if(isset($infos['titre'])){$titre=htmlspecialchars($infos['titre'],ENT_QUOTES);}
	$annee="";
	if(isset($infos['annee-production'])){$annee=str_replace('-','',htmlspecialchars($infos['annee-production'],ENT_QUOTES));}
	$duree="";
	if(isset($infos['duree']['global'])){$duree=htmlspecialchars($infos['duree']['global'],ENT_QUOTES);}
	$synopsis="";
	if(isset($infos['synopsis'])){$synopsis=htmlspecialchars($infos['synopsis'],ENT_QUOTES);}
	$bande="";
	if(isset($infos['bande-annonce']['href'])){$bande=htmlspecialchars($infos['bande-annonce']['href'],ENT_QUOTES);}
	$poster="";
	if(isset($infos['poster'])){$poster=htmlspecialchars($infos['poster'],ENT_QUOTES);}
	
	$query_Recordset = "SELECT lien_pc,id_allocine,id FROM film WHERE id_allocine = '".$id."'";
	$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
	$row_Recordset = mysql_fetch_assoc($Recordset);
	if(isset($row_Recordset['id_allocine']) && $row_Recordset['id_allocine']==$id){
		$nb=substr_count($row_Recordset['lien_pc'],";")+1 ;
		$d=$dossier.$nb."_".$creer;
		system("mv \"".$dossier.$creer."\" \"".$d."\"");
		
		$query_Recordset1 = "UPDATE film SET lien_pc='".$row_Recordset['lien_pc'].";".$d."' WHERE id_allocine = '".$id."'";
		$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());
		return $row_Recordset['id'];
	}
	
	$query_Recordset1 = "INSERT INTO film (id_allocine,titre_original,titre,annee_production,duree,synopsis,bande_annonce,lien_pc,poster) VALUES('".$id."','".$titre_original."','".$titre."','".$annee."','".$duree."','".$synopsis."','".$bande."','".$lien."','".$poster."')";
	$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());

	
		
	
	$id_du_film=mysql_insert_id();
	
	
	//remplissage du genre
	if(isset($infos['genre'])){
		foreach($infos['genre'] as $genre){
			$query_Recordset3 = "INSERT INTO genre_film (id_genre,id_film) VALUES('".$tab[$genre]."','".$id_du_film."')";
			$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
		}
	}

	//realisateur
	if(isset($infos['casting-court']['realisateurs'])){$realisateur=htmlspecialchars($infos['casting-court']['realisateurs'],ENT_QUOTES);}else{$realisateur="";}
	
	$query_Recordset = "SELECT id FROM acteur WHERE nom LIKE '".$realisateur."'";
	$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
	$row_Recordset = mysql_fetch_assoc($Recordset);
	if(isset($row_Recordset['id'])){
		$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_du_film."','".$row_Recordset['id']."') ";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	}
	
	else{
		$id=0;
		if($realisateur!=""){
			$rea = new AlloPerson($realisateur);
			$infos_rea = $rea->get();
			if(isset($infos_rea['id'])){$id=$infos_rea['id'];}			
		}
		$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".$realisateur."','".$id."') ";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
		$id=mysql_insert_id();
		
		$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_du_film."','".$id."') ";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
		
	}

	//acteurs
	
	for ($i=0;$i<7;$i++){
		if(isset($infos['casting'][$i]) && $infos['casting'][$i]['activite']!='Réalisateur'){
			$query_Recordset = "SELECT id FROM acteur WHERE nom LIKE '".htmlspecialchars($infos['casting'][$i]['personne'],ENT_QUOTES)."'";
			$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
			$row_Recordset = mysql_fetch_assoc($Recordset);
			if(isset($row_Recordset['id'])){
				$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_du_film."','".$row_Recordset['id']."','".htmlspecialchars($infos['casting'][$i]['role'],ENT_QUOTES)."') ";
				$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
			}
			
			else{
				$id=0;
				$rea = new AlloPerson($infos['casting'][$i]['personne']);
				$infos_rea = $rea->get();
				if(isset($infos_rea['id'])){$id=$infos_rea['id'];}			
				
				$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".htmlspecialchars($infos['casting'][$i]['personne'],ENT_QUOTES)."','".$id."') ";
				$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
				$id=mysql_insert_id();
				
				$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_du_film."','".$id."','".htmlspecialchars($infos['casting'][$i]['role'],ENT_QUOTES)."') ";
				$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
				
			}				
			
		}		
		
	}	
	return $id_du_film;
	
	
}



function remplir_film2($id,$id_old,$base){
	
	
	
	$query = "SELECT lien_pc FROM film where id='".$id_old."'";
	$Recordset = mysql_query($query, $base) or die(mysql_error());
	$row = mysql_fetch_assoc($Recordset);
	$lien=$row['lien_pc'];
	
	$query="delete from film WHERE id='".$id_old."'";
	$Recordset = mysql_query($query, $base) or die(mysql_error());
	//recuperation des genres de ma base
	
	$query = "SELECT id,lien_pc FROM film where id_allocine='".$id."'";
	$Recordset1 = mysql_query($query, $base) or die(mysql_error());
	$row1 = mysql_fetch_assoc($Recordset1);
	if(isset($row1['id'])){
		$query_Recordset0 = "UPDATE film SET lien_pc='".$row1['lien_pc'].";".$lien."' WHERE id = '".$row1['id']."'";
		$Recordset0 = mysql_query($query_Recordset0, $base)or die(mysql_error());
		return $row1['id'];		
	}


	$tab= array();
	$query_Recordset2 = "SELECT nom,id FROM genre";
	$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	
		
	while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
		$tab[$row_Recordset2['nom']]=$row_Recordset2['id'];
	}
	
	
	//recuperation des infos du film sur allocine
	
	$movie = new AlloMovie((int)$id);
	$infos = $movie->get();if(empty($infos)){echo "pas de film"; return;}
	
	//remplissage du film
	$id="";
	if(isset($infos['id'])){$id=$infos['id'];}
	$titre_original="";
	if(isset($infos['titre-original'])){$titre_original=htmlspecialchars($infos['titre-original'],ENT_QUOTES);}
	$titre="";
	if(isset($infos['titre'])){$titre=htmlspecialchars($infos['titre'],ENT_QUOTES);}
	$annee="";
	if(isset($infos['annee-production'])){$annee=str_replace('-','',htmlspecialchars($infos['annee-production'],ENT_QUOTES));}
	$duree="";
	if(isset($infos['duree']['global'])){$duree=htmlspecialchars($infos['duree']['global'],ENT_QUOTES);}
	$synopsis="";
	if(isset($infos['synopsis'])){$synopsis=htmlspecialchars($infos['synopsis'],ENT_QUOTES);}
	$bande="";
	if(isset($infos['bande-annonce']['href'])){$bande=htmlspecialchars($infos['bande-annonce']['href'],ENT_QUOTES);}
	$poster="";
	if(isset($infos['poster'])){$poster=htmlspecialchars($infos['poster'],ENT_QUOTES);}
	
	$query_Recordset1 = "INSERT INTO film (id_allocine,titre_original,titre,annee_production,duree,synopsis,bande_annonce,lien_pc,poster) VALUES('".$id."','".$titre_original."','".$titre."','".$annee."','".$duree."','".$synopsis."','".$bande."','".$lien."','".$poster."')";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$id_du_film=mysql_insert_id();
	
	
	//remplissage du genre
	if(isset($infos['genre'])){
		foreach($infos['genre'] as $genre){
			$query_Recordset3 = "INSERT INTO genre_film (id_genre,id_film) VALUES('".$tab[$genre]."','".$id_du_film."')";
			$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
		}
	}

	//realisateur
	if(isset($infos['casting-court']['realisateurs'])){$realisateur=htmlspecialchars($infos['casting-court']['realisateurs'],ENT_QUOTES);}else{$realisateur="";}
	
	$query_Recordset = "SELECT id FROM acteur WHERE nom LIKE '".$realisateur."'";
	$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
	$row_Recordset = mysql_fetch_assoc($Recordset);
	if(isset($row_Recordset['id'])){
		$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_du_film."','".$row_Recordset['id']."') ";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	}
	
	else{
		$id=0;
		if($realisateur!=""){
			$rea = new AlloPerson($realisateur);
			$infos_rea = $rea->get();
			if(isset($infos_rea['id'])){$id=$infos_rea['id'];}			
		}
		$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".$realisateur."','".$id."') ";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
		$id=mysql_insert_id();
		
		$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_du_film."','".$id."') ";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
		
	}

	//acteurs
	
	for ($i=0;$i<7;$i++){
		if(isset($infos['casting'][$i]) && $infos['casting'][$i]['activite']!='Réalisateur'){
			$query_Recordset = "SELECT id FROM acteur WHERE nom LIKE '".htmlspecialchars($infos['casting'][$i]['personne'],ENT_QUOTES)."'";
			$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
			$row_Recordset = mysql_fetch_assoc($Recordset);
			if(isset($row_Recordset['id'])){
				$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_du_film."','".$row_Recordset['id']."','".htmlspecialchars($infos['casting'][$i]['role'],ENT_QUOTES)."') ";
				$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
			}
			
			else{
				$id=0;
				$rea = new AlloPerson($infos['casting'][$i]['personne']);
				$infos_rea = $rea->get();
				if(isset($infos_rea['id'])){$id=$infos_rea['id'];}			
				
				$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".htmlspecialchars($infos['casting'][$i]['personne'],ENT_QUOTES)."','".$id."') ";
				$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
				$id=mysql_insert_id();
				
				$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_du_film."','".$id."','".htmlspecialchars($infos['casting'][$i]['role'],ENT_QUOTES)."') ";
				$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
				
			}				
			
		}		
		
	}	
	
	return $id_du_film;
	
	
}

$dossier="/Volumes/gdelamardiere/MesFichiers/MesVideos/film/";
if(isset($_GET['dossier'])){
	$dossier=$_GET['dossier'];
}
	
 
 if(isset($_GET['creer'])){
 	$file=str_replace("\'"," ",$_GET["creer"]);
 	if(substr_count($file,".srt")){echo '<script language="javascript" type="text/javascript"> window.close(); </script>';}
 	else{
 		$id=remplir_film($file,$dossier,$base);
		header('Location: ../fiche_film.php?id='.$id);
 	}
 }
 
 else if(isset($_POST['id'])){
	
	$id=remplir_film2($_POST['id'],$_POST['id_old'],$base);
	header('Location: ../fiche_film.php?id='.$id);
 }


else{ header('Location: ../index.php');
}




?>