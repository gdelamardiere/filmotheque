<?php

require_once('function/base.php'); 
mysql_select_db($database_base, $base);

if(isset($_GET['id'])){
	$query_Recordset1 = "SELECT f.titre,f.id,f.poster,i.nom FROM film as f,genre_film as g, genre as i where f.id=g.id_film AND i.id=g.id_genre AND g.id_genre='".$_GET['id']."'";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
}

else{
	$query_Recordset1 = "SELECT f.titre,f.id,f.poster,i.nom FROM film as f,genre_film as g, genre as i where f.id=g.id_film AND i.id=g.id_genre AND i.nom LIKE '%".$_GET['titre']."%' LIMIT 1";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);

	
}
	
	
	
	
	$query_Recordset4 = "SELECT nom,g.id, count(id_film) as c FROM `genre_film` as l,genre as g WHERE id_genre=g.id group by g.id order by c desc, g.id";
	$Recordset4 = mysql_query($query_Recordset4, $base) or die(mysql_error());
	

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($row_Recordset1['titre']); ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />

<SCRIPT language="JavaScript">
	function rep(){
		var repertoire=document.getElementById('rep').value;
		if(repertoire==""){alert("le répertoire n'a pas été renseigné!!");return;}
		else{
			document.location.href="dossier.php?dossier="+repertoire;
		}	
	}
	function fichier(){
		var repertoire=document.getElementById('fichier').value;
		if(repertoire==""){alert("le fichier n'a pas été renseigné!!");return;}
		else{
			document.location.href="fichier.php?dossier="+repertoire;
		}	
	}
	function acteur(){
		var acteur=document.getElementById('acteur').value;
		if(acteur==""){alert("l'acteur n'a pas été renseigné!!");return;}
		else{
			document.location.href="fiche_acteur_recherche.php?nom="+acteur;
		}	
	}
	function film(){
		var film=document.getElementById('film').value;
		if(film==""){alert("le film n'a pas été renseigné!!");return;}
		else{
			document.location.href="index.php?titre="+film;
		}	
	}
</SCRIPT>
</head>

<body>
<div id="entete">
<table><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
<td><input type="button" value="Importer un fichier:" onclick="fichier();"><input type="texte" value="" id="fichier"></td>
<td><h1><?php echo utf8_encode($row_Recordset1['nom']); ?></h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr><tr><td colspan="2"></td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Accueil"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a></h3></td>
<td colspan="2"></td>
</tr></table>
</div>
<div id="conteneur">
    <div id="gauche">
      <?php
      while($row_Recordset4 = mysql_fetch_assoc($Recordset4)){
		echo '<p><a href="fiche_genre.php?id='.$row_Recordset4['id'].'">'.utf8_encode($row_Recordset4['nom']).'</a>('.$row_Recordset4['c'].')</p>';
	}?>
	</div>
	
    <div id="principale2">
      
      <table>
		<tr>
      <?php
	$i=0;
      do{
      	if($i==10){echo"</tr><tr>"; $i=0;}
      	
		echo '<td><a href="fiche_film.php?id='.$row_Recordset1['id'].'"><img width="100" src="'.$row_Recordset1['poster'].'"/><br/>'.utf8_encode($row_Recordset1['titre']).'</a></td>';
		$i++;
	}while($row_Recordset1 = mysql_fetch_assoc($Recordset1));?>
	</tr>
	</table>
  </div>
</div>

</body>
</html>
