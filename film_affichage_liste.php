<?php

require_once('function/base.php'); 
mysql_select_db($database_base, $base);

if(isset($_GET['id'])){
	$query_Recordset1 = "SELECT f.titre,f.id,f.poster FROM film as f,genre_film as g, genre as i where f.id=g.id_film AND i.id=g.id_genre AND g.id_genre='".$_GET['id']."' order by f.titre";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
}

else if(isset($_GET['titre'])){
	$query_Recordset1 = "SELECT f.titre,f.id,f.poster FROM film as f where f.titre LIKE '%".$_GET['titre']."%' OR f.titre_original LIKE '%".$_GET['titre']."%' LIMIT 1";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);

	
}

else{
	$query_Recordset1 = "SELECT titre,id,poster FROM film as f order by titre";
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

<link rel="stylesheet" href="fx.slide.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/mootools-1.2-core-yc.js"></script>
<script type="text/javascript" src="js/common.js"></script>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	<script>
		!window.jQuery && document.write('<script src="fancybox/jquery-1.4.3.min.js"><\/script>');
	</script>
	<script type="text/javascript" src="fancybox/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="fancybox/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="fancybox/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
 	<link rel="stylesheet" href="fancybox/style.css" />
	<script type="text/javascript">      
		$(document).ready(function() {       
			/*
			*   Examples - images
			*/

			$("a.iframe").fancybox({
      	'width'				: '70%',
				'height'			: 820,
				'autoScale'			: true,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				//'titlePosition'		: 'outside',
				'titlePosition'		: 'over',
				'type'				: 'iframe'
      });   
      });   			   
	</script>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="container" style="float:right;">
		<div id="top">
		<!-- login -->
			<ul class="login">
		    	<li class="left">&nbsp;</li>		        
				<li><a id="toggleLogin" href="#" onclick="ouvrir();">Menu</a></li>
			</ul> <!-- / login -->
		</div> <!-- / top -->        
	</div><!-- / container -->


<div id="login" >
		<div class="entete">
			<div id="sous_entete" style="height: 160px;"></div>
			<div class="loginClose"><a href="#" id="closeLogin" onclick="fermer();">Close Panel</a></div>			<table style="width:100%" ><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
<td><h1>Menu Principal</h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr></td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Film"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a>
<br><a href="http://localhost/filmotheque/film_affichage_liste.php"><input type="button" value="Affichage liste"></a></h3></td>
</tr>





</table>

			
			
			
		</div>
		
		
	</div>



<div id="conteneur">
    <div id="gauche">
      <?php
      while($row_Recordset4 = mysql_fetch_assoc($Recordset4)){
		echo '<p><a href="film_affichage_liste.php?id='.$row_Recordset4['id'].'">'.utf8_encode($row_Recordset4['nom']).'('.$row_Recordset4['c'].')</a></p>';
	}?>
	</div>
	
    <div id="principale2">
      
      <table>
		<tr>
      <?php
	$i=0;
      do{
      	if($i==10){echo"</tr><tr>"; $i=0;}
      	
		echo '<td><a href="fiche_film.php?id='.$row_Recordset1['id'].'" class="iframe"><img width="100" src="'.$row_Recordset1['poster'].'"/><br/>'.utf8_encode($row_Recordset1['titre']).'</a></td>';
		$i++;
	}while($row_Recordset1 = mysql_fetch_assoc($Recordset1));?>
	</tr>
	</table>
  </div>
</div>

<SCRIPT language="JavaScript">


	function rep(){
		var repertoire=document.getElementById('rep').value;
		if(repertoire==""){alert("le répertoire n'a pas été renseigné!!");return;}
		else{
			document.location.href="dossier.php?dossier="+repertoire;
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
			document.location.href="film_affichage_liste.php?titre="+film;
		}	
	}
	
		
	function ouvrir(){
		document.getElementById('login').style.overflow='visible';
		document.getElementById('container').style.display='none';		
		
	}
	
	function fermer(){
		document.getElementById('login').style.overflow='hidden';
		document.getElementById('container').style.display='inline';
	}
	
	hauteur=document.body.offsetHeight+65;
	document.getElementById('principale2').style.height=hauteur+'px';
	//alert(document.getElementById('principale2').style.height);
</SCRIPT>



</body>
</html>
