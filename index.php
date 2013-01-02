<?php 



require_once('function/base.php');
mysql_select_db($database_base, $base);

include_once('classe_allocine/Filmotheque.class.php');



$f=new Filmotheque();
	
	$query_Recordset2 = "SELECT * FROM film order by date_crea,titre";
	//$query_Recordset2 = "SELECT * FROM  film WHERE  fanart LIKE  '0'";
	$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	
	if(isset($_GET['titre'])){
		$query_Recordset2 = "SELECT * FROM film where titre LIKE '%".$_GET['titre']."%' or titre LIKE '%".utf8_decode($_GET['titre'])."%' or titre_original LIKE '%".$_GET['titre']."%' or titre_original LIKE '%".utf8_decode($_GET['titre'])."%'";
		echo $query_Recordset2;
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());		
	}
	
	if(isset($_GET['doublon'])){
		$query_Recordset2 = "SELECT * FROM film where id in(SELECT id_film FROM liens group by id_film  having count(id_film)>=2)";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());		
	}
	
	if(isset($_GET['acteur'])){
		$query_Recordset2 = "SELECT distinct f.*  FROM film f,acteur_film af, acteur a where a.id=af.id_acteur AND af.id_film=f.id AND a.nom LIKE '%".$_GET['acteur']."%' order by f.titre";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());		
	}

	
	$tab=array();
	while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
		$tab[]=$row_Recordset2;
	//	$f->maj_fanart_film($row_Recordset2['id'],"");
		
	}
	
	$tab_genre=array();
	$query_Recordset4 = "SELECT nom,g.id, count(id_film) as c FROM `genre_film` as l,genre as g WHERE id_genre=g.id group by g.id order by c desc, g.id";
	$Recordset4 = mysql_query($query_Recordset4, $base) or die(mysql_error());
	while($row_Recordset4 = mysql_fetch_assoc($Recordset4)){
		$tab_genre[]=$row_Recordset4;
		
	}

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Accueil</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="fx.slide.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/mootools-1.2-core-yc.js"></script>
<SCRIPT language="JavaScript">
var x = new Array();
<?php  for($i=0;$i<count($tab);$i++){echo "x[".$tab[$i]['id']."] = '".$tab[$i]['fanart']."';\n";} ?>
	var temp=<?php echo $tab[0]['id'];?>;	
</SCRIPT>
<script type="text/javascript" src="js/common.js"></script>
   	


</head>

<body id="body" onload="chargement();" >
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
			<div class="loginClose"><a href="#" id="closeLogin" onclick="fermer();">Close Panel</a></div>			<table style="width:100%" ><tr><td><input type="button" value="Importer un répertoire:" onclick="rep_film();"><input type="texte" value="" id="rep"></td>
<td><h1>Menu Principal</h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur_film();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr>
<?php $j=0;
foreach($tab as $value){?>
	<tr name="entete_<?php echo $value['id'];?>" id="entete_<?php echo $value['id'];?>" style="display:<?php if($j==0){echo"table-row"; }else{echo "none";}?>"><td colspan="2">
<input type="button" value="Fanart(www.themoviedb.org):" onclick="maj_fanart('<?php echo $j; ?>','<?php echo $value['id']; ?>')"/> <input type="text" name="new_fanart_<?php echo $j; ?>" id="new_fanart_<?php echo $j; ?>"/>
</td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Film"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a>
<br><a href="http://localhost/filmotheque/film_affichage_liste.php"><input type="button" value="Affichage liste"></a></h3></td>
<td >
          	<input type="button" value="id_allocine:" onclick="modifier_film_id('<?php echo $j; ?>','<?php echo $value['id']; ?>')"/> <input type="text" name="new_id_allocine_<?php echo $j; ?>" id="new_id_allocine_<?php echo $j; ?>"/>
          	<input type="hidden" name="id_old" id="id_old" value="<?php echo $value['id']; ?>"/>
          
          

</td>
<td >
          <a href="modifier_fiche_film.php?id=<?php echo $value['id']; ?>">Modifier manuellement</a></td>
</tr>

<?php $j++;}?>



</table>

			
			
			
		</div>
		
		
	</div>
	
	
	
	
<div id="conteneur" >
    
	<div id="principale3" style="margin-top:150px;">
      <div id="sous_principale3"></div>
      
      <table id="table_principale" style="width:95%; height:auto;">
