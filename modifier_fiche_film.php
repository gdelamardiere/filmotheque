<?php

require_once('function/base.php'); 
mysql_select_db($database_base, $base);
if(isset($_POST['id'])){
	
	
	
	$query_Recordset1 = "UPDATE film SET titre_original='".utf8_decode($_POST['titre_original'])."',titre='".utf8_decode($_POST['titre'])."',annee_production='".utf8_decode($_POST['annee_production'])."',duree='".utf8_decode($_POST['duree'])."',synopsis='".utf8_decode($_POST['synopsis'])."',bande_annonce='".utf8_decode($_POST['bande_annonce'])."',lien_pc='".utf8_decode($_POST['lien_pc'])."',poster='".utf8_decode($_POST['poster'])."' WHERE id='".$_POST['id']."'";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());	
		
	
	
	if($_POST['genre']=='oui'){
		$query_Recordset1 = "DELETE FROM genre_film where id_film='".$_POST['id']."'";
		echo $query_Recordset1;
		//$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
		
	}
	
	if($_POST['acteur']=='oui'){
		$query_Recordset1 = "DELETE FROM acteur_film where id_film='".$_POST['id']."'";
		$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
		//echo $query_Recordset1;
		$query_Recordset1 = "DELETE FROM realisateur_film where id_film='".$_POST['id']."'";
		$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
		//echo $query_Recordset1;
		
	}
}


	$query_Recordset1 = "SELECT * FROM film where id='".$_GET['id']."'";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);

	
	
	
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
function lancer_film(){
		fichier="lancer_film.php?id=<?php echo $row_Recordset1['id'];?>";
		if(window.XMLHttpRequest) // FIREFOX
		xhr_object = new XMLHttpRequest();
		else if(window.ActiveXObject) // IE
		xhr_object = new ActiveXObject("Msxml2.XMLHTTP");
		else
		return(false);
		xhr_object.open("GET", fichier, true);
		xhr_object.send(null);
		if(xhr_object.readyState == 4) return(xhr_object.responseText);
		else return(false);
	}


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
<td><h1><?php echo utf8_encode($row_Recordset1['titre']); ?></h1></td>
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
	</div><FORM METHOD="POST"ACTION="#" >
	<input type="hidden" name="id" id="id" value="<?php echo $row_Recordset1['id']; ?>"/>
	<div id="image_fiche_film"><img src="<?php echo $row_Recordset1['poster']; ?>"/></div>
    <div id="principale">
    
      <h1>Titre original: <input type="text" name="titre_original" id="titre_original" value="<?php echo utf8_encode($row_Recordset1['titre_original']); ?>"/> <br/>Titre vf: <input type="text" name="titre" id="titre" value="<?php echo utf8_encode($row_Recordset1['titre']); ?>"/> </h1>
      <table border="0" cellspacing="10" cellpadding="0">
        <tr>
          <td>Date: <input type="text" name="annee_production" id="annee_production" value="<?php echo $row_Recordset1['annee_production']; ?>"/></td>
          <td>Duree: <input type="text" name="duree" id="duree" value="<?php echo $row_Recordset1['duree']; ?>"/></td>
          
        </tr>
        <tr>
          <td colspan="2"><a href="http://www.allocine.fr/film/fichefilm_gen_cfilm=<?php echo $row_Recordset1['id_allocine']; ?>.html" target="_blank"><img src="allocine.png"/></a>
          
          </td>
          <td >bande annonce:<input type="text" name="bande_annonce" id="bande_annonce" value="<?php echo $row_Recordset1['bande_annonce']; ?>"/>
          </td>
        </tr>
        
      </table>
      
      <p id="synopsis"><textarea name="synopsis" id="synopsis"><?php echo utf8_encode(html_entity_decode($row_Recordset1['synopsis'])); ?></textarea></p>
      <table width="100%" border="0" cellspacing="10" cellpadding="0">
        <tr>
          <td><a href="#" onclick="lancer_film()">play</a><br/>lien: <input type="text" name="lien_pc" id="lien_pc" value="<?php echo $row_Recordset1['lien_pc']; ?>"/><br/>
	poster:<input type="text" name="poster" id="poster" value="<?php echo $row_Recordset1['poster']; ?>"/></td>
          
        </tr>
      </table>
      Vider les genres <input type="radio" name="genre" value="oui" > oui&nbsp;&nbsp;<input type="radio" name="genre" value="non" checked> non<br>
      Vider les acteurs <input type="radio" name="acteur" value="oui" > oui&nbsp;&nbsp;<input type="radio" name="acteur" value="non" checked> non<br> <br/>
<input type="submit" value="Modifier"><br/><a href="fiche_film.php?id=<?php echo $_GET['id'];?>"><input type="submit" value="Annuler"></a>
      
      </form>
  </div>
  
</div>

</body>
</html>
