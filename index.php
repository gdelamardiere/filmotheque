<?php 
require_once('classes/factory.class.php');
$f=factory::load("film");
$g=factory::load("genre");
$a=factory::load("acteurs");
$liste_films=$f->list_film($_GET);
$liste_genres=$g->list_genre_film();
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
<?php  for($i=0;$i<count($liste_films);$i++){echo "x[".$liste_films[$i]['id_film']."] = '".$liste_films[$i]['fanart']."';\n";} ?>
	var temp=<?php echo $liste_films[0]['id_film'];?>;	
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
foreach($liste_films as $value){?>
	<tr name="entete_<?php echo $value['id_film'];?>" id="entete_<?php echo $value['id_film'];?>" style="display:<?php if($j==0){echo"table-row"; }else{echo "none";}?>"><td colspan="2">
<input type="button" value="Fanart(www.themoviedb.org):" onclick="maj_fanart('<?php echo $j; ?>','<?php echo $value['id_film']; ?>')"/> <input type="text" name="new_fanart_<?php echo $j; ?>" id="new_fanart_<?php echo $j; ?>"/>
</td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Film"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a>
<br><a href="http://localhost/filmotheque/film_affichage_liste.php"><input type="button" value="Affichage liste"></a></h3></td>
<td >
          	<input type="button" value="id_allocine:" onclick="modifier_film_id('<?php echo $j; ?>','<?php echo $value['id_film']; ?>')"/> <input type="text" name="new_id_allocine_<?php echo $j; ?>" id="new_id_allocine_<?php echo $j; ?>"/>
          	<input type="hidden" name="id_old" id="id_old" value="<?php echo $value['id_film']; ?>"/>
          
          

</td>
<td >
          <a href="modifier_fiche_film.php?id=<?php echo $value['id_film']; ?>">Modifier manuellement</a></td>
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
      foreach($liste_films as  $val){ ?>
      
      	<div id="content_left_<?php echo $val['id_film'];?>" style="display:<?php if($j==0){echo"inline"; }else{echo "none";}?>"/>
      	
		 <h1><?php echo utf8_encode($val['titre']); ?> <?php if($val['titre']!=$val['titre_original']){echo "(".$val['titre_original'].")";}?> </h1>
		 <p style="float:left; margin-right:100px;" id="synopsis" ><?php echo utf8_encode(html_entity_decode($val['synopsis'])); ?></p>
		 <p>
         <b>Date: </b><?php echo $val['annee_production']; ?><br/>
         <b>Duree:</b> <?php echo $val['duree']; ?><br/>
          <b>Genres: </b>
          
          <?php
			$genres=$g->genre_film($val['id_film']);
			foreach($genres as $genre){
				echo utf8_encode($genre['nom'])."&nbsp;";
			}
			?>
          
          
          
          
          <br/>
          <b>Réalisateur: </b>
          	 <?php
             $realisateurs=$a->list_realisateur_film($val['id_film']);
        			foreach($realisateurs as $realisateur){
        			
        				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$realisateur['id_acteur'].".html\">".utf8_encode($realisateur['nom'])."</a><br/>";
        			}
          
			?>
			Interet: 
			<?php if($val['interet']==''){ echo "non défini";
					for($k=1;$k<=5;$k++){?>
						<a href="#" onclick="voter_interet('<?php echo $val['id_film']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $val['id_film'].'_'.$k; ?>" src="images/etoile_vide.png"/></a>
					<?php }
      			}	
      			else{
      				for($k=1;$k<=$val['interet'];$k++){?>
						<a href="#" onclick="voter_interet('<?php echo $val['id_film']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $val['id_film'].'_'.$k; ?>" src="images/etoile_pleine.png"/></a>
					<?php }
					for($k=$val['interet']+1;$k<=5;$k++){?>
						<a href="#" onclick="voter_interet('<?php echo $val['id_film']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $val['id_film'].'_'.$k; ?>" src="images/etoile_vide.png"/></a>
					<?php }
      			}?>
						
						
						 </p>
      <table border="0" cellspacing="10" cellpadding="0" style="width: 75%;">
       
        
        <tr>
          <td colspan="2">Acteurs:<br/>
          	 <?php
              $acteurs=$a->list_acteur_film($val['id_film']);
              foreach($acteurs as $acteur){
              
                echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$acteur['id_acteur'].".html\">".utf8_encode($acteur['nom'])."(".utf8_encode($acteur['role']).")</a><br/>";
              }
          
			?></td>
			
			<td colspan="2"></td>
			<td>
      <p>
      
          <?php $liens=factory::load("liens")->liste_lien_film($val['id_film']);
         foreach($liens as $lien){?>  
          
          
          <a href="#" title="<?php echo $lien['lien']; ?>" onclick="lancer_film('<?php echo $lien['lien']; ?>')"><?php echo $lien['nom'];?></a> 
          &nbsp;&nbsp;<a href="#" onclick="afficher('<?php echo $lien['lien']; ?>')">Afficher </a> 
          &nbsp;&nbsp;<a href="#" onclick="supprimer_lien('<?php echo $lien['lien']; ?>')">Supprimer </a> 
          &nbsp;&nbsp;<select STYLE="width:100px" name="select_qualite_<?php echo $val['id_film']; ?>" id="select_qualite_<?php echo $val['id_film']; ?>" size="1" onchange="voter_qualite('<?php echo $lien['id_liens'];?>',this);">
  <option value="" <?php if($lien['qualite']==""){?>selected='selected'<?php }?>></option>
  <option value="TS" <?php if($lien['qualite']=="TS"){?>selected='selected'<?php }?>>TS</option>
  <option value="Moyenne" <?php if($lien['qualite']=="Moyenne"){?>selected='selected'<?php }?>>Moyenne</option>
  <option value="Bonne" <?php if($lien['qualite']=="Bonne"){?>selected='selected'<?php }?>>Bonne</option>
  <option value="HD" <?php if($lien['qualite']=="HD"){?>selected='selected'<?php }?>>HD</option>
  
  
  
  </select>
          <br/>
          <?php }?>
          <br/><a href="#" onclick="suppr(<?php echo $val['id_film']; ?>)">Delete</a></p>
</td>
		<td>
        <a href="http://www.allocine.fr/film/fichefilm_gen_cfilm=<?php echo $val['id_film']; ?>.html" target="_blank"><img src="images/allocine.png"/></a> <br/>
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
	
      foreach($liste_genres as  $val){ 
  echo "<option value=\"".$val['id_genre']."\">".str_replace(":","",utf8_encode($val['nom']))."(".$val['c'].")</option>";
  
      }?>
  
  
  </select><br/>

<select name="select_principale" id="select_principale" size="<?php if(count($liste_films)<20){echo count($liste_films);} else{echo "20";}?>" onchange="change(this);">
  <?php 
	$i=0;
      foreach($liste_films as  $val){ 
  echo "<option value=\"".$val['id_film']."\"";
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
