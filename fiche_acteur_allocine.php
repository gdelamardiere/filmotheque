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
	$query_Recordset1 = "SELECT id_allocine FROM acteur WHERE nom LIKE \"%".$_GET['nom']."%\"";
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

 $person = film_acteur_allocine($id_allocine);
//$person->setid ($pid);
       
 $poster= $person[0];
 $person=$person[1];      
     





$query_Recordset1 = "SELECT id, nom,id_imdb FROM acteur WHERE id_allocine=".$id_allocine;
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$acteur=$row_Recordset1['nom'];
$id_imdb=$row_Recordset1['id_imdb'];
	if($id_imdb==NULL){$id_imdb=recherche_id_imdb($row_Recordset1['id']);}
				

	
	
	
	

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($acteur);?></title>
<link href="style.css" rel="stylesheet" type="text/css" />


</head>

<body>
<div id="entete">
<table><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
<td><input type="button" value="Importer un fichier:" onclick="fichier();"><input type="texte" value="" id="fichier"></td>
<td><h1><?php echo utf8_encode($acteur); ?></h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
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
      <h1><table width="60%" ><tr><td width="30%"><?php echo $poster;?></td><td><?php echo utf8_encode($acteur);?></td></tr></table> </h1>
      <a href="fiche_acteur.php?id=<?php echo $id_imdb; ?> ">Recherche IMDB</a>
      <h3><table cellspacing="1" border="3"><tr><th colspan="2">Film</th><th>Role</th><th>liens</th></tr>
      
      <?php
      
      		foreach($person as $value){
			  			$query_Recordset0 = "SELECT id FROM  `film` WHERE  `id_allocine` =".$value['id']."";
						$Recordset0 = mysql_query($query_Recordset0, $base) or die(mysql_error());
						unset( $nom2);
						while($row_Recordset0 = mysql_fetch_assoc($Recordset0)){
							$nom2='<a href="fiche_film.php?id='.$row_Recordset0['id'].'">'.$value['titre'].'</a>';
						}
						if(isset($nom2)){$name=$nom2;}else{$name=$value['titre'];}
			  			echo "<tr><td>".$value['img']."</td><td>".$name."</td><td>".$value['role']."</td><td><a href=\"http://www.allocine.fr/film/fichefilm_gen_cfilm=".$value['id'].".html\" target=\"_blanck\" >Allocine </a>&nbsp;&nbsp;<a href=\"".lien($value['titre'])."\" target=\"_blanck\"> Streamiz</a></td></tr>";
			  		}
			  				  
			 
	?>
</table></h3>

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
