<?php 
require_once("classe_allocine/Allo.class.php");
require_once("classe_allocine/AlloSerie.class.php");
require_once("function/base.php");







	mysql_select_db($database_base, $base);
	$query_Recordset2 = "SELECT titre,id,poster FROM serie order by titre";
	$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	
	if(isset($_GET['titre'])){
		$query_Recordset2 = "SELECT titre,id,poster FROM serie where titre LIKE '%".$_GET['titre']."%' UNION SELECT titre,id,poster FROM film where titre_original LIKE '%".$_GET['titre']."%'";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());		
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Accueil</title>
<link href="style.css" rel="stylesheet" type="text/css" />

<SCRIPT language="JavaScript">
	function rep(){
		var repertoire=document.getElementById('rep').value;
		document.location.href="dossier_serie.php?dossier="+repertoire;
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
<div id="sous_entete">
</div>
<table><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
<td><input type="button" value="Importer un fichier:" onclick="fichier();"><input type="texte" value="" id="fichier"></td>
<td><h1>Série</h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr><tr><td colspan="2"></td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Accueil"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a></h3></td>
<td colspan="2"></td>
</tr></table>
</div>
<div id="conteneur">
    
	<div id="principale3">
      
      
      <table>
<tr>
	<?php
	$i=0;
      while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
      	if($i==9){echo"</tr><tr>"; $i=0;}
      	
		echo '<td><a href="fiche_serie.php?id='.$row_Recordset2['id'].'"><img width="100" src="'.$row_Recordset2['poster'].'"/><br/>'.utf8_encode($row_Recordset2['titre']).'</a></td>';
		$i++;
	}?>
	</tr>
	</table>

  </div>
</div>

</body>
</html>


