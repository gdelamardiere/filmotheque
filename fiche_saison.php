<?php

require_once('function/base.php');
mysql_select_db($database_base, $base);

	$tab=array();
	$query_Recordset1 = "SELECT * FROM episode where id_serie='".$_GET['id_serie']."' AND id_saison='".$_GET['num_saison']."' order by num_episode ASC  ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
		$tab[]=$row_Recordset1;		
	}
	
	$query_Recordset2 = "SELECT * FROM serie where id='".$_GET['id_serie']."'";
	$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	$row_Recordset2 = mysql_fetch_assoc($Recordset2);

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($row_Recordset1['titre']); ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />

<SCRIPT language="JavaScript">
function lancer_film(id){
	fichier="function/lancer_film.php?serie="+id;
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
<div id="conteneur2">
    
	
   <div id="entete_saison">
    <div id="image_saison"><img src="<?php echo $row_Recordset2['poster']; ?>"/></div>
    <div id="synop_saison">
    <table border="0" cellspacing="10" cellpadding="0">
    <tr><td rowspan="2">
      <h1>Titre original: <?php echo utf8_encode($row_Recordset2['titre_original']); ?> <br/>Titre vf: <?php echo utf8_encode($row_Recordset2['titre']); ?> </h1></td>
      <td ><a href="http://www.allocine.fr/series/ficheserie-<?php echo $row_Recordset1['id_allocine']; ?>/saisons/" target="_blank"><img src="allocine.png"/></a><br/>
      </br>
          <FORM METHOD="POST"ACTION="function/remplir_serie.php" >
          	id_allocine: <input type="text" name="id" id="id"/>
          	<input type="hidden" name="id_old" id="id_old" value="<?php echo $row_Recordset2['id']; ?>"/>
          
          </FORM>
          </br>
          <a href="modifier_fiche_serie.php?id=<?php echo $row_Recordset2['id']; ?>">Modifier manuellement</a>
          </td>
          
        </tr>
      
       
        <tr>
          
        <tr>
        <td rowspan="2">
      <p>Nombre de saisons: <?php echo $row_Recordset2['nombre_saisons']; ?></p>
      <p>Nombre d'épisodes: <?php echo $row_Recordset2['nombre_episodes']; ?> (durée: <?php echo $row_Recordset2['duree_episode']; ?>mn)</p>
      <p></p>
      <p>Liste des saisons:</p>
      <p>
      <ul>
      <?php
      for($i=1;$i<=$row_Recordset2['nombre_saisons'];$i++){?>
      <li>
      <a href="fiche_saison.php?id_serie=<?php echo $row_Recordset2['id']; ?>&num_saison=<?php echo $i; ?>">Saison <?php echo $i; ?></a>
      </li>
      <?php }?>
      </ul>
      </p></td>
          <td >Acteurs:<br/>
          	 <?php
          	$query_Recordset3 = "SELECT a.id_allocine, a.nom as nom,a.id as i FROM acteur as a,acteur_serie as l where a.id = l.id_acteur AND l.id_serie='".$row_Recordset2['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
				echo "<a href=\"fiche_acteur_allocine.php?id=".$row_Recordset3['id_allocine']."\">".utf8_encode($row_Recordset3['nom'])."</a><br/>";
			}
          
			?></td>
        </tr>
        
            <tr>
            
      <td width="450"><?php echo utf8_encode(html_entity_decode($row_Recordset1['synopsis'])); ?></td>
      </tr>
      
      </table>
     
  </div>    
  </div>
  <div id="contenu_saison">
  <table>
  <?php
  $nb=count($tab);
  for($i=0;$i<$nb-1;$i+=2) {?>
  		<tr><td colspan="2"><?php echo utf8_encode($tab[$i]['titre']); ?></td>
  		
  		<td colspan="2"><?php echo utf8_encode($tab[$i+1]['titre']); ?></td></tr>
  		
  		<tr><td style="text-align: justify; vertical-align: top; padding-top: 5px;"><?php echo utf8_encode($tab[$i]['synopsis']); ?></td>
  		<td style="text-align: center; width: 200px;vertical-align: top; ">
  		<?php 
  		
  		$liens=array();
  		$query_Recordset1 = "SELECT * FROM liens where id_episode='".$tab[$i]['id']."' ";
  		$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
		while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
			$liens[]=$row_Recordset1;		
		}
          for($li=0;$li<count($liens);$li++){?>  
          
          
          <a href="#" onclick="lancer_film('<?php echo $liens[$li]['lien']; ?>')"><input type="button" value="<?php echo $liens[$li]['nom']; ?>"></a> 
          <?php }
  			
  			?>

  		<br/><a href="episode_update.php?id=<?php echo $tab[$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
  		<a href="episode_update.php?id=<?php echo $tab[$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
  		</td>
  				
  		<td style="text-align: justify; vertical-align: top; "><?php echo utf8_encode($tab[$i+1]['synopsis']); ?></td>
  		<td style="text-align: center; width: 200px;vertical-align: top; padding-top: 20px;">
  		<?php 
  		
  		$liens=array();
  		$query_Recordset1 = "SELECT * FROM liens where id_episode='".$tab[$i+1]['id']."' ";
  		$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
		while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
			$liens[]=$row_Recordset1;		
		}
          for($li=0;$li<count($liens);$li++){?>  
          
          
          <a href="#" onclick="lancer_film('<?php echo $liens[$li]['lien']; ?>')"><input type="button" value="<?php echo $liens[$li]['nom']; ?>"></a> 
          <?php }
  			
  			?>
  			  		<br/><a href="episode_update.php?id=<?php echo $tab[$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
  		<a href="episode_update.php?id=<?php echo $tab[$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
  		</td>
  		</tr>
  		
  		<tr><td colspan="5" id="spacer">&nbsp;</td></tr>
			
				
			<?php } 
			if(isset($tab[$i])){?>
			<tr><td colspan="2"><?php echo utf8_encode($tab[$i]['titre']); ?></td>
  		<td width="20"></td><td colspan="2"></td></tr>
  		
  		<tr><td style="text-align: justify; vertical-align: top; padding-top: 5px;"><?php echo utf8_encode($tab[$i]['synopsis']); ?></td>
  		<td style="text-align: center; width: 200px;vertical-align: top; ">
  		<?php 
  		
  		$liens=array();
  		$query_Recordset1 = "SELECT * FROM liens where id_episode='".$tab[$i]['id']."' ";
  		$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
		while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
			$liens[]=$row_Recordset1;		
		}
          for($li=0;$li<count($liens);$li++){?>  
          
          
          <a href="#" onclick="lancer_film('<?php echo $liens[$li]['lien']; ?>')"><input type="button" value="<?php echo $liens[$li]['nom']; ?>"></a> 
          <?php }
  			
  			?>
  		<br/><a href="episode_update.php?id=<?php echo $tab[$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
  		<a href="episode_update.php?id=<?php echo $tab[$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
  		</td>  		
  		<td></td><td style="text-align: center; width: 200px;"></td>
  		</tr>
  		
  					
			<?php }?>
			</table>
	</div>		
</div>

</body>
</html>
