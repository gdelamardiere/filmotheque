<?php

require_once('function/base.php');
mysql_select_db($database_base, $base);

if(isset($_GET['id'])){
	$query_Recordset1 = "SELECT * FROM serie where id='".$_GET['id']."'";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
}

else{
	$query_Recordset1 = "SELECT * FROM serie where titre LIKE '%".$_GET['titre']."%' LIMIT 1";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);

	
}
	
	

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($row_Recordset1['titre']); ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />

<SCRIPT language="JavaScript">
function lancer_film(){
		fichier="function/lancer_film.php?id=<?php echo $row_Recordset1['id'];?>";
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
	
	function suppr(){
		if(confirm('etes vous sur de supprimer ce fichier')){
			fichier="function/delete.php?id=<?php echo $row_Recordset1['id'];?>&verif=true";
			if(window.XMLHttpRequest) // FIREFOX
			xhr_object = new XMLHttpRequest();
			else if(window.ActiveXObject) // IE
			xhr_object = new ActiveXObject("Msxml2.XMLHTTP");
			else
			return(false);
			xhr_object.open("GET", fichier, false);
			xhr_object.send(null);
			if(xhr_object.readyState == 4) {alert(xhr_object.responseText);document.location.href="index.php";}
			else{alert("erreur");return(false);}
			
		}
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
<td><input type="button" value="Rechercher une série:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr><tr><td colspan="2"></td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Accueil"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a></h3></td>
<td colspan="2"></td>
</tr></table>
</div>
<div id="conteneur">
    
	<div id="image_fiche_film"><img src="<?php echo $row_Recordset1['poster']; ?>"/></div>
    <div id="principale">
      <h1>Titre original: <?php echo utf8_encode($row_Recordset1['titre_original']); ?> <br/>Titre vf: <?php echo utf8_encode($row_Recordset1['titre']); ?> </h1>
      <table border="0" cellspacing="10" cellpadding="0">
       
        <tr>
          <td colspan="2"><a href="http://www.allocine.fr/series/ficheserie-<?php echo $row_Recordset1['id_allocine']; ?>/saisons/" target="_blank"><img src="allocine.png"/></a><br/>
          <FORM METHOD="POST"ACTION="function/remplir_serie.php" >
          	id_allocine: <input type="text" name="id" id="id"/>
          	<input type="hidden" name="id_old" id="id_old" value="<?php echo $row_Recordset1['id']; ?>"/>
          
          </FORM>
          </td>
          <td >
          <a href="modifier_fiche_film.php?id=<?php echo $row_Recordset1['id']; ?>">Modifier manuellement</a></td>
        </tr>
        <tr>
          <td colspan="3">Acteurs:<br/>
          	 <?php
          	$query_Recordset3 = "SELECT a.id_allocine, a.nom as nom,a.id as i FROM acteur as a,acteur_serie as l where a.id = l.id_acteur AND l.id_serie='".$row_Recordset1['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
				echo "<a href=\"fiche_acteur_allocine.php?id=".$row_Recordset3['id_allocine']."\">".utf8_encode($row_Recordset3['nom'])."</a><br/>";
			}
          
			?></td>
        </tr>
        
      </table>
      
      <p id="synopsis"><?php echo utf8_encode(html_entity_decode($row_Recordset1['synopsis'])); ?></p>
      <p>Nombre de saisons: <?php echo $row_Recordset1['nombre_saisons']; ?></p>
      <p>Nombre d'épisodes: <?php echo $row_Recordset1['nombre_episodes']; ?> (durée: <?php echo $row_Recordset1['duree_episode']; ?>mn)</p>
      <p></p>
      <p>Liste des saisons:</p>
      <p>
      <ul>
      <?php
      for($i=1;$i<=$row_Recordset1['nombre_saisons'];$i++){?>
      <li>
      <a href="fiche_saison.php?id_serie=<?php echo $row_Recordset1['id']; ?>&num_saison=<?php echo $i; ?>">Saison <?php echo $i; ?></a>
      </li>
      <?php }?>
      </ul>
      
  </div>
</div>

</body>
</html>
