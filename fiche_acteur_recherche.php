<?php

require_once('function/base.php'); 
mysql_select_db($database_base, $base);

require_once("function/film_acteur_allocine.php");
 
require_once('function/recherche_fiche_personne.php');               

function lien($str){
	$str=strtolower($str);
	$s=array("à","é","è","'","-"," ");
	$r=array("a","e","e","+","+","+");
	return "http://www.streamiz.com/gratuit-".str_replace($s,$r,$str).".html";
	
}


if(isset($_GET['id'])){
	$id_allocine=$_GET['id'];
}
else{
	$query_Recordset1 = "SELECT nom,id_allocine FROM acteur WHERE nom LIKE \"%".$_GET['nom']."%\"";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	if(isset($row_Recordset1['id_allocine'])){
		$id_allocine=$row_Recordset1['id_allocine'];
	}
	else{$id_allocine=0;
	}
}
$query_Recordset4 = "SELECT nom,g.id, count(id_film) as c FROM `genre_film` as l,genre as g WHERE id_genre=g.id group by g.id order by c desc, g.id";
	$Recordset4 = mysql_query($query_Recordset4, $base) or die(mysql_error());

if($id_allocine!=0){
	
	
	
	
	
	

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($_GET['nom']);?></title>
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
<td><h1>Recherche Acteur</h1></td>
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
    <?php
    do{
$id_allocine=$row_Recordset1['id_allocine'];
 $person = film_acteur_allocine($id_allocine);
//$person->setid ($pid);
       
 $poster= $person[0];
 $person=$person[1]; 
$acteur=$row_Recordset1['nom'];
				
	?>
    
    
    
    
      <h1><table width="60%" ><tr><td width="30%"><?php echo $poster;?></td><td><a href="fiche_acteur_allocine.php?id=<?php echo $id_allocine;?>"><?php echo utf8_encode($acteur);?></a></td></tr></table> </h1>
      <?php
      }while($row_Recordset1 = mysql_fetch_assoc($Recordset1));?>
  </div>
</div>

</body>
</html>

<?php
}
else{
	$id_imdb=recherche_id_imdb($_GET['nom']);
	
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($acteur);?></title>
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
<td><h1>Introuvable</h1></td>
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
      Aucun acteur de ce nom dans la base désolé!!<br/><br/>
      <a href="fiche_acteur.php?id=<?php echo $id_imdb; ?> ">Recherche IMDB</a>

  </div>
</div>

</body>
</html>


<?php
}?>