<tr>
<td style="width:85%">
	<?php
	$j=0;
      foreach($tab as  $val){ ?>
      
      	<div id="content_left_<?php echo $val['id'];?>" style="display:<?php if($j==0){echo"inline"; }else{echo "none";}?>"/>
      	
		 <h1><?php echo utf8_encode($val['titre']); ?> <?php if($val['titre']!=$val['titre_original']){echo "(".$val['titre_original'].")";}?> </h1>
		 <p style="float:left; margin-right:100px;" id="synopsis" ><?php echo utf8_encode(html_entity_decode($val['synopsis'])); ?></p>
		 <p>
         <b>Date: </b><?php echo $val['annee_production']; ?><br/>
         <b>Duree:</b> <?php echo $val['duree']; ?><br/>
          <b>Genres: </b>
          
          <?php
          	$query_Recordset3 = "SELECT nom,g.id FROM genre as g,genre_film where g.id = genre_film.id_genre AND genre_film.id_film='".$val['id']."'";
			$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
				echo utf8_encode($row_Recordset3['nom'])."&nbsp;";				
				
			}
			?>
          
          
          
          
          <br/>
          <b>Réalisateur: </b>
          	 <?php
          	$query_Recordset3 = "SELECT a.nom as nom,a.id_allocine as i FROM acteur as a,realisateur_film as l where a.id = l.id_realisateur AND l.id_film='".$val['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$row_Recordset3['i'].".html\">".utf8_encode($row_Recordset3['nom'])."</a><br/>";
			}
          
			?>
			Interet: 
			<?php if($val['interet']==''){ echo "non défini";
					for($k=1;$k<=5;$k++){?>
						<a href="#" onclick="voter_interet('<?php echo $val['id']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $val['id'].'_'.$k; ?>" src="images/etoile_vide.png"/></a>
					<?php }
      			}	
      			else{
      				for($k=1;$k<=$val['interet'];$k++){?>
						<a href="#" onclick="voter_interet('<?php echo $val['id']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $val['id'].'_'.$k; ?>" src="images/etoile_pleine.png"/></a>
					<?php }
					for($k=$val['interet']+1;$k<=5;$k++){?>
						<a href="#" onclick="voter_interet('<?php echo $val['id']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $val['id'].'_'.$k; ?>" src="images/etoile_vide.png"/></a>
					<?php }
      			}?>
						
						
						 </p>
      <table border="0" cellspacing="10" cellpadding="0" style="width: 75%;">
       
        
        <tr>
          <td colspan="2">Acteurs:<br/>
          	 <?php
          	$query_Recordset3 = "SELECT a.id_allocine, a.nom as nom,a.id as i,l.role as role FROM acteur as a,acteur_film as l where a.id = l.id_acteur AND l.id_film='".$val['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$row_Recordset3['id_allocine'].".html\">".utf8_encode($row_Recordset3['nom'])."</a><br/>";
			}
          
			?></td>
			
			<td colspan="2"></td>
			<td>
      <p>
      
          <?php $liens=$f->lister_lien_film($val['id']);
         foreach($liens as $lien){?>  
          
          
          <a href="#" title="<?php echo $lien['lien']; ?>" onclick="lancer_film('<?php echo $lien['lien']; ?>')"><?php echo $lien['nom'];?></a> 
          &nbsp;&nbsp;<a href="#" onclick="afficher('<?php echo $lien['lien']; ?>')">Afficher </a> 
          &nbsp;&nbsp;<a href="#" onclick="supprimer_lien('<?php echo $lien['lien']; ?>')">Supprimer </a> 
          &nbsp;&nbsp;<select STYLE="width:100px" name="select_qualite_<?php echo $val['id']; ?>" id="select_qualite_<?php echo $val['id']; ?>" size="1" onchange="voter_qualite('<?php echo $lien['id'];?>',this);">
  <option value="" <?php if($lien['qualite']==""){?>selected='selected'<?php }?>></option>
  <option value="TS" <?php if($lien['qualite']=="TS"){?>selected='selected'<?php }?>>TS</option>
  <option value="Moyenne" <?php if($lien['qualite']=="Moyenne"){?>selected='selected'<?php }?>>Moyenne</option>
  <option value="Bonne" <?php if($lien['qualite']=="Bonne"){?>selected='selected'<?php }?>>Bonne</option>
  <option value="HD" <?php if($lien['qualite']=="HD"){?>selected='selected'<?php }?>>HD</option>
  
  
  
  </select>
          <br/>
          <?php }?>
          <br/><a href="#" onclick="suppr(<?php echo $val['id']; ?>)">Delete</a></p>
</td>
		<td>
        <a href="http://www.allocine.fr/film/fichefilm_gen_cfilm=<?php echo $val['id_allocine']; ?>.html" target="_blank"><img src="images/allocine.png"/></a> <br/>
        <?php if($val['bande_annonce']!=""){echo '<a href="'.$val['bande_annonce'].'" target="_blank"><img src="images/bande_annonce.png"/></a>';} ?>
        </td>
        </tr>
                
      </table>
      
      
     
             </div>
	<?php	$j++;
	}?>
	

  
  </td><td style="vertical-align:bottom;">
  <select name="select_genre" id="select_genre" size="1" onchange="change_genre(this);">
  <option value="0" selected='selected'>Tous les genres</option>
  <?php 
	
      foreach($tab_genre as  $val){ 
  echo "<option value=\"".$val['id']."\">".str_replace(":","",utf8_encode($val['nom']))."(".$val['c'].")</option>";
  
      }?>
  
  
  </select><br/>

<select name="select_principale" id="select_principale" size="<?php if(count($tab)<20){echo count($tab);} else{echo "20";}?>" onchange="change(this);">
  <?php 
	$i=0;
      foreach($tab as  $val){ 
  echo "<option value=\"".$val['id']."\"";
  if($i==0){echo 'selected="selected"';}
  echo ">".str_replace(":","",utf8_encode($val['titre']))."</option>";
  $i++;
      }?>
  
  
  </select>
  </td>
  
    
   
  
  </tr></table>
</div>


</body>
</html>
