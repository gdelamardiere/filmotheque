<?php

require_once('function/base.php'); 
mysql_select_db($database_base, $base);

require_once("imdb/imdb_person.class.php");
              

if(isset($_GET['nom'])){
	require_once("imdb/imdbsearch.class.php");
	$id_imdb=0;
}

else{
	$id_imdb="";
	$n=strlen($_GET['id']);
	for($i=7;$i>$n;$i--){ 
		$id_imdb.='0';
	}
	$id_imdb.=$_GET['id'];
	
}

function lien($str){
	$str=strtolower($str);
	$s=array("à","é","è","'","-"," ");
	$r=array("a","e","e","+","+","+");
	return "http://www.streamiz.com/gratuit-".str_replace($s,$r,$str).".html";
	
}

 $person = new imdb_person($id_imdb);
//$person->setid ($pid);
$tab=array();	

//$ff = array("producer","director","actor");
if(isset($_GET['tous'])){$ff = array("producer","director","actor","actress");}
    else{$ff = array("actor","actress");}

  foreach ($ff as $var) {
    $fdt = "movies_".$var;
    
    $filmo = $person->$fdt();
    if (!empty($filmo)) {
    	foreach ($filmo as $film) {
    		if (!empty($film["chname"])||isset($_GET['tous'])) {
    		 if (!empty($film["year"])) {$annee=$film["year"];}else{$annee=0;}
    		 
    		 if($var=="actor"||$var=="actress"){
    		 	if (empty($film["chname"])) {$chname= "Acteur";} else {$chname= "Acteur(".$film["chname"].")";}
    		 	
    		 }
    		 else{
    		 	$chname= $var;
    		 }
    		if(!isset($tab[$annee])){
    			$tab[$annee]=array($film["name"]=>array('id'=>$film["mid"],'chname'=>$chname));
    		}
    		else{
    			if(!isset($tab[$annee][$film["name"]]))
    			{$tab[$annee][$film["name"]]=array('id'=>$film["mid"],'chname'=>$chname);}  
    			else{
    				$tab[$annee][$film["name"]]['chname'].=', '.$chname;
    			}  			
    			
    		}
    		
    	}
    	}
    }
  }
    			
   krsort($tab);		
      
        
        
     




$query_Recordset1 = "SELECT id_allocine FROM acteur WHERE id_imdb=".$id_imdb;
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$id_allocine=$row_Recordset1['id_allocine'];



	
	
	$query_Recordset4 = "SELECT nom,g.id, count(id_film) as c FROM `genre_film` as l,genre as g WHERE id_genre=g.id group by g.id order by c desc, g.id";
	$Recordset4 = mysql_query($query_Recordset4, $base) or die(mysql_error());
	

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($person->name()); ?></title>
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
<td><h1><?php echo utf8_encode($person->name()); ?></h1></td>
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
    <h1><table width="60%" ><tr><td width="30%"><?php if (($photo_url = $person->photo() ) != FALSE) {
			    echo '<img src="'.$photo_url.'" alt="Cover">';
			  } ?></td><td><?php echo utf8_encode($person->name()); ?></td></tr></table> </h1>
     <a href="fiche_acteur_allocine.php?id=<?php echo $id_allocine; ?> ">Recherche Allocine</a>&nbsp;&nbsp;<?php if(isset($_GET['tous'])){echo "<a href=\"fiche_acteur.php?id=".$id_imdb."\">Seulement les plus connus</a><br/>";}
			  else{echo "<a href=\"fiche_acteur.php?id=".$id_imdb."&tous=true\">Tous</a><br/>"; }
			  echo "<table cellspacing=\"0\" border=\"2\"><tr><td>Année</td><td style=\"padding:0px\"><table style=\"margin:0px\" cellspacing=\"0\" class=\"tab_personne\"><tr><td>Film</td><td>Role</td><td>Recherche Lien</td></tr></table></td></tr>";
			  if (!empty($tab)){
			  	foreach($tab as $annee => $value){ 
			  	if($annee==0){echo	"<tr><td>date inconnu</td><td ><table cellspacing=\"0\" class=\"tab_personne\" >"; }else{echo	"<tr><td>".$annee."</td><td ><table cellspacing=\"0\" class=\"tab_personne\">";}
			  	
			  		foreach($value as $nom => $value2){
			  			$query_Recordset0 = "SELECT id FROM  `film` WHERE  `titre_original` LIKE  '%".$nom."%'";
						$Recordset0 = mysql_query($query_Recordset0, $base) or die(mysql_error());
						unset( $nom2);
						while($row_Recordset0 = mysql_fetch_assoc($Recordset0)){
							$nom2='<a href="fiche_film.php?id='.$row_Recordset0['id'].'">'.$nom.'</a>';
						}
						if(isset($nom2)){$name=$nom2;}else{$name=$nom;}
			  			echo "<tr><td>".$name."</td><td>".$value2['chname']."</td><td><a href=\"http://www.imdb.com/title/tt".$value2['id']."\" target=\"_blanck\" >IMDB(en) </a><a href=\"http://www.imdb.fr/title/tt".$value2['id']."\" target=\"_blanck\" >IMDB (fr) </a> <a href=\"".lien($nom)."\" target=\"_blanck\"> Streamiz</a></td></tr>";
			  		}
			  	echo "</table></td></tr>";
			  	
			  }
			  }
			  
			  echo "</table>";
	?>


  </div>
</div>

</body>
</html>
