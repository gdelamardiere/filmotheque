<?php

require_once('function/base.php');
mysql_select_db($database_base, $base);

if(isset($_GET['id'])){
	$query_Recordset1 = "SELECT * FROM film where id='".$_GET['id']."'";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
}

else{
	$query_Recordset1 = "SELECT * FROM film where titre LIKE '%".$_GET['titre']."%' LIMIT 1";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);

	
}



include_once('classe_allocine/Filmotheque.class.php');



$f=new Filmotheque();
	
	

	

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo utf8_encode($row_Recordset1['titre']); ?></title>
<link href="style2.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="js/common.js"></script>
</head>

<body>

<div id="conteneur">    
	<div id="image_fiche_film"><img src="<?php echo $row_Recordset1['poster']; ?>"/></div>
    <div id="principale">
      <h1>Titre original: <?php echo utf8_encode($row_Recordset1['titre_original']); ?> <br/>Titre vf: <?php echo utf8_encode($row_Recordset1['titre']); ?> </h1>
      <table border="0" cellspacing="10" cellpadding="0">
        <tr>
          <td>Date: <?php echo $row_Recordset1['annee_production']; ?></td>
          <td>Duree: <?php echo $row_Recordset1['duree']; ?></td>
          <td>Genres: 
          
          <?php
          	$query_Recordset3 = "SELECT nom,g.id FROM genre as g,genre_film where g.id = genre_film.id_genre AND genre_film.id_film='".$row_Recordset1['id']."'";
			$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
				echo "<a href=\"fiche_genre.php?id=".$row_Recordset3['id']."\">".utf8_encode($row_Recordset3['nom'])."</a>&nbsp;";				
				
			}
			?>
          
          
          
          
          </td>
        </tr>
        <tr>
          <td colspan="2"><a href="http://www.allocine.fr/film/fichefilm_gen_cfilm=<?php echo $row_Recordset1['id_allocine']; ?>.html" target="_blank"><img src="images/allocine.png"/></a><br/>
          <FORM METHOD="POST"ACTION="function/remplir_film.php" >
          	id_allocine: <input type="text" name="id" id="id"/>
          	<input type="hidden" name="id_old" id="id_old" value="<?php echo $row_Recordset1['id']; ?>"/>
          
          </FORM>
          </td>
          <td ><?php if($row_Recordset1['bande_annonce']!=""){echo '<a href="'.$row_Recordset1['bande_annonce'].'" target="_blank"><img src="images/bande_annonce.png"/></a>';} ?><br/>
          <a href="modifier_fiche_film.php?id=<?php echo $row_Recordset1['id']; ?>">Modifier manuellement</a></td>
        </tr>
        <tr>
          <td colspan="3">Acteurs:<br/>
          	 <?php
          	$query_Recordset3 = "SELECT a.id_allocine, a.nom as nom,a.id as i,l.role as role FROM acteur as a,acteur_film as l where a.id = l.id_acteur AND l.id_film='".$row_Recordset1['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
          	$row_Recordset3 = mysql_fetch_assoc($Recordset3);
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$row_Recordset3['id_allocine'].".html\">".utf8_encode($row_Recordset3['nom'])."</a>(".utf8_encode($row_Recordset3['role']).")<br/>";
			}
          
			?></td>
        </tr>
        <tr>
          <td colspan="3">Réalisateur:
          <?php
          	$query_Recordset3 = "SELECT a.nom as nom,a.id_allocine as i FROM acteur as a,realisateur_film as l where a.id = l.id_realisateur AND l.id_film='".$row_Recordset1['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$row_Recordset3['i'].".html\">".utf8_encode($row_Recordset3['nom'])."</a>&nbsp;";				
				
			}         
			?></td>
        </tr>
      </table>
      
      <p id="synopsis_1"><?php echo utf8_encode(html_entity_decode($row_Recordset1['synopsis'])); ?></p>
      <table width="100%" border="0" cellspacing="10" cellpadding="0">
        <tr>
          <td>
      <p>
      
          <?php $liens=$f->lister_lien_film($row_Recordset1['id']);
         foreach($liens as $lien){?>  
          
          
          <a href="#" title="<?php echo $lien['lien']; ?>" onclick="lancer_film('<?php echo $lien['lien']; ?>')"><?php echo $lien['nom'];?></a> 
          &nbsp;&nbsp;<a href="#" onclick="afficher('<?php echo $lien['lien']; ?>')">Afficher </a> 
          &nbsp;&nbsp;<a href="#" onclick="supprimer_lien('<?php echo $lien['lien']; ?>')">Supprimer </a> 
          &nbsp;&nbsp;<select STYLE="width:100px" name="select_qualite_<?php echo $val['id']; ?>" id="select_qualite_<?php echo $row_Recordset1['id']; ?>" size="1" onchange="voter_qualite('<?php echo $lien['id'];?>',this);">
  <option value="" <?php if($lien['qualite']==""){?>selected='selected'<?php }?>></option>
  <option value="TS" <?php if($lien['qualite']=="TS"){?>selected='selected'<?php }?>>TS</option>
  <option value="Moyenne" <?php if($lien['qualite']=="Moyenne"){?>selected='selected'<?php }?>>Moyenne</option>
  <option value="Bonne" <?php if($lien['qualite']=="Bonne"){?>selected='selected'<?php }?>>Bonne</option>
  <option value="HD" <?php if($lien['qualite']=="HD"){?>selected='selected'<?php }?>>HD</option>
  
  
  
  </select>
          <br/>
          <?php }?>
          <br/><a href="#" onclick="suppr(<?php echo $row_Recordset1['id']; ?>)">Delete</a></p>
</td>
		
          
        </tr>
      </table>
  </div>
</div>

</body>
</html>
